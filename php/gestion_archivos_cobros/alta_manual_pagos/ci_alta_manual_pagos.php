<?php
class ci_alta_manual_pagos extends ci_administrar_formas_cobro
{
    protected $s__datos_alta_manual_pago;

    //---- Funciones ---------------------------------------------------------------------

    /*
    * Retorna los medios de pagos que tienen el parámetro se_muestra_alta_manual en S
    */
    public function get_medios_pagos_ci($filtro = null)
    {
        $filtro['se_muestra_alta_manual'] = true;
        return parent::get_medios_pagos_ci($filtro);
    }

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_alta_manual_pago = $persona->get_datos_cuenta_corriente();
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_pago($this->s__datos_alta_manual_pago);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['solo_alumnos'] = true;
            $this->s__datos_filtro['administrar_forma_cobro'] = true;
            $this->s__datos_filtro['con_saldo_actual'] = true;
        }
        parent::conf__cuadro($cuadro);
    }

	//---- formulario -------------------------------------------------------------------

	function conf__formulario($form)
	{
        //$form->set_datos($this->s__datos_persona);
	}

	function evt__formulario__modificacion($datos)
	{
        $datos['id_alumno'] = $this->s__alumno_editar;
	    $this->s__datos_alta_manual_pago = $datos;
	}

	//---- cuadro_cuenta_corriente ------------------------------------------------------

	function conf__cuadro_cuenta_corriente($cuadro)
	{
        if (!empty($this->s__datos_alta_manual_pago)) {
            $cuadro->set_datos($this->s__datos_alta_manual_pago);
        }
	}

	function evt__cuadro_cuenta_corriente__seleccion($seleccion)
	{

	}

}
?>