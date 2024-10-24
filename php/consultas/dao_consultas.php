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
            if (isset($filtro['id'])) {
                $where .= " AND id_tipo_documento = '{$filtro['id']}'";
            }

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
     * Retorna la lista de niveles dados de alta
     */
    public static function get_niveles($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_nivel'])) {
                $where .= " AND id_nivel = '{$filtro['id_nivel']}'";
            }
            if (isset($filtro['excluir'])) {
                $where .= " AND id_nivel NOT IN ('{$filtro['excluir']}')";
            }
        }

        $sql = "SELECT id_nivel
                      ,nombre
                      ,nombre_corto
                      ,observaciones
                FROM nivel
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
            if (isset($filtro['nivel'])) {
                $where .= " AND g.id_nivel = '{$filtro['nivel']}'";
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
        $datos = toba::db()->consultar($sql);

        if (isset($datos)) {
            if (isset($filtro['solo_ids_grado'])) {
                if ($filtro['solo_ids_grado']) {
                    return dao_consultas::retornar_solo_ids_grado($datos);
                } else {
                    return $datos;
                }
            }
            return $datos;
        }
    }

    /*
     * Retorna los id_grado en un arreglo posicional
     */
    public static function retornar_solo_ids_grado($datos)
    {
        if (isset($datos)) {
            $grados = array();
            $cant = count($datos);
            for ($i = 0; $i < $cant; $i++) {			//Recorro los valores formando un arreglo posicional.
                $grados[] = $datos[$i]['id_grado'];
            }
            return $grados;
        }
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
                                AND p.activo NOT IN ('N', 'B')
                              ";
                }
            }

            if (isset($filtro['nro_documento'])) {
                /*$from .= "LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                            ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                   FROM tipo_documento X1
                                                                                       ,persona_tipo_documento X2
                                                                                   WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                     AND X2.id_persona = p.id_persona)
                         ";*/
                $where .= " AND ptd.numero = '{$filtro['nro_documento']}'";
            }

            if (isset($filtro['con_dni'])) {
                if ($filtro['con_dni'] == 1) {
                    $select .= ",(p.apellidos || ', ' || p.nombres || ' - ' || ptd.numero) as nombre_completo";
                } else {
                    $select .= ",(p.apellidos || ', ' || p.nombres) as nombre_completo";
                }
            }
        }

        $sql = "SELECT p.id_persona
                      ,p.nombres
                      ,p.apellidos
                      --,(p.apellidos || ', ' || p.nombres || ' - ' || ptd.numero) as nombre_completo
                      $select  
				FROM persona p
				    $from
				    LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                            ON ptd.id_persona = p.id_persona /*AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                   FROM tipo_documento X1
                                                                                       ,persona_tipo_documento X2
                                                                                   WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                        AND X2.id_persona = p.id_persona)*/
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
     * Retorna la lista de a�os dados de alta
     */
    public static function get_anios($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_anio'])) {
                $where .= " AND id_anio = '{$filtro['id_anio']}'";
            }
            if (isset($filtro['solo_activos'])) {
                if ($filtro['solo_activos'] == 'S') {
                    $where .= " AND estado = 'A'";
                }
            }
        }

        $sql = "SELECT id_anio
                      ,anio
                      ,estado 
				FROM anio
                $where
                ORDER BY anio
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_meses_del_anio()
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
            throw new toba_error("Se solicito un PARAMETRO inexistente o su valor no est� establecido: '$id'");
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
                      ,usuario_ultima_modificacion
                      ,fecha_ultima_modificacion
                      ,numero_comprobante
                      ,numero_lote
                      ,numero_autorizacion
                      ,id_medio_pago
                      ,id_marca_tarjeta
                      ,id_motivo_rechazo1                                            
                      ,id_motivo_rechazo2
                      ,codigo_error_debito
                      ,descripcion_error_debito
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
                $where .= " AND ar.cuota = '{$filtro['cuota']}'";
            }
            if (isset($filtro['id_alumno_cc'])) {
                $where .= " AND id_alumno_cc = '{$filtro['id_alumno_cc']}'";
            }
        }

        $sql = "SELECT a.id_alumno
                      ,p.id_persona
                      ,a.legajo
                      ,a.regular
                      ,r1.id_alumno_cc
                      ,max_fecha.UltimaFecha
                      ,r1.id_archivo_respuesta
                      ,r1.id_marca_tarjeta
                      ,r1.id_medio_pago
                      ,r1.nombre_archivo
                      ,r1.numero_establecimiento
                      ,r1.usuario_alta
                      ,r1.cantidad_total_debitos
                      ,r1.importe_total_debitos
                      ,r1.cuota
                      ,r1.id_archivo_respuesta_detalle
                      ,r1.registro
                      ,r1.numero_codigo_banco_pagador
                      ,r1.numero_sucursal_banco_pagador
                      ,r1.numero_lote
                      ,r1.codigo_transaccion
                      ,r1.numero_tarjeta
                      ,r1.fecha_presentacion
                      ,r1.importe
                      ,r1.id_alumno
                      ,r1.codigo_alta_identificador
                      ,r1.cuenta_debito_fondos
                      ,r1.estado_movimiento
                      ,r1.rechazo1
                      ,r1.descripcion_rechazo1
                      ,r1.rechazo2
                      ,r1.descripcion_rechazo2
                      ,r1.numero_tarjeta_nueva
                      ,r1.fecha_devolucion_respuesta
                      ,r1.fecha_pago
                      ,r1.numero_cartera_cliente
                      ,r1.fecha_generacion
                      ,r1.contenido
                      ,CASE WHEN r1.codigo_error_debito = '' THEN 'NUL'
                            ELSE r1.codigo_error_debito
                            END AS codigo_error_debito  
                      ,CASE WHEN r1.descripcion_error_debito = '' THEN 'NUL'
                            ELSE r1.descripcion_error_debito
                            END AS descripcion_error_debito
                      ,r1.fecha_origen_venc_debito
                FROM alumno_cuenta_corriente acc
                         INNER JOIN (SELECT ad.id_alumno_cc
                                           ,MAX(SUBSTRING(ar.Nombre_archivo FROM 10 FOR 8)) AS UltimaFecha
                                     FROM archivo_respuesta ar
                                        INNER JOIN archivo_respuesta_detalle ad ON ad.id_archivo_respuesta = ar.id_archivo_respuesta
                                     WHERE 1=1 $where
                                     GROUP BY id_alumno_cc) AS max_fecha ON max_fecha.id_alumno_cc = acc.id_alumno_cc
                         INNER JOIN (SELECT ar.id_archivo_respuesta
                                           ,ar.id_marca_tarjeta
                                           ,ar.id_medio_pago
                                           ,ar.nombre_archivo
                                           ,ar.numero_establecimiento
                                           ,ar.usuario_alta
                                           ,SUBSTRING(ar.nombre_archivo FROM 10 FOR 8) AS fecha
                                           ,ar.cantidad_total_debitos
                                           ,ar.importe_total_debitos
                                           ,ar.cuota
                                           ,ard.id_archivo_respuesta_detalle
                                           ,ard.registro
                                           ,ard.numero_codigo_banco_pagador
                                           ,ard.numero_sucursal_banco_pagador
                                           ,ard.numero_lote
                                           ,ard.codigo_transaccion
                                           ,ard.numero_tarjeta
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
                                           ,ard.codigo_error_debito
                                           ,ard.descripcion_error_debito
                                           ,ard.fecha_origen_venc_debito
                                           ,ard.id_alumno_cc
                                     FROM archivo_respuesta ar
                                              INNER JOIN archivo_respuesta_detalle ard ON ard.id_archivo_respuesta = ar.id_archivo_respuesta
                                     WHERE 1=1 AND ard.procesado = 0 $where
                                     GROUP BY ar.id_archivo_respuesta
                                             ,ar.id_marca_tarjeta
                                             ,ar.id_medio_pago
                                             ,ar.nombre_archivo
                                             ,ar.numero_establecimiento
                                             ,ar.usuario_alta
                                             ,ar.cantidad_total_debitos
                                             ,ar.importe_total_debitos
                                             ,ar.cuota
                                             ,ard.id_archivo_respuesta_detalle
                                             ,ard.registro
                                             ,ard.numero_codigo_banco_pagador
                                             ,ard.numero_sucursal_banco_pagador
                                             ,ard.numero_lote
                                             ,ard.codigo_transaccion
                                             ,ard.numero_tarjeta
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
                                             ,ard.codigo_error_debito
                                             ,ard.descripcion_error_debito
                                             ,ard.fecha_origen_venc_debito
                                             ,ard.id_alumno_cc) AS r1 ON r1.id_alumno_cc = max_fecha.id_alumno_cc and max_fecha.UltimaFecha = r1.fecha
                         INNER JOIN alumno a ON a.id_alumno = acc.id_alumno
                         INNER JOIN persona p ON a.id_persona = p.id_persona
                WHERE 1=1;
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_datos_cuadro_control_mensual($filtro = null)
    {
        $where = '';

        if (isset($filtro)) {
            if (isset($filtro['anio'])) {
                $where .= " AND substring(acc.cuota, 3,4) = '{$filtro['anio']}'";
            }
        }

        $sql = "SELECT (CASE WHEN left(acc.cuota, 2) = '01' THEN 'ENERO'
                             WHEN left(acc.cuota, 2) = '02' THEN 'FEBRERO'
                             WHEN left(acc.cuota, 2) = '03' THEN 'MARZO'
                             WHEN left(acc.cuota, 2) = '04' THEN 'ABRIL'
                             WHEN left(acc.cuota, 2) = '05' THEN 'MAYO'
                             WHEN left(acc.cuota, 2) = '06' THEN 'JUNIO'
                             WHEN left(acc.cuota, 2) = '07' THEN 'JULIO'
                             WHEN left(acc.cuota, 2) = '08' THEN 'AGOSTO'
                             WHEN left(acc.cuota, 2) = '09' THEN 'SEPTIEMBRE'
                             WHEN left(acc.cuota, 2) = '10' THEN 'OCTUBRE'
                             WHEN left(acc.cuota, 2) = '11' THEN 'NOVIEMBRE'
                             WHEN left(acc.cuota, 2) = '12' THEN 'DICIEMBRE'
                        END) AS mes
                      ,COALESCE(cargo_mensual_inscripcion.cargo_mensual_inscripcion, 0) AS cargo_mensual_inscripcion
                      ,COALESCE(cargo_mensual_cuota.cargo_mensual_cuota, 0) AS cargo_mensual_cuota
                      ,COALESCE(total_cargo_mensual.total_cargo_mensual, 0) AS total_cargo_mensual
                      ,COALESCE(cuotas_impagas_generadas.cuotas_impagas_generadas, 0) AS cuotas_impagas_generadas
                      ,COALESCE(cuotas_impagas_liquidadas.cuotas_impagas_liquidadas, 0) AS cuotas_impagas_liquidadas
                      ,COALESCE(cuotas_impagas_rechazadas.cuotas_impagas_rechazadas, 0) AS cuotas_impagas_rechazadas
                      ,COALESCE(total_cuotas_impagas.total_cuotas_impagas, 0) AS total_cuotas_impagas
                      ,COALESCE(cantidad_pagadores_transferencia.cantidad_pagadores_transferencia, 0) AS cantidad_pagadores_transferencia
                      ,COALESCE(cantidad_pagadores_debito.cantidad_pagadores_debito, 0) AS cantidad_pagadores_debito
                      ,COALESCE(cantidad_pagadores_credito.cantidad_pagadores_credito, 0) AS cantidad_pagadores_credito
                      ,COALESCE(cantidad_pagadores_posnet.cantidad_pagadores_posnet, 0) AS cantidad_pagadores_posnet
                      ,COALESCE(cantidad_pagadores_deposito.cantidad_pagadores_deposito, 0) AS cantidad_pagadores_deposito
                      ,COALESCE(total_cuotas_pagas.total_cuotas_pagas, 0) AS total_cuotas_pagas
                FROM alumno_cuenta_corriente acc
                         INNER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                  WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                  WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                  WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                  WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                  WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                  WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                  WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                  WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                  WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                  WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                  WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                            END) AS mes
                                          ,cuota
                                          ,count(id_alumno_cc) AS cargo_mensual_cuota
                                     FROM alumno_cuenta_corriente
                                     WHERE 1=1 AND left(cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                       AND id_cargo_cuenta_corriente = 2
                                     GROUP BY 1, 2) AS cargo_mensual_cuota ON cargo_mensual_cuota.cuota = acc.cuota
                         LEFT OUTER JOIN (SELECT CASE WHEN substr(fecha_generacion_cc::varchar,1,7) = '2022-11' THEN 'NOVIEMBRE' END AS mes
                                                ,'112022' AS cuota
                                                ,count(id_alumno_cc) as cargo_mensual_inscripcion
                                          FROM alumno_cuenta_corriente
                                          WHERE 1=1
                                            AND id_cargo_cuenta_corriente = 1
                                          GROUP BY 1, 2) AS cargo_mensual_inscripcion ON cargo_mensual_inscripcion.cuota = acc.cuota
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '07' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '07' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '07' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '07' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(id_alumno_cc) AS total_cargo_mensual
                                         FROM alumno_cuenta_corriente
                                         WHERE 1=1 AND left(cuota, 2) IN ('01', '02','03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                         GROUP BY 1, 2) AS total_cargo_mensual ON total_cargo_mensual.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                      WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                      WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                      WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                      WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                      WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                      WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                      WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                      WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                      WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                      WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                      WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                END) AS mes
                                               ,cuota
                                               ,count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_generadas
                                 FROM transaccion_cuenta_corriente trcc
                                          LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                          INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                          INNER JOIN persona p on p.id_persona = a.id_persona
                                          LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                ,ultimo_cambio
                                                                ,e.id_estado_cuota
                                                                ,e.nombre AS estado_actual
                                                                ,tcc.id_motivo_rechazo1
                                                                ,mr.nombre AS motivo_rechazo
                                                           FROM (SELECT id_alumno_cc
                                                                      ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                 FROM transaccion_cuenta_corriente tcc
                                                                 GROUP BY id_alumno_cc) AS estado_actual
                                                                    INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                    INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                    LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo1 = mr.id_motivo_rechazo
                                                                    LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                           ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                 WHERE 1=1 AND left(acc.cuota, 2) IN ('01', '02','03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                   AND b.id_estado_cuota IN (1)
                                 GROUP BY 1,2) AS cuotas_impagas_generadas ON cuotas_impagas_generadas.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_liquidadas
                                          FROM transaccion_cuenta_corriente trcc
                                                   LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                                   INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                                   INNER JOIN persona p on p.id_persona = a.id_persona
                                                   LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                          ,ultimo_cambio
                                                                          ,e.id_estado_cuota
                                                                          ,e.nombre AS estado_actual
                                                                          ,tcc.id_motivo_rechazo1
                                                                          ,mr.nombre AS motivo_rechazo
                                                                    FROM (SELECT id_alumno_cc
                                                                                ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                          FROM transaccion_cuenta_corriente tcc
                                                                          GROUP BY id_alumno_cc) AS estado_actual
                                                                             INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                             INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                             LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo1 = mr.id_motivo_rechazo
                                                                             LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                    ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota IN (2)
                                          GROUP BY 1,2) AS cuotas_impagas_liquidadas ON cuotas_impagas_liquidadas.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cuotas_impagas_rechazadas
                                          FROM transaccion_cuenta_corriente trcc
                                                  LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                                  INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                                  INNER JOIN persona p on p.id_persona = a.id_persona
                                                  LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                         ,ultimo_cambio
                                                                         ,e.id_estado_cuota
                                                                         ,e.nombre AS estado_actual
                                                                         ,tcc.id_motivo_rechazo1
                                                                         ,mr.nombre AS motivo_rechazo
                                                                   FROM (SELECT id_alumno_cc
                                                                              ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                         FROM transaccion_cuenta_corriente tcc
                                                                         GROUP BY id_alumno_cc) AS estado_actual
                                                                            INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                            INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                            LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo1 = mr.id_motivo_rechazo
                                                                            LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                   ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota IN (4)
                                          GROUP BY 1,2) AS cuotas_impagas_rechazadas ON cuotas_impagas_rechazadas.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS total_cuotas_impagas
                                          FROM transaccion_cuenta_corriente trcc
                                                  LEFT OUTER JOIN alumno_cuenta_corriente acc on trcc.id_alumno_cc = acc.id_alumno_cc
                                                  INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                                  INNER JOIN persona p on p.id_persona = a.id_persona
                                                  LEFT OUTER JOIN (SELECT estado_actual.id_alumno_cc
                                                                         ,ultimo_cambio
                                                                         ,e.id_estado_cuota
                                                                         ,e.nombre AS estado_actual
                                                                         ,tcc.id_motivo_rechazo1
                                                                         ,mr.nombre AS motivo_rechazo
                                                                   FROM (SELECT id_alumno_cc
                                                                              ,MAX(tcc.fecha_transaccion) AS ultimo_cambio
                                                                         FROM transaccion_cuenta_corriente tcc
                                                                         GROUP BY id_alumno_cc) AS estado_actual
                                                                            INNER JOIN transaccion_cuenta_corriente tcc on (estado_actual.id_alumno_cc = tcc.id_alumno_cc AND tcc.fecha_transaccion = estado_actual.ultimo_cambio)
                                                                            INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                                            LEFT OUTER JOIN motivo_rechazo mr on tcc.id_motivo_rechazo1 = mr.id_motivo_rechazo
                                                                            LEFT OUTER JOIN estado_cuota e on e.id_estado_cuota = tcc.id_estado_cuota
                                                                   ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota IN (1, 2, 4)
                                          GROUP BY 1,2) AS total_cuotas_impagas ON total_cuotas_impagas.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                      WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                      WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                      WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                      WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                      WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                      WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                      WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                      WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                      WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                      WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                      WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_transferencia
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
                                                                             LEFT OUTER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                             INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                                    ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3 --PAGAS
                                            AND b.id_medio_pago = 2
                                          GROUP BY 1,2) AS cantidad_pagadores_transferencia ON cantidad_pagadores_transferencia.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                ,cuota
                                                ,count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_debito
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
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3 --PAGAS
                                            AND b.id_medio_pago = 3
                                          GROUP BY 1,2) AS cantidad_pagadores_debito ON cantidad_pagadores_debito.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_credito
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
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3 --PAGAS
                                            AND b.id_medio_pago = 4
                                          GROUP BY 1,2) AS cantidad_pagadores_credito ON cantidad_pagadores_credito.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_posnet
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
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3 --PAGAS
                                            AND b.id_medio_pago = 5
                                          GROUP BY 1,2) AS cantidad_pagadores_posnet ON cantidad_pagadores_posnet.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS cantidad_pagadores_deposito
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
                                                                             LEFT OUTER JOIN marca_tarjeta mt on tcc.id_marca_tarjeta = mt.id_marca_tarjeta
                                                                             INNER JOIN medio_pago mp on tcc.id_medio_pago = mp.id_medio_pago
                                                                    ORDER BY tcc.fecha_transaccion DESC) AS b ON b.id_alumno_cc = trcc.id_alumno_cc
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3 --PAGAS
                                            AND b.id_medio_pago = 6
                                          GROUP BY 1,2) AS cantidad_pagadores_deposito ON cantidad_pagadores_deposito.cuota = acc.cuota
                
                         LEFT OUTER JOIN (SELECT (CASE WHEN left(cuota, 2) = '01' THEN 'ENERO'
                                                       WHEN left(cuota, 2) = '02' THEN 'FEBRERO'
                                                       WHEN left(cuota, 2) = '03' THEN 'MARZO'
                                                       WHEN left(cuota, 2) = '04' THEN 'ABRIL'
                                                       WHEN left(cuota, 2) = '05' THEN 'MAYO'
                                                       WHEN left(cuota, 2) = '06' THEN 'JUNIO'
                                                       WHEN left(cuota, 2) = '07' THEN 'JULIO'
                                                       WHEN left(cuota, 2) = '08' THEN 'AGOSTO'
                                                       WHEN left(cuota, 2) = '09' THEN 'SEPTIEMBRE'
                                                       WHEN left(cuota, 2) = '10' THEN 'OCTUBRE'
                                                       WHEN left(cuota, 2) = '11' THEN 'NOVIEMBRE'
                                                       WHEN left(cuota, 2) = '12' THEN 'DICIEMBRE'
                                                  END) AS mes
                                                 ,cuota
                                                 ,count(DISTINCT(b.id_alumno_cc)) AS total_cuotas_pagas
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
                                          WHERE 1=1 AND left(acc.cuota, 2) IN ('03', '04', '05', '06', '07', '08', '09', '10', '11', '12')
                                            AND b.id_estado_cuota = 3
                                          GROUP BY 1,2) AS total_cuotas_pagas ON total_cuotas_pagas.cuota = acc.cuota
                
                WHERE 1=1
                    $where
                GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14
                ORDER BY 1 DESC
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_saldo_cuenta_corriente($filtro = null)
    {
        $select = $from = '';
        $where = $where_interno = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND p.id_persona = '{$filtro['id_persona']}'";
            }

            if (isset($filtro['id_tipo_documento'])) {
                $where .= " AND ptd.id_tipo_documento = '{$filtro['id_tipo_documento']}'";
            }

            if (isset($filtro['numero'])) {
                $where .= " AND ptd.numero = '{$filtro['numero']}'";
            }

            if (isset($filtro['id_sexo'])) {
                $where .= " AND ps.id_sexo = '{$filtro['id_sexo']}'";
            }

            if (isset($filtro['apellidos'])) {
                $where .= " AND p.apellidos ILIKE '%{$filtro['apellidos']}%'";
            }

            if (isset($filtro['activo'])) {
                $where .= " AND p.activo = '{$filtro['activo']}'";
            }

            if (isset($filtro['estado_alumno'])) {
                $where .= " AND a.regular = '{$filtro['estado_alumno']}'";
            }

            if (isset($filtro['cuota'])) {
                if ($filtro['cuota'] < 10) {
                    $filtro['cuota'] = '0'.$filtro['cuota'];
                }
                if ($filtro['cuota'] == 11) {
                    $where_interno .= " AND ((substring(acc.cuota, 1, 2) = '{$filtro['cuota']}') OR acc.cuota = '')";
                } else {
                    $where_interno .= " AND substring(acc.cuota, 1, 2) = '{$filtro['cuota']}'";
                }
            }

            if (isset($filtro['anio'])) {
                if ($filtro['cuota'] == 11) {
                    $where_interno .= " AND ((substring(acc.cuota, 3, 4) = '{$filtro['anio']}') OR acc.cuota = '')";
                } else {
                    $where_interno .= " AND substring(acc.cuota, 3, 4) = '{$filtro['anio']}'";
                }
            }

            /*if (isset($filtro['estado_cuota'])) {
                if ($filtro['estado_cuota'] == 'p') {
                    $where .= " AND b.id_estado_cuota IN (3)";
                } elseif ($filtro['estado_cuota'] == 'i') {
                    $where .= " AND b.id_estado_cuota IN (1, 2, 4)";
                }
            }*/

            if (isset($filtro['con_saldo'])) {
                if ($filtro['con_saldo'] == 'S') {
                    $select .= ",(COALESCE(subconsulta_saldo.saldo, 0)) as saldo";
                    $from .= " LEFT OUTER JOIN (SELECT acc.id_alumno
                                                      ,p.id_persona
                                                      ,COALESCE(SUM(tcc.importe), 0) as saldo
                                                FROM transaccion_cuenta_corriente tcc
                                                    INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                                    INNER JOIN alumno a on a.id_alumno = acc.id_alumno
                                                    INNER JOIN persona p on p.id_persona = a.id_persona
                                                $where_interno    
                                                GROUP BY acc.id_alumno
                                                        ,p.id_persona
                                                ORDER BY acc.id_alumno) AS subconsulta_saldo ON subconsulta_saldo.id_alumno = a.id_alumno and subconsulta_saldo.id_persona = p.id_persona 
                             ";
                }
            }

            if (isset($filtro['tiene_tarjeta'])) {
                if ($filtro['tiene_tarjeta'] == 'S') {
                    $where .= " AND EXISTS (SELECT ''
                                            FROM alumno_tarjeta at
                                            WHERE a.id_alumno = at.id_alumno
                                                 AND at.activo = 'S')";
                }
                if ($filtro['tiene_tarjeta'] == 'N') {
                    $where .= " AND NOT EXISTS (SELECT ''
                                                FROM alumno_tarjeta at
                                                WHERE a.id_alumno = at.id_alumno
                                                    AND at.activo = 'S')";
                }
            }
        }
        $sql = "SELECT a.id_alumno
                      ,p.id_persona
                      ,(p.apellidos || ', ' || p.nombres)     as persona
                      ,ptd.id_tipo_documento
                      ,td.nombre                              as tipo_documento
                      ,td.nombre_corto                        as tipo_documento_corto
                      ,ptd.numero                             as identificacion
                      ,ps.id_sexo
                      ,s.nombre                               as sexo
                      ,(CASE
                            WHEN p.es_alumno = 'S' AND a.regular = 'S' THEN 'Regular'
                            WHEN p.es_alumno = 'S' AND a.regular = 'N' THEN 'No regular'
                            WHEN p.es_alumno = 'N' THEN '-'
                        END)  as estado_alumno
                      $select  
                FROM alumno a
                    INNER JOIN persona p on p.id_persona = a.id_persona
                    LEFT OUTER JOIN (persona_sexo ps JOIN sexo s on ps.id_sexo = s.id_sexo)
                                    ON ps.id_persona = p.id_persona AND ps.activo = 'S'
                    LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td
                                     on ptd.id_tipo_documento = td.id_tipo_documento)
                                    ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                         FROM tipo_documento X1
                                                                                            , persona_tipo_documento X2
                                                                                         WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                           AND X2.id_persona = p.id_persona)
                    $from                    
                $where
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /**
     * Obtiene el listado de los perfiles funcionales asociados a un usuario en particular
     */
    public static function get_perfiles_funcionales_por_usuario($usuario, $ordena = null)
    {
        $where = 'WHERE 1 = 1';

        if (isset($usuario)) {
            $where = $where . " AND usuario = '{$usuario}'";
        }

        $sql = "SELECT proyecto
					  ,usuario_grupo_acc
					  ,usuario_perfil_datos
				FROM desarrollo.apex_usuario_proyecto
				$where
					AND proyecto = 'gestionescuelas'
		       ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $datos = toba::db('toba_3_3')->consultar($sql);

        if (isset($datos) && ($ordena == true)) {
            return self::ordenar_perfiles_funcionales($datos);
        } else {
            return $datos;
        }
    }

    public static function ordenar_perfiles_funcionales($datos)
    {
        if (isset($datos)) {
            $funcionales = array();
            $cant = count($datos);
            for ($i = 0; $i < $cant; $i++) {			//Recorro los valores formando un arreglo posicional.
                $funcionales[] = $datos[$i]['usuario_grupo_acc'];
            }

            toba::logger()->debug('Perfiles funcionales desde el ordenar_perfiles:');
            toba::logger()->debug($funcionales);
            return $funcionales;
        }
    }

    /*
    * Retorna el id de la persona seg�n un usuario
    */
    public static function get_id_persona_x_id_usuario($id_usuario = null)
    {
        $where = 'WHERE 1 = 1';

        if (isset($id_usuario)) {
            $where = $where . " AND ptd.numero = '{$id_usuario}'";
        }

        $sql = "SELECT persona.id_persona
			    FROM persona
			        INNER JOIN persona_tipo_documento ptd on persona.id_persona = ptd.id_persona
				$where
		       ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna todas las personas que son tutores y tienen al menos 1 allegado activo
     */
    public static function get_tutores_con_allegados($filtro = null)
    {
        $where = 'WHERE 1 = 1';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND p.id_persona = '{$filtro['id_persona']}'";
            }
        }

        $sql = "SELECT DISTINCT(p.id_persona) as id_persona
                      ,p.apellidos
                      ,p.nombres
                      ,(p.apellidos || ', ' || p.nombres) as persona
                      ,p.correo_electronico
                      ,p.es_alumno
                      ,pa.tutor
                      ,p.usuario
                      ,td.nombre as tipo_documento
                      ,td.nombre_corto as tipo_documento_corto
                      ,ptd.numero as identificacion
                FROM persona p
                    INNER JOIN persona_allegado pa on p.id_persona = pa.id_persona and pa.tutor = 'S' and pa.activo = 'S'
                    LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                                    ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                         FROM tipo_documento X1
                                                                                            ,persona_tipo_documento X2
                                                                                         WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                           AND X2.id_persona = p.id_persona)
                $where 
                    AND p.es_alumno = 'N'
                ORDER BY p.id_persona
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function estado_servidor_afip()
    {
        /*
         * Valido el estado del servidor AFIP (AppServer) para la generaci�n de los comprobantes
         * por el momento no valido los par�metros DbServer y AuthServer
         */

        $afip = new Afip();
        return $afip->conectado();

        /*$afip = new Afip();
        $server_status = $afip->ElectronicBilling->GetServerStatus();
        var_dump($server_status);
        if ($server_status === NULL) {
            return false;
        } else {
            if ($server_status->AppServer != 'OK') {
                return false;
            }
        }
        return true;*/
    }

    /*
     * Retorna los datos necesarios del alumno y la cuota que se abona para mostrar en la factura AFIP
     */
    public static function get_datos_alumno_cuenta_corriente($filtro=null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_alumno_cc'])) {
                $where .= " AND id_alumno_cc = '{$filtro['id_alumno_cc']}'";
            }
        }

        $sql = "SELECT acc.id_alumno_cc
                      ,acc.cuota
                      ,acc.descripcion || ' - Alumnos: ' || (p.apellidos ||', '|| p.nombres) as descripcion
                      ,p.nombres
                      ,p.apellidos
                      ,(p.apellidos ||', '|| p.nombres) as persona
                      ,a.direccion_calle
                      ,a.direccion_numero
                      ,a.direccion_piso
                      ,a.direccion_depto
                FROM alumno_cuenta_corriente acc
                    INNER JOIN alumno a on acc.id_alumno = a.id_alumno
                    INNER JOIN persona p on a.id_persona = p.id_persona
                $where
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    /*
     * Retorna los datos de los alumnos con deuda
     */
    public static function get_listado_estado_deuda_alumnos($filtro=null)
    {
        $where = 'WHERE 1=1';
        $having = '';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND p.id_persona = '{$filtro['id_persona']}'";
            }

            if (isset($filtro['id_tipo_documento'])) {
                $where .= " AND ptd.id_tipo_documento = '{$filtro['id_tipo_documento']}'";
            }

            if (isset($filtro['numero'])) {
                $where .= " AND ptd.numero = '{$filtro['numero']}'";
            }

            if (isset($filtro['id_sexo'])) {
                $where .= " AND ps.id_sexo = '{$filtro['id_sexo']}'";
            }

            if (isset($filtro['apellidos'])) {
                $where .= " AND p.apellidos ILIKE '%{$filtro['apellidos']}%'";
            }

            if (isset($filtro['activo'])) {
                $where .= " AND p.activo = '{$filtro['activo']}'";
            }

            if (isset($filtro['estado_alumno'])) {
                $where .= " AND a.regular = '{$filtro['estado_alumno']}'";
            }

            if (isset($filtro['cuota'])) {
                if ($filtro['cuota'] < 10) {
                    $filtro['cuota'] = '0'.$filtro['cuota'];
                }
                if ($filtro['cuota'] == 11) {
                    $where .= " AND ((substring(acc.cuota, 1, 2) = '{$filtro['cuota']}') OR acc.cuota = '')";
                } else {
                    $where .= " AND substring(acc.cuota, 1, 2) = '{$filtro['cuota']}'";
                }
            }

            if (isset($filtro['anio'])) {
                if ($filtro['cuota'] == 11) {
                    $where .= " AND ((substring(acc.cuota, 3, 4) = '{$filtro['anio']}') OR acc.cuota = '')";
                } else {
                    $where .= " AND substring(acc.cuota, 3, 4) = '{$filtro['anio']}'";
                }
            }

            if (isset($filtro['tiene_tarjeta'])) {
                if ($filtro['tiene_tarjeta'] == 'S') {
                    $where .= " AND EXISTS (SELECT ''
                                            FROM alumno_tarjeta at
                                            WHERE a.id_alumno = at.id_alumno
                                                 AND at.activo = 'S')";
                }
                if ($filtro['tiene_tarjeta'] == 'N') {
                    $where .= " AND NOT EXISTS (SELECT ''
                                                FROM alumno_tarjeta at
                                                WHERE a.id_alumno = at.id_alumno
                                                    AND at.activo = 'S')";
                }
            }

            if (isset($filtro['saldo'])) {
                switch ($filtro['saldo']) {
                    case 'M':
                        $having .= " HAVING SUM(tcc.importe) > 0";
                        break;
                    case 'm':
                        $having .= " HAVING SUM(tcc.importe) < 0";
                        break;
                    case 'D':
                        $having .= " HAVING SUM(tcc.importe) <> 0";
                        break;
                }
            }
        }

        $sql = "SELECT a.id_alumno
                      ,a.legajo
                      ,acc.id_alumno_cc
                      ,p.id_persona
                      ,(p.apellidos || ', ' || p.nombres)     as persona
                      ,ptd.id_tipo_documento
                      ,td.nombre                              as tipo_documento
                      ,td.nombre_corto                        as tipo_documento_corto
                      ,ptd.numero                             as identificacion
                      ,ps.id_sexo
                      ,s.nombre                               as sexo
                      ,(CASE
                           WHEN p.es_alumno = 'S' AND a.regular = 'S' THEN 'Regular'
                           WHEN p.es_alumno = 'S' AND a.regular = 'N' THEN 'No regular'
                           WHEN p.es_alumno = 'N' THEN '-'
                        END)  as estado_alumno
                      ,acc.cuota
                      ,SUM(tcc.importe) AS SaldoCuota
                      ,SaldoTotal
                FROM transaccion_cuenta_corriente tcc
                    INNER JOIN alumno_cuenta_corriente acc ON acc.id_alumno_Cc = tcc.id_alumno_cc
                    INNER JOIN alumno a ON a.id_alumno = acc.id_alumno AND a.regular = 'S'
                    INNER JOIN persona p ON p.id_persona = a.id_persona
                    INNER JOIN (SELECT id_alumno, sum(tc.importe) AS SaldoTotal
                                FROM transaccion_cuenta_corriente tc
                                    INNER JOIN alumno_cuenta_corriente acc ON acc.id_alumno_cc = tc.id_alumno_cc
                                GROUP BY id_alumno) AS R1 ON R1.id_alumno = a.id_alumno
                    LEFT OUTER JOIN (persona_sexo ps JOIN sexo s on ps.id_sexo = s.id_sexo)
                                    ON ps.id_persona = p.id_persona AND ps.activo = 'S'
                    LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td
                                     on ptd.id_tipo_documento = td.id_tipo_documento)
                                    ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                         FROM tipo_documento X1
                                                                                            , persona_tipo_documento X2
                                                                                         WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                           AND X2.id_persona = p.id_persona)
                    $where
                GROUP BY a.id_alumno
                        ,p.apellidos
                        ,p.nombres
                        ,p.id_persona
                        ,a.legajo
                        ,acc.id_alumno_cc
                        ,acc.cuota
                        ,SaldoTotal
                        ,ptd.id_tipo_documento
                        ,td.nombre
                        ,td.nombre_corto
                        ,ptd.numero
                        ,ps.id_sexo
                        ,s.nombre
                $having
                ORDER BY a.legajo;
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        return toba::db()->consultar($sql);
    }

    public static function get_documento_tutor($filtro = null)
    {
        $where = 'WHERE 1=1';

        if (isset($filtro)) {
            if (isset($filtro['id_persona'])) {
                $where .= " AND p.id_persona = '{$filtro['id_persona']}'";
            }
        }

        $sql = "SELECT pa.id_persona_allegado
                      ,pa.id_persona
                      ,(p.apellidos || ', ' || p.nombres) as nombre_alumno
                      ,pa.id_alumno
                      ,pa.id_tipo_allegado
                      ,ta.nombre as allegado
                      ,per.id_persona as id_persona_tutor
                      ,per.nombres
                      ,per.apellidos
                      ,(per.apellidos || ', ' || per.nombres) as nombre_tutor
                      ,ptd.numero as identificacion_tutor
                FROM persona_allegado pa
                    INNER JOIN alumno a on pa.id_alumno = a.id_alumno
                    INNER JOIN persona p on p.id_persona = a.id_persona
                    INNER JOIN persona per on per.id_persona = pa.id_persona
                    LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                                     ON ptd.id_persona = per.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                            FROM tipo_documento X1
                                                                                                ,persona_tipo_documento X2
                                                                                            WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                               AND X2.id_persona = p.id_persona)
                    INNER JOIN tipo_allegado ta on pa.id_tipo_allegado = ta.id_tipo_allegado
                $where
                  AND pa.tutor = 'S' AND pa.activo = 'S'
                ORDER BY ta.jerarquia
                        ,pa.id_tipo_allegado
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $datos = consultar_fuente($sql);
        if (isset($filtro['solo_identificacion_tutor'])) {
            if ($filtro['solo_identificacion_tutor'] == 1) {
                return $datos[0]['identificacion_tutor'];
            }
        }
        return $datos;
    }

}