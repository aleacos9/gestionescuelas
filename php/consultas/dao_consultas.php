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

    public static function get_registros_a_procesar_alta_masiva_pagos($filtro = null)
    {
        $where = '';

        if (isset($filtro)) {
            if (isset($filtro['cuota'])) {
                $where .= " AND cuota = '{$filtro['cuota']}'";
            }
        }

        $sql = "SELECT ar.id_archivo_respuesta
                      ,ar.id_marca_tarjeta
                      ,ar.id_medio_pago
                      ,ar.nombre_archivo
                      ,ar.numero_establecimiento
                      ,ar.usuario_alta
                      ,ar.fecha_generacion
                      ,ar.cantidad_total_debitos
                      ,ar.importe_total_debitos
                      ,ar.cuota
                      ,ard.id_archivo_respuesta_detalle
                      ,ard.registro
                      ,ard.numero_codigo_banco_pagador
                      ,ard.numero_sucursal_banco_pagador
                      ,ard.numero_lote
                      ,ard.codigo_transaccion
                      ,ard.numero_establecimiento
                      ,ard.numero_tarjeta
                      ,ard.id_alumno_cc
                      ,ard.fecha_presentacion
                      ,ard.importe
                      ,ard.id_alumno
                      ,ard.codigo_alta_identificador
                      ,ard.cuenta_debito_fondos
                      ,ard.estado_movimiento
                      ,ard.rechazo1
                      ,ard.descripcion_rechazo1
                      ,ard.rechazo2
                      ,ard.descripcion_rechazo2
                      ,ard.numero_tarjeta_nueva
                      ,ard.fecha_devolucion_respuesta
                      ,ard.fecha_pago
                      ,ard.numero_cartera_cliente
                      ,ard.fecha_generacion
                      ,ard.contenido
                      ,ard.usuario_alta
                      ,ard.codigo_error_debito
                      ,ard.descripcion_error_debito
                      ,ard.fecha_origen_venc_debito
                FROM archivo_respuesta ar
                    INNER JOIN archivo_respuesta_detalle ard on ar.id_archivo_respuesta = ard.id_archivo_respuesta 
				WHERE 1=1 AND procesado = 0 
				    $where
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_datos_cuadro_control_mensual($filtro = null)
    {
        $sql = "SELECT /*(CASE WHEN substring(cuota, 1, 2) = '01' THEN 'ENERO'
                             WHEN substring(cuota, 1, 2) = '02' THEN 'FEBRERO'
                             WHEN substring(cuota, 1, 2) = '03' THEN 'MARZO'
                             WHEN substring(cuota, 1, 2) = '04' THEN 'ABRIL'
                             WHEN substring(cuota, 1, 2) = '05' THEN 'MAYO'
                             WHEN substring(cuota, 1, 2) = '06' THEN 'JUNIO'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'JULIO'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'AGOSTO'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'SEPTIEMBRE'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'OCTUBRE'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'NOVIEMBRE'
                             WHEN substring(cuota, 1, 2) = '07' THEN 'DICIEMBRE'
                        END) AS mes*/
                      'MARZO' AS mes  
                      ,COALESCE ((SELECT count(id_alumno_cc) as cargo_mensual_inscripcion
                                  FROM alumno_cuenta_corriente
                                  WHERE 1=1 AND substring(cuota, 1, 2) = '03'
                                    AND id_cargo_cuenta_corriente = 1), 0) AS cargo_mensual_inscripcion
                      ,COALESCE ((SELECT count(id_alumno_cc) as cargo_mensual_cuota
                                  FROM alumno_cuenta_corriente
                                  WHERE 1=1 AND substring(cuota, 1, 2) = '03'
                                    AND id_cargo_cuenta_corriente = 2), 0) AS cargo_mensual_cuota
                      ,COALESCE ((SELECT count(id_alumno_cc) as total_cargo_mensual
                                  FROM alumno_cuenta_corriente
                                  WHERE 1=1 AND substring(cuota, 1, 2) = '03'), 0) AS total_cargo_mensual
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_generadas
                                  FROM transaccion_cuenta_corriente trcc
                                       LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                       INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                       INNER JOIN persona p on p.id_persona = a.id_persona
                                       LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                              ,ultimo_cambio
                                                              ,e.id_estado_cuota
                                                              ,e.nombre AS estado_actual
                                                              ,tcc.id_motivo_rechazo
                                                              ,mr.nombre AS motivo_rechazo
                                                         FROM (SELECT id_alumno_cc
                                                                     ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                               FROM transaccion_cuenta_corriente tcc
                                                               GROUP BY id_alumno_cc) AS estado_actual
                                                                   INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                   INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                   LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo = mr.id_motivo_rechazo
                                                                   LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                          ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                  AND b.id_estado_cuota IN (1)), 0) AS cuotas_impagas_generadas
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_liquidadas
                                   FROM transaccion_cuenta_corriente trcc
                                            LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                            INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                            INNER JOIN persona p on p.id_persona = a.id_persona
                                            LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                  ,ultimo_cambio
                                                                  ,e.id_estado_cuota
                                                                  ,e.nombre AS estado_actual
                                                                  ,tcc.id_motivo_rechazo
                                                                  ,mr.nombre AS motivo_rechazo
                                                             FROM (SELECT id_alumno_cc
                                                                        ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                   FROM transaccion_cuenta_corriente tcc
                                                                   GROUP BY id_alumno_cc) AS estado_actual
                                                                      INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                      INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                      LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo = mr.id_motivo_rechazo
                                                                      LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                             ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                   WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                     AND b.id_estado_cuota IN (2)), 0) AS cuotas_impagas_liquidadas
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_rechazadas
                                  FROM transaccion_cuenta_corriente trcc
                                           LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                           INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                           INNER JOIN persona p on p.id_persona = a.id_persona
                                           LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                 ,ultimo_cambio
                                                                 ,e.id_estado_cuota
                                                                 ,e.nombre AS estado_actual
                                                                 ,tcc.id_motivo_rechazo
                                                                 ,mr.nombre AS motivo_rechazo
                                                            FROM (SELECT id_alumno_cc
                                                                       ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                  FROM transaccion_cuenta_corriente tcc
                                                                  GROUP BY id_alumno_cc) AS estado_actual
                                                                     INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                     INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                     LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo = mr.id_motivo_rechazo
                                                                     LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                            ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                  WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                    AND b.id_estado_cuota IN (4)), 0) AS cuotas_impagas_rechazadas
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS total_cuotas_impagas
                                 FROM transaccion_cuenta_corriente trcc
                                          LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                          INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                          INNER JOIN persona p on p.id_persona = a.id_persona
                                          LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                ,ultimo_cambio
                                                                ,e.id_estado_cuota
                                                                ,e.nombre AS estado_actual
                                                                ,tcc.id_motivo_rechazo
                                                                ,mr.nombre AS motivo_rechazo
                                                           FROM (SELECT id_alumno_cc
                                                                      ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                 FROM transaccion_cuenta_corriente tcc
                                                                 GROUP BY id_alumno_cc) AS estado_actual
                                                                    INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                    INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                    LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo = mr.id_motivo_rechazo
                                                                    LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                           ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                 WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                   AND b.id_estado_cuota IN (1, 2, 4)), 0) AS total_cuotas_impagas
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_transferencia
                                  FROM transaccion_cuenta_corriente trcc
                                           LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                           INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                           INNER JOIN persona p on p.id_persona = a.id_persona
                                           LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                  ,ultimo_cambio
                                                                  ,e.id_estado_cuota
                                                                  ,e.nombre AS estado_actual
                                                                  ,tcc.id_medio_pago
                                                                  ,mp.nombre AS medio_pago
                                                                  ,tcc.id_marca_tarjeta
                                                                  ,mt.nombre AS marca_tarjeta
                                                                  ,tcc.numero_comprobante
                                                                  ,tcc.numero_lote
                                                                  ,tcc.numero_autorizacion
                                                             FROM (SELECT id_alumno_cc
                                                                        ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                   FROM transaccion_cuenta_corriente tcc
                                                                   GROUP BY id_alumno_cc) AS estado_actual
                                                                      INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                      INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                      LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                      INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                      INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                             ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                   WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                     AND b.id_estado_cuota = 3 --PAGAS
                                     AND b.id_medio_pago = 2), 0) AS cantidad_pagadores_transferencia
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_debito
                                  FROM transaccion_cuenta_corriente trcc
                                            LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                            INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                            INNER JOIN persona p on p.id_persona = a.id_persona
                                            LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                  ,ultimo_cambio
                                                                  ,e.id_estado_cuota
                                                                  ,e.nombre AS estado_actual
                                                                  ,tcc.id_medio_pago
                                                                  ,mp.nombre AS medio_pago
                                                                  ,tcc.id_marca_tarjeta
                                                                  ,mt.nombre AS marca_tarjeta
                                                                  ,tcc.numero_comprobante
                                                                  ,tcc.numero_lote
                                                                  ,tcc.numero_autorizacion
                                                             FROM (SELECT id_alumno_cc
                                                                        ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                   FROM transaccion_cuenta_corriente tcc
                                                                   GROUP BY id_alumno_cc) AS estado_actual
                                                                      INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                      INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                      LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                      INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                      INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                             ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                   WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                     AND b.id_estado_cuota = 3 --PAGAS
                                     AND b.id_medio_pago = 3), 0) AS cantidad_pagadores_debito
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_credito
                                  FROM transaccion_cuenta_corriente trcc
                                             LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                             INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                             INNER JOIN persona p on p.id_persona = a.id_persona
                                             LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                   ,ultimo_cambio
                                                                   ,e.id_estado_cuota
                                                                   ,e.nombre AS estado_actual
                                                                   ,tcc.id_medio_pago
                                                                   ,mp.nombre AS medio_pago
                                                                   ,tcc.id_marca_tarjeta
                                                                   ,mt.nombre AS marca_tarjeta
                                                                   ,tcc.numero_comprobante
                                                                   ,tcc.numero_lote
                                                                   ,tcc.numero_autorizacion
                                                              FROM (SELECT id_alumno_cc
                                                                         ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                    FROM transaccion_cuenta_corriente tcc
                                                                    GROUP BY id_alumno_cc) AS estado_actual
                                                                       INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                       INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                       LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                       INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                       INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                              ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                    WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                      AND b.id_estado_cuota = 3 --PAGAS
                                      AND b.id_medio_pago = 4), 0) AS cantidad_pagadores_credito
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_posnet
                                  FROM transaccion_cuenta_corriente trcc
                                             LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                             INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                             INNER JOIN persona p on p.id_persona = a.id_persona
                                             LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                   ,ultimo_cambio
                                                                   ,e.id_estado_cuota
                                                                   ,e.nombre AS estado_actual
                                                                   ,tcc.id_medio_pago
                                                                   ,mp.nombre AS medio_pago
                                                                   ,tcc.id_marca_tarjeta
                                                                   ,mt.nombre AS marca_tarjeta
                                                                   ,tcc.numero_comprobante
                                                                   ,tcc.numero_lote
                                                                   ,tcc.numero_autorizacion
                                                              FROM (SELECT id_alumno_cc
                                                                         ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                    FROM transaccion_cuenta_corriente tcc
                                                                    GROUP BY id_alumno_cc) AS estado_actual
                                                                       INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                       INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                       LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                       INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                       INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                              ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                    WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                      AND b.id_estado_cuota = 3 --PAGAS
                                      AND b.id_medio_pago = 5), 0) AS cantidad_pagadores_posnet
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_deposito
                                  FROM transaccion_cuenta_corriente trcc
                                             LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                             INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                             INNER JOIN persona p on p.id_persona = a.id_persona
                                             LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                   ,ultimo_cambio
                                                                   ,e.id_estado_cuota
                                                                   ,e.nombre AS estado_actual
                                                                   ,tcc.id_medio_pago
                                                                   ,mp.nombre AS medio_pago
                                                                   ,tcc.id_marca_tarjeta
                                                                   ,mt.nombre AS marca_tarjeta
                                                                   ,tcc.numero_comprobante
                                                                   ,tcc.numero_lote
                                                                   ,tcc.numero_autorizacion
                                                              FROM (SELECT id_alumno_cc
                                                                         ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                    FROM transaccion_cuenta_corriente tcc
                                                                    GROUP BY id_alumno_cc) AS estado_actual
                                                                       INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                       INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                       LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                       INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                       INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                              ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                    WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                      AND b.id_estado_cuota = 3 --PAGAS
                                      AND b.id_medio_pago = 6), 0) AS cantidad_pagadores_deposito
                      ,COALESCE ((SELECT count(DISTINCT(b.id_alumno_cc)) AS total_cuotas_pagas
                                  FROM transaccion_cuenta_corriente trcc
                                             LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                             INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                             INNER JOIN persona p on p.id_persona = a.id_persona
                                             LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                   ,ultimo_cambio
                                                                   ,e.id_estado_cuota
                                                                   ,e.nombre AS estado_actual
                                                                   ,tcc.id_medio_pago
                                                                   ,mp.nombre AS medio_pago
                                                                   ,tcc.id_marca_tarjeta
                                                                   ,mt.nombre AS marca_tarjeta
                                                                   ,tcc.numero_comprobante
                                                                   ,tcc.numero_lote
                                                                   ,tcc.numero_autorizacion
                                                              FROM (SELECT id_alumno_cc
                                                                         ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                    FROM transaccion_cuenta_corriente tcc
                                                                    GROUP BY id_alumno_cc) AS estado_actual
                                                                       INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                       INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                       LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                       INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                       INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                              ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                    WHERE 1=1 AND substring(acc.cuota, 1, 2) = '03'
                                      AND b.id_estado_cuota = 3), 0) AS total_cuotas_pagas
                FROM alumno_cuenta_corriente
                WHERE 1 = 1
                GROUP BY 1
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_detalle_pagadores_por_periodo($filtro = null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_mes'])) {
                $where .= " AND substring(acc.cuota, 1, 2) = '{$filtro['id_mes']}'";
            }
        }

        $sql = "SELECT DISTINCT(b.id_alumno_cc)
                              ,p.id_persona
                              ,(p.apellidos || ', ' || p.nombres) as nombre_persona
                              ,b.ultimo_cambio
                              ,b.estado_actual
                              ,b.id_medio_pago
                              ,b.medio_pago
                              ,b.id_medio_pago
                              ,b.id_marca_tarjeta
                              ,b.marca_tarjeta
                              ,b.numero_comprobante
                              ,b.numero_lote
                              ,b.numero_autorizacion
                FROM transaccion_cuenta_corriente trcc
                         LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                         INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                         INNER JOIN persona p on p.id_persona = a.id_persona
                         LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                               ,ultimo_cambio
                                               ,e.id_estado_cuota
                                               ,e.nombre AS estado_actual
                                               ,tcc.id_medio_pago
                                               ,mp.nombre AS medio_pago
                                               ,tcc.id_marca_tarjeta
                                               ,mt.nombre AS marca_tarjeta
                                               ,tcc.numero_comprobante
                                               ,tcc.numero_lote
                                               ,tcc.numero_autorizacion
                                          FROM (SELECT id_alumno_cc
                                                     ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                FROM transaccion_cuenta_corriente tcc
                                                GROUP BY id_alumno_cc) AS estado_actual
                                             INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                             INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                             LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                             INNER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                             INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                          ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                $where
                  AND b.id_estado_cuota = 3 --solo las pagas
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }
}