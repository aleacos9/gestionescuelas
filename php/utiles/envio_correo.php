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
    public static function generar_asunto_notificacion_cargo()
    {
        return 'Nuevo cargo generado - Gestion Escuelas';
    }

    public static function generar_cuerpo_notificacion_cargo($datos)
    {
        $alumno = htmlspecialchars($datos['alumno']);
        $tutor = htmlspecialchars($datos['tutor']);
        $cargo_nombre = htmlspecialchars($datos['cargo_nombre']);
        $descripcion = htmlspecialchars($datos['descripcion']);
        $importe = htmlspecialchars($datos['importe']);
        $fecha = htmlspecialchars($datos['fecha_generacion']);
        $periodo = htmlspecialchars($datos['periodo']);

        $nombre_institucion = htmlspecialchars($datos['nombre_institucion']);

        $detalle_deuda = '';
        if (!empty($datos['detalle_deuda'])) {
            $detalle_deuda = '
                <h3 style="color: #5bc0de; margin-top: 20px;">Detalle de Deuda Actual</h3>
                <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                    <thead>
                        <tr style="background-color: #5bc0de; color: #fff;">
                            <th style="padding: 8px; border: 1px solid #ddd;">Concepto</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Periodo</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Importe</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($datos['detalle_deuda'] as $deuda) {
                $detalle_deuda .= '
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['concepto']) . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['periodo']) . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">$ ' . htmlspecialchars($deuda['importe']) . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['estado']) . '</td>
                        </tr>';
            }
            $detalle_deuda .= '
                    </tbody>
                </table>';
        }

        $forma_pago = '';
        if (!empty($datos['forma_pago'])) {
            $forma_pago = '
                <h3 style="color: #5bc0de; margin-top: 20px;">Forma de Pago Registrada</h3>
                <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Medio de Pago:</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($datos['forma_pago']['medio_pago']) . '</td>
                    </tr>';
            if (!empty($datos['forma_pago']['marca_tarjeta'])) {
                $forma_pago .= '
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Marca:</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($datos['forma_pago']['marca_tarjeta']) . '</td>
                    </tr>';
            }
            if (!empty($datos['forma_pago']['entidad_bancaria'])) {
                $forma_pago .= '
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Banco/Entidad:</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($datos['forma_pago']['entidad_bancaria']) . '</td>
                    </tr>';
            }
            $forma_pago .= '
                </table>';
        }

        $mensaje = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #5bc0de; color: #fff; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
                    <h2 style="margin: 0;">Nuevo Cargo Generado</h2>
                </div>

                <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                    <p>Estimado/a <strong>' . $tutor . '</strong>,</p>
                    <p>Le informamos que se ha generado un nuevo cargo en la cuenta corriente del alumno/a <strong>' . $alumno . '</strong>.</p>

                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Tipo de Cargo:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $cargo_nombre . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Descripcion:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $descripcion . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Periodo:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $periodo . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Importe:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #d9534f;">$ ' . $importe . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Fecha de Generacion:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $fecha . '</td>
                        </tr>
                    </table>

                    ' . $detalle_deuda . '

                    ' . $forma_pago . '

                    <p style="margin-top: 20px;">Por favor, le solicitamos se regularice la situación a la brevedad. Si ya ha realizado el pago por otro medio, por favor envíe el comprobante por este medio o por correo para que podamos registrarlo correctamente, ya que en ocasiones se realiza el pago pero no recibimos la constancia necesaria.</p>
                    <p>Ante cualquier consulta, no dude en comunicarse con la institución.</p>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="font-size: 12px; color: #999; text-align: center;">' . $nombre_institucion . ' - Sistema de Gestion de Escuelas</p>
                </div>
            </div>
        ';

        return $mensaje;
    }


    public static function generar_asunto_notificacion_cargos_multiples()
    {
        return 'Nuevos cargos generados - Gestion Escuelas';
    }

    public static function generar_cuerpo_notificacion_cargos_multiples($datos)
    {
        $tutor = htmlspecialchars($datos['tutor']);
        $nombre_institucion = htmlspecialchars($datos['nombre_institucion']);

        $alumnos_html = '';
        foreach ($datos['alumnos'] as $al) {
            $alumno = htmlspecialchars($al['alumno']);
            $cargo_nombre = htmlspecialchars($al['cargo_nombre']);
            $descripcion = htmlspecialchars($al['descripcion']);
            $importe = htmlspecialchars($al['importe']);
            $fecha = htmlspecialchars($al['fecha_generacion']);
            $periodo = htmlspecialchars($al['periodo']);

            $detalle_deuda = '';
            if (!empty($al['detalle_deuda'])) {
                $detalle_deuda = '
                    <h4 style="color: #5bc0de;">Detalle de Deuda Actual</h4>
                    <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                        <thead>
                            <tr style="background-color: #5bc0de; color: #fff;">
                                <th style="padding: 8px; border: 1px solid #ddd;">Concepto</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Periodo</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Importe</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($al['detalle_deuda'] as $deuda) {
                    $detalle_deuda .= '
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['concepto']) . '</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['periodo']) . '</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">$ ' . htmlspecialchars($deuda['importe']) . '</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deuda['estado']) . '</td>
                            </tr>';
                }
                $detalle_deuda .= '
                        </tbody>
                    </table>';
            }

            $forma_pago = '';
            if (!empty($al['forma_pago'])) {
                $forma_pago = '
                    <h4 style="color: #5bc0de;">Forma de Pago Registrada</h4>
                    <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Medio de Pago:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($al['forma_pago']['medio_pago']) . '</td>
                        </tr>';
                if (!empty($al['forma_pago']['marca_tarjeta'])) {
                    $forma_pago .= '
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Marca:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($al['forma_pago']['marca_tarjeta']) . '</td>
                        </tr>';
                }
                if (!empty($al['forma_pago']['entidad_bancaria'])) {
                    $forma_pago .= '
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Banco/Entidad:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($al['forma_pago']['entidad_bancaria']) . '</td>
                        </tr>';
                }
                $forma_pago .= '
                    </table>';
            }

            $alumnos_html .= '
                    <div style="margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #fafafa;">
                        <h3 style="color: #555; margin-top: 0;">' . $alumno . '</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Tipo de Cargo:</strong></td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . $cargo_nombre . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Descripcion:</strong></td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . $descripcion . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Periodo:</strong></td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . $periodo . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Importe:</strong></td>
                                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #d9534f;">$ ' . $importe . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Fecha de Generacion:</strong></td>
                                <td style="padding: 8px; border: 1px solid #ddd;">' . $fecha . '</td>
                            </tr>
                        </table>
                        ' . $detalle_deuda . '
                        ' . $forma_pago . '
                    </div>';
        }

        $mensaje = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #5bc0de; color: #fff; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
                    <h2 style="margin: 0;">Nuevos Cargos Generados</h2>
                </div>

                <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                    <p>Estimado/a <strong>' . $tutor . '</strong>,</p>
                    <p>Se han generado los siguientes cargos en las cuentas corrientes de los alumnos a su cargo:</p>

                    ' . $alumnos_html . '

                    <p style="margin-top: 20px;">Por favor, le solicitamos se regularice la situación a la brevedad. Si ya ha realizado el pago por otro medio, por favor envíe el comprobante por este medio o por correo para que podamos registrarlo correctamente, ya que en ocasiones se realiza el pago pero no recibimos la constancia necesaria.</p>
                    <p>Ante cualquier consulta, no dude en comunicarse con la institución.</p>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="font-size: 12px; color: #999; text-align: center;">' . $nombre_institucion . ' - Sistema de Gestion de Escuelas</p>
                </div>
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
