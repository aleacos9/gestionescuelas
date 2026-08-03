#!/usr/bin/env php
<?php
/**
 * Emite los comprobantes AFIP que quedaron encolados en comprobante_pendiente.
 *
 * Para correrlo a mano:
 *
 *     php php/utiles/procesar_comprobantes_pendientes.php
 *
 * Como se agenda en el cron: ver el bloque que sigue al cierre de este
 * comentario. Va aparte porque una linea de crontab lleva barra-asterisco y eso
 * cerraria este bloque.
 *
 * Por que existe: hoy persona::grabar_pago_persona() graba el pago y despues
 * llama a AFIP por red, todo suelto. Si AFIP falla queda el pago sin
 * comprobante; si AFIP responde bien y falla el UPDATE posterior, AFIP emitio
 * una factura que la base no sabe que existe. Y mientras AFIP tarda, el
 * operador espera.
 *
 * Con la cola, quien registra el pago solo encola —en la misma transaccion que
 * el pago— y la emision se hace aca, con reintento y sin frenar a nadie.
 *
 * ---------------------------------------------------------------------------
 * POR QUE NO SE PARECE AL CRON DE CORREOS
 * ---------------------------------------------------------------------------
 * El cron de correos toma el lote entero dentro de una transaccion y la cierra
 * al final. Aca eso seria un error: emitir en AFIP NO SE PUEDE DESHACER. Si el
 * lote se abortara a mitad de camino, se revertirian los UPDATE de comprobantes
 * que AFIP ya emitio, y quedarian facturas reales que la base no registra.
 *
 * Por eso cada comprobante se maneja en tres pasos, cada uno con su propia
 * transaccion:
 *
 *   1. RECLAMAR  - se marca la fila como 'EN' (en curso) y se commitea. Desde
 *                  ese momento ninguna otra corrida la toma.
 *   2. EMITIR    - la llamada a AFIP se hace FUERA de toda transaccion.
 *   3. REGISTRAR - se guarda el resultado y se commitea.
 *
 * Si el proceso muere entre 2 y 3, la fila queda en 'EN'. Eso es a proposito:
 * NO se reintenta sola, porque AFIP pudo haber emitido la factura y reintentar
 * la emitiria dos veces. Una fila en 'EN' pide que alguien consulte en AFIP si
 * el comprobante salio y la cierre a mano. Es el unico estado que no se
 * resuelve automaticamente, y tiene que ser asi.
 *
 * Ademas se guarda el CAE y su vencimiento, que hoy no se guardan en ningun
 * lado: transaccion_cuenta_corriente solo tiene punto_venta, comprobante_tipo y
 * comprobante_numero. Por eso descargar_compobante.php le pide los datos a AFIP
 * cada vez que arma un PDF, y si AFIP esta caido no se puede reimprimir nada.
 */

// ---------------------------------------------------------------------------
// COMO SE AGENDA — crontab del servidor, no del repo
// ---------------------------------------------------------------------------
//
//   # Emite los comprobantes AFIP encolados. Cada 15 minutos alcanza: los pagos
//   # se encolan de a uno y el lote es de 20 por corrida.
//   */15 * * * * TOBA_DIR=/data/local/sistema TOBA_INSTANCIA=produccion TOBA_PROYECTO=gestionescuelas php /data/local/sistema/proyectos/gestionescuelas/php/utiles/procesar_comprobantes_pendientes.php >> /var/log/gestionescuelas/comprobantes.log 2>&1
//
// Las tres variables ya tienen valor por defecto adentro del script
// (/data/local/sistema, desarrollo, gestionescuelas). Se pasan explicitas para
// que la linea del crontab deje asentado contra que instancia corre: es el dato
// que despues nadie encuentra.
//
// Ajustar la ruta si TOBA_DIR no es /data/local/sistema. El proyecto siempre
// cuelga de $TOBA_DIR/proyectos/$TOBA_PROYECTO.
//
// Codigos de salida: 0 si salio todo bien, 1 si algo fallo o quedo trabado, para
// que el cron avise sin que nadie tenga que mirar el log.
//
// Es seguro que dos corridas se solapen: la segunda no toca lo que la primera ya
// reclamo. Ver el estado 'EN' mas abajo.

if (!isset($_SERVER['TOBA_DIR'])) {
    $_SERVER['TOBA_DIR'] = '/data/local/sistema';
}
if (!isset($_SERVER['TOBA_INSTANCIA'])) {
    $_SERVER['TOBA_INSTANCIA'] = 'desarrollo';
}
if (!isset($_SERVER['TOBA_PROYECTO'])) {
    $_SERVER['TOBA_PROYECTO'] = 'gestionescuelas';
}

$dir = $_SERVER['TOBA_DIR'] . '/php';
$separador = ':.:';
ini_set('include_path', ini_get('include_path') . $separador . $dir);
require_once('nucleo/toba_nucleo.php');

toba_nucleo::instancia()->iniciar_contexto_desde_consola(
    $_SERVER['TOBA_INSTANCIA'],
    $_SERVER['TOBA_PROYECTO']
);

