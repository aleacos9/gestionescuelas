<?php
class dao_personas
{
    /*
     * Retorna los datos de las personas
     */
    public function get_datos_personas($filtro=null)
    {
        $where = 'WHERE 1=1';
        $select = $select_inicial = $from = '';

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

            if (isset($filtro['solo_alumnos'])) {
                if ($filtro['solo_alumnos'] == 1) {
                    $select .= ",md.id_motivo_desercion
                                ,md.nombre as motivo_desercion";

                    $from .= " INNER JOIN alumno a on p.id_persona = a.id_persona
                               LEFT OUTER JOIN motivo_desercion md on a.id_motivo_desercion = md.id_motivo_desercion";
                }
                if ($filtro['solo_alumnos'] == 0) {
                    $from .= " LEFT OUTER JOIN alumno a on p.id_persona = a.id_persona";
                }
            }

            if (isset($filtro['tiene_allegados'])) {
                if ($filtro['tiene_allegados'] == 'S') {
                    $where .= " AND EXISTS (SELECT ''
                                            FROM persona_allegado pa2
                                            WHERE a.id_alumno = pa2.id_alumno
                                                 AND pa2.activo = 'S')";
                }
                if ($filtro['tiene_allegados'] == 'N') {
                    $where .= " AND NOT EXISTS (SELECT ''
                                                FROM persona_allegado pa2
                                                WHERE a.id_alumno = pa2.id_alumno
                                                    AND pa2.activo = 'S')";
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

            if (isset($filtro['vincular_allegados'])) {
                if ($filtro['vincular_allegados'] == 'S') {
                    $select_inicial .= "distinct a.id_alumno
                                       ,p.id_persona";
                }
            } else {
                $select_inicial .= "distinct p.id_persona 
                                   ,a.id_alumno";
            }

            if (isset($filtro['con_saldo_actual'])) {
                if ($filtro['con_saldo_actual'] == 'S') {
                    $select .= ",(COALESCE(subconsulta_saldo.saldo, 0)) as saldo_actual";
                    $from .= " LEFT OUTER JOIN (SELECT acc.id_alumno
                                                     ,tcc.id_alumno_cc
                                                     ,COALESCE(SUM(tcc.importe), 0) as saldo
                                               FROM transaccion_cuenta_corriente tcc
                                                    INNER JOIN alumno_cuenta_corriente acc on tcc.id_alumno_cc = acc.id_alumno_cc
                                               GROUP BY tcc.id_alumno_cc
                                                       ,acc.id_alumno
                                               ORDER BY acc.id_alumno
                                                       ,tcc.id_alumno_cc) AS subconsulta_saldo ON subconsulta_saldo.id_alumno = a.id_alumno 
                             ";
                }
            }

