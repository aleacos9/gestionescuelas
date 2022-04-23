<?php
class ci_alta_manual_pagos extends ci_administrar_formas_cobro
{
    protected $s__datos_alta_manual_pago;
    protected $s__datos_alta_manual_pago_alta;
    protected $s__id_alumno_cc_seleccionado;

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
        if (!empty($this->s__persona_editar)) {
            $persona = new persona($this->s__persona_editar);
            $this->s__datos_alta_manual_pago = $persona->get_datos_cuenta_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    public function set_datos_cn()
    {
        if (isset($this->s__id_alumno_cc_seleccionado)) {
            $this->cn()->set_datos_pago($this->s__datos_alta_manual_pago_alta);
        }
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
        $form->set_solo_lectura();

	    if (isset($this->s__id_alumno_cc_seleccionado)) {
            /*
             * Busco los datos de la cuenta corriente en los datos que obtuve en la
             * function carga_datos (a través del id_alumno_cc que seleccioné en el cuadro)
             */
            $datos = array();
            if (isset($this->s__datos_alta_manual_pago)) {
                foreach ($this->s__datos_alta_manual_pago as $clave => $fila) {
                    if ($fila['id_alumno_cc'] == $this->s__id_alumno_cc_seleccionado['id_alumno_cc']) {
                        $datos = $fila;
                    }
                }
            }
            $form->set_solo_lectura(null, false);
            $form->set_datos($datos);
        }
	}

	function evt__formulario__modificacion($datos)
	{
        if (isset($this->s__id_alumno_cc_seleccionado)) {
            $usuario = toba::usuario()->get_id();

            $datos['id_alumno'] = $this->s__alumno_editar;
            $datos['id_alumno_cc'] = $this->s__id_alumno_cc_seleccionado['id_alumno_cc'];
            $datos['id_estado_cuota'] = 3; //por el momento le seteo x defecto el estado de pago de cuota Aprobado
            $datos['importe'] = $datos['importe'] * -1;
            $datos['usuario_ultima_modificacion'] = $usuario;
            $datos['mostrar_mensaje_individual'] = true;
            $datos['estado'] = 'nuevo';
            $datos['modo'] = "alta_individual";

            $this->s__datos_alta_manual_pago_alta = $datos;
        }
	}

    function evt__formulario__cancelar()
    {
        unset($this->s__id_alumno_cc_seleccionado);
    }

	//---- cuadro_cuenta_corriente ------------------------------------------------------

	function conf__cuadro_cuenta_corriente($cuadro)
	{
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }
	    if (!empty($this->s__datos_alta_manual_pago)) {
	        $cuadro->set_datos($this->s__datos_alta_manual_pago);
        }
	}

	function evt__cuadro_cuenta_corriente__seleccion($seleccion)
	{
        $this->s__id_alumno_cc_seleccionado = $seleccion;
	}

    function conf_evt__cuadro_cuenta_corriente__seleccion(toba_evento_usuario $evento, $fila)
    {
        if ($this->s__datos_alta_manual_pago[$fila]['importe'] < 0) {
            $evento->desactivar();
        } else {
            $evento->activar();
        }
    }
}