define('MAX_INTENTOS', 3);
define('TAMANO_LOTE', 20);
define('TIPO_COMPROBANTE', 11); // Factura C

if (dao_consultas::catalogo_de_parametros('genera_comprobante_afip') != 'SI') {
    toba::logger()->info('[comprobante_pendiente] La emision de comprobantes esta desactivada por parametro.');
    toba::logger()->guardar();
    return;
}

$afip_wrapper = new Afip();
if (!$afip_wrapper->getAfip()) {
    toba::logger()->error('[comprobante_pendiente] AFIP no esta configurado. No se procesa nada.');
    toba::logger()->guardar();
    exit(1);
}

$factura_electronica = new \SIU\Afip\WebService\FacturaElectronica($afip_wrapper->getAfip());
$punto_venta = (int) dao_consultas::catalogo_de_parametros('punto_venta');

$emitidos = 0;
$fallidos = 0;
$agotados = 0;
$en_curso = 0;

//Si quedaron filas trabadas de una corrida anterior, se avisa y no se tocan.
$trabadas = toba::db()->consultar(
    "SELECT id_comprobante_pendiente, id_transaccion_cc
     FROM comprobante_pendiente
     WHERE procesado = 'EN'"
);

if (!empty($trabadas)) {
    foreach ($trabadas as $t) {
        toba::logger()->error(
            "[TRABADO] El comprobante id={$t['id_comprobante_pendiente']} (transaccion {$t['id_transaccion_cc']}) " .
            "quedo en curso en una corrida anterior. Hay que consultar en AFIP si el comprobante se emitio " .
            "antes de reintentarlo: reintentar a ciegas puede facturar dos veces."
        );
    }
    $en_curso = count($trabadas);
}

//Candidatos. El lote real se decide fila por fila al reclamarlas.
$candidatos = toba::db()->consultar(
    "SELECT id_comprobante_pendiente
     FROM comprobante_pendiente
     WHERE procesado = 'NO' AND intentos < " . MAX_INTENTOS . "
     ORDER BY fecha_alta ASC
     LIMIT " . TAMANO_LOTE
);

if (empty($candidatos)) {
    toba::logger()->info('[comprobante_pendiente] No hay comprobantes pendientes de emitir.');
    toba::logger()->guardar();
    exit($en_curso > 0 ? 1 : 0);
}

