<?php
class ci_cuadro_control extends gestionescuelas_ci
{
	//---- cuadro -----------------------------------------------------------------------

	function conf__cuadro($cuadro)
	{
	    $cuadro->set_datos(dao_consultas::get_datos_cuadro_control_mensual());
	}
}