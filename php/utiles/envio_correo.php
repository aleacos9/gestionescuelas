<?php
/**
 * Clase para manejar el envio de correos electronicos en el Sistema de Gestion de Escuelas.
 */
class envio_correo
{
    private $email;
    private $asunto;
    private $cuerpo;
    private $max_intentos = 3;  
    private $pausa = 2;         

    public function __construct($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El formato del correo electronico '$email' no es valido.");
        }
        $this->email = $email;
    }

    public function set_asunto($asunto)
    {
        $this->asunto = $asunto;
    }

    public function set_cuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;
    }

    public function enviar()
    {
        if (empty($this->asunto)) {
            throw new toba_error('El asunto del correo no puede estar vacio.');
        }
        if (empty($this->cuerpo)) {
            throw new toba_error('El cuerpo del correo no puede estar vacio.');
        }

        $intentos = 0;
        while ($intentos < $this->max_intentos) {
            try {
                $mail = new toba_mail($this->email, $this->asunto, $this->cuerpo);
                $mail->set_html(true);
                $mail->enviar();
                return true;
            } catch (Exception $e) {
                $intentos++;
                toba::logger()->info('Intento ' . $intentos . ' fallido al enviar el correo a ' . $this->email . ': ' . $e->getMessage());

                if ($intentos == $this->max_intentos) {
                    toba::logger()->error('Error al enviar el correo a ' . $this->email . ' tras ' . $this->max_intentos . ' intentos: ' . $e->getMessage());
                    throw new toba_error('No se pudo enviar el correo despues de varios intentos. Detalles: ' . $e->getMessage());
                }
                sleep($this->pausa);
            }
        }
    }

    // ---- Metodos especificos para Gestion Escuelas ----

    public static function generar_asunto_notificacion_rechazo()
    {
        return 'Notificacion de pago rechazado - Gestion Escuelas';
    }

    public static function generar_cuerpo_notificacion_rechazo($datos)
    {
        $alumno = htmlspecialchars($datos['alumno']);
        $cuota_raw = $datos['cuota']; // Viene como MMYYYY
        $error = htmlspecialchars($datos['descripcion_error_debito']);
        $tutor = htmlspecialchars($datos['tutor']);

        // Formateamos la cuota
        $meses = array(
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        );
        
        $mes_nro = substr($cuota_raw, 0, 2);
        $anio = substr($cuota_raw, 2);
        $mes_nombre = (isset($meses[$mes_nro])) ? $meses[$mes_nro] : 'Mes ' . $mes_nro;
        $cuota = "$mes_nombre / $anio";

        $mensaje = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <h2 style="color: #d9534f;">Aviso de Pago Rechazado</h2>
                <p>Estimado/a <strong>' . $tutor . '</strong>,</p>
                <p>Le informamos que el debito automatico correspondiente a la cuota del alumno/a <strong>' . $alumno . '</strong> ha sido rechazado por la entidad bancaria.</p>
                
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Cuota (Mes/Año):</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . $cuota . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Motivo del rechazo:</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd; color: #d9534f;">' . $error . '</td>
                    </tr>
                </table>

                <p>Por favor, le solicitamos se regularice la situacion a la brevedad para evitar recargos o inconvenientes en la cuenta corriente.</p>
                <p>Si ya ha realizado el pago por otro medio, por favor *envie el comprobante por este medio o por correo* para que podamos registrarlo correctamente, ya que en ocasiones se realiza el pago pero no recibimos la constancia necesaria.</p>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
            </div>
        ';

        return $mensaje;
    }

    public static function generar_mensaje_whatsapp_rechazo($datos)
    {
        $alumno = $datos['alumno'];
        $cuota_raw = $datos['cuota'];
        $error = $datos['descripcion_error_debito'];
        $tutor = $datos['tutor'];

        $meses = array(
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        );
        
        $mes_nro = substr($cuota_raw, 0, 2);
        $anio = substr($cuota_raw, 2);
        $mes_nombre = (isset($meses[$mes_nro])) ? $meses[$mes_nro] : 'Mes ' . $mes_nro;
        $cuota = "$mes_nombre / $anio";

        $mensaje = "*Aviso de Pago Rechazado*\n\n";
        $mensaje .= "Estimado/a *{$tutor}*\n\n";
        $mensaje .= "Le informamos que el debito automatico correspondiente a la cuota del alumno/a *{$alumno}* ha sido rechazado por la entidad bancaria.\n\n";
        $mensaje .= "*Detalle:*\n";
        $mensaje .= "- *Cuota (Mes/Año):* {$cuota}\n";
        $mensaje .= "- *Motivo:* {$error}\n\n";
        $mensaje .= "Por favor, le solicitamos se regularice la situacion a la brevedad. Si ya ha realizado el pago por otro medio, por favor *envie el comprobante por este medio o por correo* para que podamos registrarlo correctamente, ya que en ocasiones se realiza el pago pero no recibimos la constancia necesaria.";

        return $mensaje;
    }
}
?>