            /*if (isset($filtro['administrar_forma_cobro'])) {
                if ($filtro['administrar_forma_cobro'] == 'S') {
                    $select_inicial .= "distinct a.id_alumno
                                       ,p.id_persona";
                }
            } else {
                $select_inicial .= "distinct p.id_persona
                                   ,a.id_alumno";
            }*/
        }

        $sql = "SELECT $select_inicial
                       --distinct p.id_persona
                      ,p.apellidos
                      ,p.nombres
                      ,(p.apellidos ||', '|| p.nombres) as persona
                      ,ptd.id_tipo_documento
                      ,td.nombre as tipo_documento
                      ,td.nombre_corto as tipo_documento_corto
                      ,ptd.numero as identificacion
                      ,ps.id_sexo
                      ,s.nombre as sexo
                      ,fecha_nacimiento
                      ,id_localidad_nacimiento
                      ,ln.nombre as localidad_nacimiento
                      ,ln.id_provincia
                      ,pro_ln.nombre as provincia_nacimiento
                      ,id_localidad_residencia
                      ,lr.nombre as localidad_residencia
                      ,lr.id_provincia
                      ,pro_lr.nombre as provincia_residencia
                      ,p.id_nacionalidad
                      ,n.nombre_corto as nacionalidad
                      ,correo_electronico
                      ,(CASE WHEN p.activo = 'S' THEN 'Activo'
                             WHEN p.activo = 'N' THEN 'Inactivo'
                        END) as estado_persona         
                      ,usuario
                      ,recibe_notif_x_correo
                      ,telefono
                      ,p.fecha_alta
                      ,p.usuario_alta
                      ,p.fecha_ultima_modificacion
                      ,p.usuario_ultima_modificacion
                      ,p.usuario
                      --,a.id_alumno
                      ,a.legajo
                      ,a.extranjero
                      ,a.regular    
                      ,a.es_celiaco
                      ,a.direccion_calle
                      ,a.direccion_numero
                      ,a.direccion_piso
                      ,a.direccion_depto
                      ,(CASE WHEN p.es_alumno = 'S' THEN 'Sí'
                             WHEN p.es_alumno = 'N' THEN 'No'
                        END) as es_alumno         
                      ,(CASE WHEN p.es_alumno = 'S' AND a.regular = 'S' THEN 'Regular'
                             WHEN p.es_alumno = 'S' AND a.regular = 'N' THEN 'No regular'
                             WHEN p.es_alumno = 'N' THEN '-'
                        END) as estado_alumno
                      ,(CASE WHEN subconsulta_tiene_allegado.id_persona IS NOT NULL THEN 'Sí'
                             ELSE 'No'
                       	END) AS tiene_allegado
                      ,(CASE WHEN subconsulta_tiene_tarjeta.id_alumno IS NOT NULL THEN 'Sí'
                             ELSE 'No'
                       	END) AS tiene_tarjeta 	  
                      $select
            FROM persona p
                LEFT OUTER JOIN (persona_sexo ps JOIN sexo s on ps.id_sexo = s.id_sexo)
                         ON ps.id_persona = p.id_persona AND ps.activo = 'S'
                LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                    ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                         FROM tipo_documento X1
                                                                             ,persona_tipo_documento X2
                                                                         WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                            AND X2.id_persona = p.id_persona)
                                                     --AND ptd.activo = 'S'
                INNER JOIN localidad ln on p.id_localidad_nacimiento = ln.id_localidad
                JOIN provincia pro_ln on ln.id_provincia = pro_ln.id_provincia
                INNER JOIN localidad lr on lr.id_localidad = p.id_localidad_residencia
                JOIN provincia pro_lr on lr.id_provincia = pro_lr.id_provincia
                INNER JOIN nacionalidad n on p.id_nacionalidad = n.id_nacionalidad  
                $from
                LEFT OUTER JOIN (SELECT id_persona, id_alumno
					 		     FROM persona_allegado pa3
							     WHERE pa3.activo = 'S') AS subconsulta_tiene_allegado ON subconsulta_tiene_allegado.id_alumno = a.id_alumno
                LEFT OUTER JOIN (SELECT id_alumno
					 		     FROM alumno_tarjeta at
							     WHERE at.activo = 'S') AS subconsulta_tiene_tarjeta ON subconsulta_tiene_tarjeta.id_alumno = a.id_alumno							         
			$where
                AND p.activo != 'B'    
                ORDER BY p.apellidos
                        ,p.nombres;
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $datos = toba::db()->consultar($sql);

        if (isset($datos)) {
            if ($filtro['solo_ids'] == true) {
                return dao_personas::retornar_solo_ids($datos);
            } else {
                return $datos;
            }
        }
    }

    /*
     * Retorna los id_persona en un arreglo posicional
     */
    public static function retornar_solo_ids($datos)
    {
        if (isset($datos)) {
            $alumnos = array();
            $cant = count($datos);
            for ($i = 0; $i < $cant; $i++) {			//Recorro los valores formando un arreglo posicional.
                $alumnos[] = $datos[$i]['id_alumno'];
            }
            return $alumnos;
        }
    }
}