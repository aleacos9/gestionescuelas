#!/usr/bin/env php
<?php

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
