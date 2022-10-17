<?php
class cn_alta_manual_pagos extends gestionescuelas_cn
{
    protected $datos_pago;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';

        if ($this->datos_pago) {
            if ($this->validar() == 'ok') {
                $persona = array();

                if (isset($this->datos_pago[0])) {
                    foreach ($this->datos_pago as $pago) {
                        $persona = new persona($pago['id_alumno']);
                        break;
                    }
                } else {
                    $persona = new persona($this->datos_pago['id_alumno']);
                }
                $persona->set_modo($this->datos_pago['modo']);
                $persona->set_datos_cuenta_corriente($this->datos_pago);
                $persona->grabar_pago_persona();
                //$persona->generar_comprobante_afip();
                return $ok;
            }
        } else {
            throw new toba_error('Para procesar debe ingresar datos.');
        }

    }

    public function validar()
    {
        //Obtengo los datos del cargo a pagar
        $datos_cargo_generado = dao_consultas::get_datos_cargo_generado($this->datos_pago);
        if (isset($datos_cargo_generado)) {
            //si el parámetro permite_pagos_parciales está desactivao => valido que el importe del cargo sea igual al ingresado
            if (dao_consultas::catalogo_de_parametros("permite_pagos_parciales") == 'NO') {
                if ( $datos_cargo_generado[0]['importe'] != ($this->datos_pago['importe'] * -1) ) {
                    throw new toba_error("El importe ingresado es incorrecto, debe ser: $" .$datos_cargo_generado[0]['importe']);
                }
            }

            //valido que la fecha de pago ingresada sea mayor o igual a la fecha del cargo generado
            if ($datos_cargo_generado[0]['fecha_transaccion'] > $this->datos_pago['fecha_pago']) {
                //formateo la fecha
                $fecha = new fecha($datos_cargo_generado[0]['fecha_transaccion']);
                $fecha_pantalla = $fecha->get_fecha_pantalla();
                throw new toba_error("La fecha del pago debe ser mayor o igual que: " .$fecha_pantalla);
            }
        }

        //valido que la fecha de pago no sea mayor a la fecha actual
        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        if (isset($this->datos_pago)) {
            if (isset($this->datos_pago['fecha_pago'])) {
                if ($this->datos_pago['fecha_pago'] > $hoy) {
                    throw new toba_error("La fecha del pago no puede ser mayor a la fecha actual.");
                }
            }
        }
        return 'ok';
    }

    public function set_datos_pago($datos)
    {
        $this->datos_pago = $datos;
    }
}