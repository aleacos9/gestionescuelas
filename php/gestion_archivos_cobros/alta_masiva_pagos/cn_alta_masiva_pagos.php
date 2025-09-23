<?php
class cn_alta_masiva_pagos extends gestionescuelas_cn
{
    protected $datos_pago;

    function evt__procesar_especifico()
    {
        $ok = 'ok';
        if ($this->datos_pago) {
            if ($this->validar() == 'ok') {
                //Obtengo todos los registros de la tabla archivo_respuesta_detalle asociados a la cuota seleccionada
                $registros_a_procesar = $this->get_registros_a_procesar();

                if ($registros_a_procesar) {

                    $procesados_ok = 0;
                    $genera_afip = dao_consultas::catalogo_de_parametros("genera_comprobante_afip");

                    foreach ($registros_a_procesar as $pago) {
                        //Obtengo el primer y el último dia del mes que está pagando la cuota para agregarlo en el comprobante AFIP
                        if (isset($pago['cuota'])) {
                            $mes = substr($pago['cuota'], 0, 2); // Obtener los dos primeros dígitos
                            $anio = substr($pago['cuota'], 2, 4); // Obtener los cuatro últimos dígitos
                            $pago['fecha_primer_dia_mes_pago'] = fecha::primer_dia_mes($mes, $anio);
                            $pago['fecha_ultimo_dia_mes_pago'] = fecha::ultimo_dia_mes($mes, $anio);
                        }
                        $persona = new persona($pago['id_alumno']);
                        //Obtengo el tutor de cada alumno, ya que necesito el identificador para hacer el comprobante AFIP
                        $persona->set_documento_tutor(dao_consultas::get_documento_tutor(array('id_persona'=>$pago['id_persona'],'solo_identificacion_tutor'=>true))); //$persona->get_documento_tutor()
                        $persona->set_modo($this->datos_pago['modo']);
                        $persona->set_datos_cuenta_corriente($pago);
                        $persona->set_usuario_ultima_modificacion($this->datos_pago['usuario_ultima_modificacion']);
                        $persona->grabar_pago_persona();

                        if (($pago['id_medio_pago'] == 4 && isset($pago['rechazo1']) && $pago['rechazo1'] == '00') ||
                            ($pago['id_medio_pago'] == 3 && trim($pago['descripcion_error_debito']) == 'NUL')) {
                            if ($genera_afip == 'SI') {
                                $persona->generar_comprobante_afip();
                                $persona->actualizar_datos_comprobante_generado();
                                $procesados_ok++;
                            } else {
                                $procesados_ok++;
                            }
                        }
                    }

                    $id_archivo_respuesta = $registros_a_procesar[0]['id_archivo_respuesta'];
                    $this->actualizar_cantidad_procesados($id_archivo_respuesta, $procesados_ok);

                    if (dao_consultas::catalogo_de_parametros("genera_comprobante_afip") == 'SI') {
                        toba::notificacion()->agregar('El procesamiento de los pagos y la generación de los comprobantes AFIP fue realizada con éxito.', "info");
                     } else {
                        toba::notificacion()->agregar('El procesamiento de los pagos fue realizada con éxito.', "info");
                    }
                    // Notificación al usuario
                    toba::notificacion()->agregar("Se aplicaron efectivamente {$procesados_ok} pagos.",'info');
                    return $ok;
                } else {
                    throw new toba_error("La cuota y el año seleccionado no tiene pagos para procesar.");
                }
            }
        }
    }

    public function validar()
    {
        return 'ok';
    }

	public function set_datos_pago($datos)
    {
        $this->datos_pago = $datos;
    }

    /*
     * Obtiene todos los registros a procesar
     */
    public function get_registros_a_procesar()
    {
        return dao_consultas::get_registros_a_procesar_alta_masiva_pagos($this->datos_pago);
    }

    /**
     * Actualiza la cantidad de registros procesados correctamente
     * en la tabla archivo_respuesta.
     *
     * @param int $id_archivo_respuesta Identificador del archivo respuesta
     * @param int $procesados_ok Cantidad de registros procesados correctamente
     */
    private function actualizar_cantidad_procesados($id_archivo_respuesta, $procesados_ok)
    {
        $sql_update = "UPDATE archivo_respuesta
                       SET cantidad_procesados_ok = {$procesados_ok}
                       WHERE id_archivo_respuesta = {$id_archivo_respuesta}
                      ";

        toba::db()->ejecutar($sql_update);
    }

}