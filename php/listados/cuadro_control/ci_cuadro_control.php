<?php
class ci_cuadro_control extends gestionescuelas_ext_ci
{

    //---- filtro -----------------------------------------------------------------------

    public function conf__filtro($form)
    {
        //si no tengo nada seleccionado en el filtro, asigno x defecto el año activo
        if (!isset($this->s__datos_filtro)) {
            $anio = dao_consultas::get_anios(array('solo_activos'=>true));
            $this->s__datos_filtro['anio'] = $anio[0]['anio'];
        }
        return $this->s__datos_filtro;
    }

    public function evt__filtro__filtrar($datos)
    {
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
            $cuadro->set_datos(dao_consultas::get_datos_cuadro_control_mensual($this->s__datos_filtro));
        }
	}
}