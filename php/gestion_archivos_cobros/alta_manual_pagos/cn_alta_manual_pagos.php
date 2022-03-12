<?php
class cn_alta_manual_pagos extends gestionescuelas_cn
{
    protected $datos_pago;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';
        $this->validar();
        $persona = array();

        ei_arbol($this->datos_alta_manual_pago, 'desde el cn');

        /*if (isset($this->datos_pago['id_transaccion_cc'])) {
            $persona = new persona($this->datos_pago['id_transaccion_cc']);
        } else {
            $persona = new persona();
        }*/

        foreach ($this->datos_pago as $pago) {
            $persona = new persona($pago['id_alumno']);
            break;
        }
        $persona->set_allegados($this->datos_alta_manual_pago);
        $persona->grabar_persona_allegados();
        return $ok;
    }

    public function validar()
    {
        //agregar todas las validaciones necesarias
    }

    public function set_datos_pago($datos)
    {
        $this->datos_pago = $datos;
    }


}