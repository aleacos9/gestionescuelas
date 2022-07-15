
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	'Tutor', --nombre
	NULL, --nivel_acceso
	'Accede a las operaciones del tutor', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'1', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 82
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000004'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000016'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000023'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000024'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000025'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'gestionescuelas', --proyecto
	'tutor', --usuario_grupo_acc
	NULL, --item_id
	'82000026'  --item
);
--- FIN Grupo de desarrollo 82
