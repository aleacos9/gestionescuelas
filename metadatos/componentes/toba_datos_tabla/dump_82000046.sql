------------------------------------------------------------
--[82000046]--  DT - parametros_sistema 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 82
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'gestionescuelas', --proyecto
	'82000046', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'82000001', --punto_montaje
	'dt_parametros_sistema', --subclase
	'datos/dt_parametros_sistema.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'DT - parametros_sistema', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'gestionescuelas', --fuente_datos_proyecto
	'gestionescuelas', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2022-04-04 21:25:53', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 82

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'82000001', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'parametros_sistema', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'gestionescuelas', --fuente_datos_proyecto
	'gestionescuelas', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	'public'  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 82
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000001', --col_id
	'parametro', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'42', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000002', --col_id
	'descripcion', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'255', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000003', --col_id
	'desc_corta', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'120', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000004', --col_id
	'valor', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'120', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000005', --col_id
	'version_publicacion', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'gestionescuelas', --objeto_proyecto
	'82000046', --objeto
	'82000006', --col_id
	'id_parametro', --columna
	'E', --tipo
	'1', --pk
	'sq_id_parametro', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'parametros_sistema'  --tabla
);
--- FIN Grupo de desarrollo 82
