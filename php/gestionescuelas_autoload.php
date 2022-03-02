<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class gestionescuelas_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'constantes' => 'constantes.php',
		'dao_consultas' => 'consultas/dao_consultas.php',
		'gestionescuelas_ci' => 'extension_toba/componentes/gestionescuelas_ci.php',
		'gestionescuelas_cn' => 'extension_toba/componentes/gestionescuelas_cn.php',
		'gestionescuelas_datos_relacion' => 'extension_toba/componentes/gestionescuelas_datos_relacion.php',
		'gestionescuelas_datos_tabla' => 'extension_toba/componentes/gestionescuelas_datos_tabla.php',
		'gestionescuelas_ei_arbol' => 'extension_toba/componentes/gestionescuelas_ei_arbol.php',
		'gestionescuelas_ei_archivos' => 'extension_toba/componentes/gestionescuelas_ei_archivos.php',
		'gestionescuelas_ei_calendario' => 'extension_toba/componentes/gestionescuelas_ei_calendario.php',
		'gestionescuelas_ei_codigo' => 'extension_toba/componentes/gestionescuelas_ei_codigo.php',
		'gestionescuelas_ei_cuadro' => 'extension_toba/componentes/gestionescuelas_ei_cuadro.php',
		'gestionescuelas_ei_esquema' => 'extension_toba/componentes/gestionescuelas_ei_esquema.php',
		'gestionescuelas_ei_filtro' => 'extension_toba/componentes/gestionescuelas_ei_filtro.php',
		'gestionescuelas_ei_firma' => 'extension_toba/componentes/gestionescuelas_ei_firma.php',
		'gestionescuelas_ei_formulario' => 'extension_toba/componentes/gestionescuelas_ei_formulario.php',
		'gestionescuelas_ei_formulario_ml' => 'extension_toba/componentes/gestionescuelas_ei_formulario_ml.php',
		'gestionescuelas_ei_grafico' => 'extension_toba/componentes/gestionescuelas_ei_grafico.php',
		'gestionescuelas_ei_mapa' => 'extension_toba/componentes/gestionescuelas_ei_mapa.php',
		'gestionescuelas_servicio_web' => 'extension_toba/componentes/gestionescuelas_servicio_web.php',
		'gestionescuelas_comando' => 'extension_toba/gestionescuelas_comando.php',
		'gestionescuelas_modelo' => 'extension_toba/gestionescuelas_modelo.php',
		'gestionescuelas_autoload' => 'gestionescuelas_autoload.php',
		'gestionescuelas_ext_ci' => 'gestionescuelas_ext_ci.php',
		'ci_login' => 'login/ci_login.php',
		'cuadro_autologin' => 'login/cuadro_autologin.php',
		'pant_login' => 'login/pant_login.php',
		'ci_abm_personas' => 'personas/abm_personas/ci_abm_personas.php',
		'ci_abm_personas_interno' => 'personas/abm_personas/ci_abm_personas_interno.php',
		'cn_abm_personas' => 'personas/abm_personas/cn_abm_personas.php',
		'dao_personas' => 'personas/dao_personas.php',
		'ci_gestion_academica' => 'personas/gestion_academica/ci_gestion_academica.php',
		'cn_gestion_academica' => 'personas/gestion_academica/cn_gestion_academica.php',
		'persona' => 'personas/persona.php',
		'ci_vincular_allegados' => 'personas/vincular_allegados/ci_vincular_allegados.php',
		'cn_vincular_allegados' => 'personas/vincular_allegados/cn_vincular_allegados.php',
		'conversion_tipo_datos' => 'utiles/conversion_tipo_datos.php',
		'fecha' => 'utiles/fecha.php',
	);
}
?>