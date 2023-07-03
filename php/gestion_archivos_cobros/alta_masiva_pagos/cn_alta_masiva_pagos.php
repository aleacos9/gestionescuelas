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
                    foreach ($registros_a_procesar as $pago) {
                        $persona = new persona($pago['id_alumno']);
                        $persona->set_modo($this->datos_pago['modo']);
                        $persona->set_datos_cuenta_corriente($pago);
                        $persona->set_usuario_ultima_modificacion($this->datos_pago['usuario_ultima_modificacion']);
                        $persona->grabar_pago_persona();
                        if (dao_consultas::catalogo_de_parametros("genera_comprobante_afip") == 'SI') {
                            $persona->generar_comprobante_afip();
                        }
                    }
                    toba::notificacion()->agregar('El procesamiento de los pagos fue realizada con éxito.', "info");
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

}