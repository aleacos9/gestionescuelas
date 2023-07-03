<?php
class dt_parametros_sistema extends gestionescuelas_datos_tabla
{
    function get_listado($filtro=array())
    {
        $where = 'WHERE 1=1';

        if (isset($filtro['parametro'])) {
            $where = $where . " AND parametro ILIKE ".quote("%{$filtro['parametro']}%");
        }
        if (isset($filtro['descripcion'])) {
            $where = $where . " AND descripcion ILIKE ".quote("%{$filtro['descripcion']}%");
        }
        if (isset($filtro['desc_corta'])) {
            $where = $where . " AND desc_corta ILIKE ".quote("%{$filtro['desc_corta']}%");
        }
        if (isset($filtro['valor'])) {
            $where = $where . " AND valor = {$filtro['valor']}";
        }
        if (isset($filtro['version_publicacion'])) {
            $where = $where . " AND version_publicacion = '{$filtro['version_publicacion']}'";
        }

        $sql = "SELECT parametro
					  ,descripcion
					  ,desc_corta
					  ,valor
					  ,version_publicacion
					  ,id_parametro
				FROM parametros_sistema
				$where
				ORDER BY descripcion
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        if (isset($filtro['aplica_perfil_datos'])){
            if ($filtro['aplica_perfil_datos'] == TRUE)
                $sql = toba::perfil_de_datos()->filtrar($sql);
        }
        return toba::db()->consultar($sql);
    }

    function get_descripciones()
    {
        $sql = "SELECT parametro
					  ,descripcion 
				FROM parametros_sistema 
				ORDER BY descripcion
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }
}