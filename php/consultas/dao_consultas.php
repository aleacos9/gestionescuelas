<?php
class dao_consultas
{
    /*
     * Retorna la lista de tipos de documentos dadas de alta
     */
    public static function get_tipos_documentos($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_tipo_documento'])) {
                $where .= " AND id_tipo_documento = '{$filtro['id_tipo_documento']}'";
            }
        }

        $sql = "SELECT id_tipo_documento
                      ,nombre
                      ,nombre_corto
                      ,jerarquia
                      ,extranjero
                      ,observaciones
				FROM tipo_documento
                $where
                ORDER BY jerarquia
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de sexos dados de alta
     */
    public static function get_sexo($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_sexo'])) {
                $where .= " AND id_sexo = '{$filtro['id_sexo']}'";
            }
        }

        $sql = "SELECT id_sexo
                      ,nombre
                      ,nombre_corto
				FROM sexo
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de localidades dadas de alta
     */
    public static function get_localidades($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_localidad'])) {
                $where .= " AND l.id_localidad = '{$filtro['id_localidad']}'";
            }

            if (isset($filtro['id_provincia'])) {
                $where .= " AND l.id_provincia = '{$filtro['id_provincia']}'";
            }
        }

        $sql = "SELECT l.id_localidad as id_localidad_nacimiento
                      ,l.id_localidad as id_localidad_residencia
                      ,l.nombre as localidad_nacimiento
                      ,l.nombre as localidad_residencia
                      ,l.nombre_corto
                      ,l.id_provincia
                      ,p.nombre_corto as provincia
                FROM localidad l
                    INNER JOIN provincia p on l.id_provincia = p.id_provincia
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de nacionalidades dadas de alta
     */
    public static function get_nacionalidades($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_nacionalidad'])) {
                $where .= " AND n.id_nacionalidad = '{$filtro['id_nacionalidad']}'";
            }
        }

        $sql = "SELECT n.id_nacionalidad
                      ,n.nombre
                      ,n.nombre_corto
                FROM nacionalidad n
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de nacionalidades dadas de alta
     */
    public static function get_motivos_desercion($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_motivo_desercion'])) {
                $where .= " AND id_motivo_desercion = '{$filtro['id_motivo_desercion']}'";
            }
        }

        $sql = "SELECT id_motivo_desercion
                      ,nombre
                      ,nombre_corto
                      ,observaciones
                FROM motivo_desercion md
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de ocupaciones dadas de alta
     */
    public static function get_ocupaciones($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_ocupacion'])) {
                $where .= " AND id_ocupacion = '{$filtro['id_ocupacion']}'";
            }
        }

        $sql = "SELECT id_ocupacion
                      ,nombre
                      ,nombre_corto
                      ,observaciones
				FROM ocupacion
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de tipos de allegados dados de alta
     */
    public static function get_tipos_allegados($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_tipo_allegado'])) {
                $where .= " AND id_tipo_allegado = '{$filtro['id_tipo_allegado']}'";
            }
        }

        $sql = "SELECT id_tipo_allegado
                      ,nombre
                      ,nombre_corto
                      ,observaciones
                      ,jerarquia  
				FROM tipo_allegado
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de estudios alcanzados dados de alta
     */
    public static function get_estudios_alcanzados($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_estudio_alcanzado'])) {
                $where .= " AND id_estudio_alcanzado = '{$filtro['id_estudio_alcanzado']}'";
            }
        }

        $sql = "SELECT id_estudio_alcanzado
                      ,nombre
                      ,nombre_corto
                      ,observaciones
				FROM estudio_alcanzado
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de medios de pagos dados de alta
     */
    public static function get_medios_pagos($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_medio_pago'])) {
                $where .= " AND id_medio_pago = '{$filtro['id_medio_pago']}'";
            }
        }

        $sql = "SELECT id_medio_pago
                      ,nombre
                      ,nombre_corto
                      ,observaciones
                      ,jerarquia
				FROM medio_pago
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de marcas de tarjetas dadas de alta
     */
    public static function get_marcas_tarjetas($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_marca_tarjeta'])) {
                $where .= " AND id_marca_tarjeta = '{$filtro['id_marca_tarjeta']}'";
            }
        }

        $sql = "SELECT id_marca_tarjeta
                      ,nombre
                      ,nombre_corto
                      ,observaciones
                      ,jerarquia
				FROM marca_tarjeta
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de entidades bancarias dadas de alta
     */
    public static function get_entidades_bancarias($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_entidad_bancaria'])) {
                $where .= " AND id_entidad_bancaria = '{$filtro['id_entidad_bancaria']}'";
            }
        }

        $sql = "SELECT id_entidad_bancaria
                      ,nombre
                      ,nombre_corto
                      ,observaciones
				FROM entidad_bancaria
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de grados dados de alta
     */
    public static function get_grados($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_grado'])) {
                $where .= " AND id_grado = '{$filtro['id_grado']}'";
            }
        }

        $sql = "SELECT g.id_grado
                      ,g.nombre as grado
                      ,(g.nombre || ' - ' || n.nombre) as grado_completo
                      ,g.nombre_corto
                      ,g.observaciones
                      ,g.id_nivel
                      ,n.nombre as nivel
				FROM grado g
				    INNER JOIN nivel n on n.id_nivel = g.id_nivel
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna el nombre y apellido de las personas
     */
    public static function get_nombres_persona($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND id_persona = '{$filtro['id_persona']}'";
            }
        }

        $sql = "SELECT id_persona
                      ,nombres
                      ,apellidos
                      ,(apellidos || ', ' || nombres) as nombre_completo
				FROM persona
                $where
                    AND es_alumno = 'N'
                ORDER BY apellidos, nombres
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }
}