foreach ($candidatos as $candidato) {
    $id = (int) $candidato['id_comprobante_pendiente'];

    // ---- 1. RECLAMAR ------------------------------------------------------
    // El SKIP LOCKED evita esperar a otra corrida que ya la tenga; el estado
    // 'EN' commiteado evita que la tome despues.
    $p = null;

    toba::db()->abrir_transaccion();
    try {
        $filas = toba::db()->consultar(
            "SELECT id_comprobante_pendiente
                   ,id_transaccion_cc
                   ,identificador_tutor
                   ,importe
                   ,intentos
                   ,to_char(fecha_servicio_desde, 'YYYYMMDD') AS fecha_desde
                   ,to_char(fecha_servicio_hasta, 'YYYYMMDD') AS fecha_hasta
             FROM comprobante_pendiente
             WHERE id_comprobante_pendiente = " . $id . "
               AND procesado = 'NO'
               AND intentos < " . MAX_INTENTOS . "
             FOR UPDATE SKIP LOCKED"
        );

        if (empty($filas)) {
            //Otra corrida la tomo primero.
            toba::db()->cerrar_transaccion();
            continue;
        }

        $p = $filas[0];

        toba::db()->ejecutar(
            "UPDATE comprobante_pendiente
             SET procesado = 'EN', intentos = intentos + 1
             WHERE id_comprobante_pendiente = " . $id
        );

        toba::db()->cerrar_transaccion();
    } catch (Exception $e) {
        toba::db()->abortar_transaccion();
        toba::logger()->error("[ERR] No se pudo reclamar el comprobante id={$id}: " . $e->getMessage());
        $fallidos++;
        continue;
    }

    // ---- 2. EMITIR --------------------------------------------------------
    // Fuera de toda transaccion. Es la parte que no se puede deshacer.
    $comprobante = null;
    $error_emision = null;

    try {
        //Un cargo sin cuota —materiales— no trae periodo de servicio. Se usa el
        //mes corriente, que es lo que hace el origen.
        $fecha_desde = !empty($p['fecha_desde']) ? $p['fecha_desde'] : date('Ym') . '01';
        $fecha_hasta = !empty($p['fecha_hasta']) ? $p['fecha_hasta'] : date('Ymd', strtotime('last day of this month'));

        $importe = (float) $p['importe'];
        if ($importe <= 0) {
            throw new Exception('El importe a facturar tiene que ser mayor a cero.');
        }
        if (empty($p['identificador_tutor'])) {
            throw new Exception('El alumno no tiene tutor con documento: AFIP necesita el DocNro.');
        }

        $data = array(
            'CantReg'      => 1,
            'PtoVta'       => $punto_venta,
            'CbteTipo'     => TIPO_COMPROBANTE,
            'Concepto'     => 2,   // Servicios
            'DocTipo'      => 96,  // DNI
            'DocNro'       => $p['identificador_tutor'],
            'FchServDesde' => intval($fecha_desde),
            'FchServHasta' => intval($fecha_hasta),
            'FchVtoPago'   => intval(date('Ymd')),
            'CbteDesde'    => 1,
            'CbteHasta'    => 1,
            'CbteFch'      => intval(date('Ymd')),
            'ImpTotal'     => $importe,
            'ImpTotConc'   => 0,
            'ImpNeto'      => $importe,
            'ImpOpEx'      => 0,
            'ImpIVA'       => 0,
            'ImpTrib'      => 0,
            'MonId'        => 'PES',
            'MonCotiz'     => 1,
        );

        $comprobante = $factura_electronica->crearProximoComprobante($data);

        if (!isset($comprobante['voucher_number'])) {
            throw new Exception('AFIP no devolvio el numero de comprobante.');
        }
    } catch (Exception $e) {
        $error_emision = substr($e->getMessage(), 0, 2000);
    }

    // ---- 3. REGISTRAR -----------------------------------------------------
    if ($error_emision !== null) {
        //AFIP no emitio: la fila vuelve a 'NO' para que se reintente. El
        //intento ya se conto al reclamarla.
        toba::db()->abrir_transaccion();
        try {
            toba::db()->ejecutar(
                "UPDATE comprobante_pendiente
                 SET procesado = 'NO', ultimo_error = " . toba::db()->quote($error_emision) . "
                 WHERE id_comprobante_pendiente = " . $id
            );
            toba::db()->cerrar_transaccion();
        } catch (Exception $e) {
            toba::db()->abortar_transaccion();
            toba::logger()->error("[ERR] No se pudo registrar el fallo del comprobante id={$id}: " . $e->getMessage());
        }

        if (((int) $p['intentos'] + 1) >= MAX_INTENTOS) {
            $agotados++;
        }

        toba::logger()->error("[ERR] Fallo el comprobante de la transaccion {$p['id_transaccion_cc']} (id={$id}): {$error_emision}");
        $fallidos++;
        continue;
    }

    $numero = (int) $comprobante['voucher_number'];
    $cae = isset($comprobante['CAE']) ? $comprobante['CAE'] : null;
    $cae_vto = isset($comprobante['CAEFchVto']) ? $comprobante['CAEFchVto'] : null;

    toba::db()->abrir_transaccion();
    try {
        toba::db()->ejecutar(
            "UPDATE comprobante_pendiente
             SET procesado          = 'SI'
                ,fecha_procesado    = CURRENT_TIMESTAMP
                ,ultimo_error       = NULL
                ,punto_venta        = " . $punto_venta . "
                ,comprobante_tipo   = " . TIPO_COMPROBANTE . "
                ,comprobante_numero = " . $numero . "
                ,cae                = " . toba::db()->quote($cae) . "
                ,cae_vencimiento    = " . ($cae_vto ? toba::db()->quote($cae_vto) : 'NULL') . "
             WHERE id_comprobante_pendiente = " . $id
        );

        //Se sigue escribiendo en transaccion_cuenta_corriente para que el resto
        //del sistema —la cuenta corriente del tutor, el PDF— funcione igual.
        toba::db()->ejecutar(
            "UPDATE transaccion_cuenta_corriente
             SET punto_venta        = " . $punto_venta . "
                ,comprobante_tipo   = " . TIPO_COMPROBANTE . "
                ,comprobante_numero = " . $numero . "
             WHERE id_transaccion_cc = " . (int) $p['id_transaccion_cc']
        );

        toba::db()->cerrar_transaccion();

        toba::logger()->info("[OK] Comprobante {$punto_venta}-{$numero} emitido para la transaccion {$p['id_transaccion_cc']} (CAE {$cae})");
        $emitidos++;
    } catch (Exception $e) {
        toba::db()->abortar_transaccion();
        //AFIP YA EMITIO y no se pudo registrar. La fila queda en 'EN' y hay que
        //cerrarla a mano con los datos del comprobante que quedan en el log.
        toba::logger()->error(
            "[GRAVE] AFIP emitio el comprobante {$punto_venta}-{$numero} (CAE {$cae}) para la transaccion " .
            "{$p['id_transaccion_cc']} pero NO se pudo guardar: " . $e->getMessage() . ". " .
            "La fila id={$id} queda en curso y hay que cerrarla a mano con estos datos."
        );
        $fallidos++;
        $en_curso++;
    }
}

$resumen = "[comprobante_pendiente] Emitidos: {$emitidos}, fallidos: {$fallidos}";
if ($agotados > 0) {
    $resumen .= ", agotaron reintentos: {$agotados}";
}
if ($en_curso > 0) {
    $resumen .= ", TRABADOS en curso: {$en_curso} (revisar en AFIP antes de reintentar)";
}

toba::logger()->info($resumen);
toba::logger()->guardar();

exit(($fallidos > 0 || $en_curso > 0) ? 1 : 0);
