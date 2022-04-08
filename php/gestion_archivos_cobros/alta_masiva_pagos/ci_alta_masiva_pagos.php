<?php
class ci_alta_masiva_pagos extends ci_alta_manual_pagos
{
	protected $s__datos_alta_masiva_pagos;

    //---- formulario -------------------------------------------------------------------

	function conf__formulario($form)
	{
	}

	function evt__formulario__modificacion($datos)
	{
        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        $usuario = toba::usuario()->get_id();

	    if ($datos['cuota'] < 10) {
            $datos['cuota'] = '0'.$datos['cuota'];
        }
        $datos['cuota'] = $datos['cuota'].$datos['anio'];
        $datos['usuario_ultima_modificacion'] = $usuario;
        $datos['fecha_ultima_modificacion'] = $hoy;
	    $datos['fecha_transaccion'] = $hoy;
        $datos['mostrar_mensaje_individual'] = false;
	    $datos['modo'] = "alta_masiva";
        $this->s__datos_alta_masiva_pagos = $datos;
	}

    public function set_datos_cn()
    {
        $this->cn()->set_datos_pago($this->s__datos_alta_masiva_pagos);
    }
}