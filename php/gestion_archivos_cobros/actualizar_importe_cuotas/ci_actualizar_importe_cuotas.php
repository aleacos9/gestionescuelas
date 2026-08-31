<?php
class ci_actualizar_importe_cuotas extends gestionescuelas_ext_ci
{
    protected $s__importe_cuota_actual;
    protected $s__datos_formulario;

    function ini()
    {
        $this->s__importe_cuota_actual = dao_consultas::catalogo_de_parametros('importe_mensual_cuota');
    }

    //---- formulario -------------------------------------------------------------------

	function conf__filtro($form)
	{
        $valor = $this->s__importe_cuota_actual;
        $mensaje = <<<HTML
                    Tenga en cuenta que va a actualizar todas aquellas cuotas que cumplan con las siguientes condiciones:
                    <ul>
                      <li>Solo cuotas mensuales</li>
                      <li>Sin datos de pago cargados</li>
                      <li>En estado pendiente</li>
                      <li>Que no hayan sido abonadas parcial o totalmente</li>
                      <li>Cuotas ya vencidas (fecha anterior al primer dí­a del mes actual)</li>
                      <li>Que no hayan sido actualizadas en el mes vigente ni en el anterior</li>
                      <li>Solo de alumnos regulares, salvo que se tilde <em>Incluir alumnos no regulares</em></li>
                    </ul>
                    <p>El valor de cuota que se aplicará a cada cuota adeudada es <strong>\${$valor}</strong></p>
                    HTML;
        $form->ef('leyenda')->set_estado($mensaje);
        if (isset($this->s__datos_filtro)) {
            return $this->s__datos_filtro;
        }
	}

    public function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro['leyenda'] = null;
        $this->s__datos_filtro = $datos;
    }

    public function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $cuadro->set_datos(dao_consultas::get_listado_cuotas_para_actualizar_deuda($this->s__datos_filtro));
        }
    }

    //---- Eventos ----------------------------------------------------------------------

    public function evt__procesar()
    {
        $this->validar();
        $this->set_datos_cn();
        $this->cn()->procesar();
        $this->controlador()->disparar_limpieza_memoria();
    }

    public function validar()
    {
        //este método puede q no sea necesario, ya que puedo hacer todo desde el validar del cn
    }

    public function set_datos_cn()
    {
        $this->s__datos_filtro['leyenda'] = null;
        $this->cn()->set_datos_formulario($this->s__datos_filtro);
    }

}
