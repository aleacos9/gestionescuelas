<?php
class cn_alta_manual_pagos extends gestionescuelas_cn
{
    protected $datos_pago;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';

        if ($this->datos_pago) {
            ei_arbol($this->datos_pago, 'desde el cn');
            $this->validar();
            $persona = array();

            //if (is_array($this->datos_pago)) {
            if (isset($this->datos_pago[0])) {
                foreach ($this->datos_pago as $pago) {
                    $persona = new persona($pago['id_alumno']);
                    break;
                }
            } else {
                $persona = new persona($this->datos_pago['id_alumno']);
            }
            $persona->set_datos_cuenta_corriente($this->datos_pago);
            $persona->grabar_pago_persona();
            return $ok;
        } else {
            throw new toba_error('Para procesar debe ingresar datos.');
        }

    }

    public function validar()
    {
        //Obtengo los datos del cargo a pagar
        $datos_cargo_generado = dao_consultas::get_datos_cargo_generado($this->datos_pago);
        //ei_arbol($datos_cargo_generado);
        if (isset($datos_cargo_generado)) {
            //valido que el importe del cargo sea igual al ingresado
            if ( $datos_cargo_generado[0]['importe'] != ($this->datos_pago['importe'] * -1) ) {
                throw new toba_error("El importe ingresado es incorrecto, debe ser: $" .$datos_cargo_generado[0]['importe']);
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
    }

    public function set_datos_pago($datos)
    {
        $this->datos_pago = $datos;
    }


}