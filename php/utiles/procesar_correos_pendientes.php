#!/usr/bin/env php
<?php
/**
 * Envia los correos encolados en correo_pendiente.
 *
 * La generacion de cargos ya no manda los correos en el momento —con 137 tutores
 * se iba en timeout— sino que los deja encolados. Este script los saca.
 *
 * Para correrlo a mano:
 *
 *     php php/utiles/procesar_correos_pendientes.php
 *
 * Como se agenda en el cron: ver el bloque que sigue al cierre de este
 * comentario. Va aparte porque una linea de crontab lleva barra-asterisco y eso
 * cerraria este bloque.
 */

// ---------------------------------------------------------------------------
// COMO SE AGENDA — crontab del servidor, no del repo
// ---------------------------------------------------------------------------
//
//   # Envia los avisos encolados al generar cargos.
//   */5 * * * * TOBA_DIR=/data/local/sistema TOBA_INSTANCIA=produccion TOBA_PROYECTO=gestionescuelas php /data/local/sistema/proyectos/gestionescuelas/php/utiles/procesar_correos_pendientes.php >> /var/log/gestionescuelas/correos.log 2>&1
//
// Por que cada 5 minutos y no cada 15: el lote es de 20 correos por corrida y una
// generacion de cargos encola mas de 130. A 5 minutos la cola se vacia en poco
// mas de media hora; a 15 tardaria casi dos.
//
// Las tres variables ya tienen valor por defecto abajo (/data/local/sistema,
// desarrollo, gestionescuelas). Se pasan explicitas para que la linea del
// crontab deje asentado contra que instancia corre: es el dato que despues nadie
// encuentra.
//
// Ajustar la ruta si TOBA_DIR no es /data/local/sistema. El proyecto siempre
// cuelga de $TOBA_DIR/proyectos/$TOBA_PROYECTO.

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

$pendientes = toba::db()->consultar(
    "SELECT * FROM correo_pendiente
     WHERE procesado = 'NO' AND intentos < 3
     ORDER BY fecha_alta ASC
     LIMIT 20"
);

if (empty($pendientes)) {
    toba::logger()->info('[correo_pendiente] No hay correos pendientes para procesar.');
    return;
}

$procesados = 0;
$fallidos = 0;

foreach ($pendientes as $p) {
    try {
        $mail = new envio_correo($p['email_destino']);
        $mail->set_asunto($p['asunto']);
        $mail->set_cuerpo($p['cuerpo']);
        $mail->enviar();

        toba::db()->consultar(
            "UPDATE correo_pendiente
             SET procesado = 'SI', fecha_procesado = CURRENT_TIMESTAMP
             WHERE id_correo_pendiente = " . (int)$p['id_correo_pendiente']
        );

        toba::logger()->info("[OK] Correo enviado a {$p['email_destino']} (id={$p['id_correo_pendiente']})");
        $procesados++;
    } catch (Exception $e) {
        $error_msg = toba::db()->quote($e->getMessage());
        toba::db()->consultar(
            "UPDATE correo_pendiente
             SET intentos = intentos + 1,
                 ultimo_error = {$error_msg}
             WHERE id_correo_pendiente = " . (int)$p['id_correo_pendiente']
        );

        toba::logger()->info("[ERR] Fallo correo a {$p['email_destino']} (id={$p['id_correo_pendiente']}): {$e->getMessage()}");
        $fallidos++;
    }
}

toba::logger()->info("[correo_pendiente] Procesados: {$procesados}, Fallidos: {$fallidos}");
toba::logger()->guardar();
