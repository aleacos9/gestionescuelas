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

            if (isset($filtro['se_muestra_alta_manual'])) {
                if ($filtro['se_muestra_alta_manual'] == 'S') {
                    $where .= " AND se_muestra_alta_manual = 'S'";
                } else {
                    $where .= " AND se_muestra_alta_manual = 'N'";
                }
            }
        }

        $sql = "SELECT id_medio_pago
                      ,nombre
                      ,nombre_corto
                      ,se_muestra_alta_manual
                      ,observaciones
                      ,jerarquia
				FROM medio_pago
                $where
                ORDER BY jerarquia
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

            if (isset($filtro['permite_posnet'])) {
                if ($filtro['permite_posnet'] == 'S') {
                    $where .= " AND permite_posnet = 'S'";
                } else {
                    $where .= " AND permite_posnet = 'N'";
                }
            }
        }

        $sql = "SELECT id_marca_tarjeta
                      ,nombre
                      ,nombre_corto
                      ,permite_posnet  
                      ,observaciones
                      ,jerarquia
				FROM marca_tarjeta
                $where
                ORDER BY jerarquia
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
        $select = $from = '';
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND p.id_persona = '{$filtro['id_persona']}'";
            }

            if (isset($filtro['solo_alumnos'])) {
                if ($filtro['solo_alumnos'] == 0) {
                    $where .= " AND p.es_alumno = 'N'";
                }
                if ($filtro['solo_alumnos'] == 1) {
                    if (isset($filtro['id_alumno'])) {
                        $where .= " AND a.id_alumno = '{$filtro['id_alumno']}'";
                    }

                    $select .= ",a.id_alumno";
                    $from .= "LEFT OUTER JOIN alumno a on a.id_persona = p.id_persona";
                    $where .= " AND p.es_alumno = 'S' 
                                AND p.activo != 'B'
                              ";
                }
            }
        }

        $sql = "SELECT p.id_persona
                      ,p.nombres
                      ,p.apellidos
                      ,(p.apellidos || ', ' || p.nombres) as nombre_completo
                      $select  
				FROM persona p
				    $from
                $where
                ORDER BY p.apellidos, p.nombres
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de cargos en la cuenta corriente dadas de alta
     */
    public static function get_cargos_cuenta_corriente($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_cargo_cuenta_corriente'])) {
                $where .= " AND id_cargo_cuenta_corriente = '{$filtro['id_cargo_cuenta_corriente']}'";
            }
        }

        $sql = "SELECT id_cargo_cuenta_corriente
                      ,nombre
                      ,nombre_corto
                      ,observaciones
				FROM cargo_cuenta_corriente
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna la lista de años dados de alta
     */
    public static function get_anios($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_anio'])) {
                $where .= " AND id_anio = '{$filtro['id_anio']}'";
            }
        }

        $sql = "SELECT id_anio
                      ,anio
				FROM anio
                $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public function get_meses_del_anio()
    {
        $meses[0]['id'] = 1;
        $meses[0]['mes'] = "Enero";
        $meses[1]['id'] = 2;
        $meses[1]['mes'] = "Febrero";
        $meses[2]['id'] = 3;
        $meses[2]['mes'] = "Marzo";
        $meses[3]['id'] = 4;
        $meses[3]['mes'] = "Abril";
        $meses[4]['id'] = 5;
        $meses[4]['mes'] = "Mayo";
        $meses[5]['id'] = 6;
        $meses[5]['mes'] = "Junio";
        $meses[6]['id'] = 7;
        $meses[6]['mes'] = "Julio";
        $meses[7]['id'] = 8;
        $meses[7]['mes'] = "Agosto";
        $meses[8]['id'] = 9;
        $meses[8]['mes'] = "Septiembre";
        $meses[9]['id'] = 10;
        $meses[9]['mes'] = "Octubre";
        $meses[10]['id'] = 11;
        $meses[10]['mes'] = "Noviembre";
        $meses[11]['id'] = 12;
        $meses[11]['mes'] = "Diciembre";

        return $meses;
    }

    public static function catalogo_de_parametros($id, $fuente_datos = null)
    {
        if ($fuente_datos) {
            $base = $fuente_datos;
        } else {
            $base = 'gestionescuelas';
        }

        $where = 'WHERE 1=1';
        $par_personalizados = toba::db($base)->consultar("SELECT parametro, valor FROM parametros_sistema $where");

        if ($par_personalizados) {
            foreach (array_keys($par_personalizados) as $key) {
                $clave = $par_personalizados[$key]["parametro"];
                $valor = $par_personalizados[$key]["valor"];
                $par_personalizados[$clave] = $valor;
                unset($par_personalizados[$key]);
            }
        }

        if (isset($par_personalizados[$id]) && !is_null($par_personalizados[$id])) {
            return $par_personalizados[$id];	/* el 'parametro=valor' no existe en la tabla de parametros personalizados*/
        } else {
            throw new toba_error("Se solicito un PARAMETRO inexistente o su valor no está establecido: '$id'");
        }
    }

    /*
     * Retorna los datos de un cargo generado
     */
    public static function get_datos_cargo_generado($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_alumno_cc'])) {
                $where .= " AND id_alumno_cc = '{$filtro['id_alumno_cc']}'";
            }
        }

        $sql = "SELECT id_transaccion_cc
                      ,id_alumno_cc
                      ,to_char(fecha_transaccion,'YYYY-MM-dd') AS fecha_transaccion
                      ,id_estado_cuota
                      ,(COALESCE(importe, 0)) AS importe
                      ,fecha_pago
                      ,fecha_respuesta_prisma
                      ,id_motivo_rechazo
                      ,usuario_ultima_modificacion
                      ,fecha_ultima_modificacion
                      ,numero_comprobante
                      ,numero_lote
                      ,numero_autorizacion
                      ,id_medio_pago
                      ,id_marca_tarjeta
				FROM transaccion_cuenta_corriente 
				$where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }
}