--Alejandro feature/alta-manual-pagos 10/03/2022
CREATE OR REPLACE FUNCTION alta_manual_pagos() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'transaccion_cuenta_corriente' and column_name = 'numero_comprobante') THEN

        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN numero_comprobante INTEGER;
        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN numero_lote INTEGER;
        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN numero_autorizacion INTEGER;

        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN id_medio_pago INTEGER;
        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN id_marca_tarjeta INTEGER;

        ALTER TABLE transaccion_cuenta_corriente
            ADD CONSTRAINT id_medio_pago FOREIGN KEY (id_medio_pago)
                REFERENCES medio_pago (id_medio_pago) MATCH FULL
                ON UPDATE NO ACTION ON DELETE NO ACTION;

        ALTER TABLE transaccion_cuenta_corriente
            ADD CONSTRAINT id_marca_tarjeta FOREIGN KEY (id_marca_tarjeta)
                REFERENCES marca_tarjeta (id_marca_tarjeta) MATCH FULL
                ON UPDATE NO ACTION ON DELETE NO ACTION;

        --Inserto los registros que deben tener como medio de pago tarjeta de crédito Visa
        UPDATE transaccion_cuenta_corriente
        SET id_medio_pago = 4
           ,id_marca_tarjeta = 1
        WHERE id_alumno_cc IN (76,139,47,146,145,120,131);

        --Inserto los registros que deben tener como medio de pago tarjeta de crédito MC
        UPDATE transaccion_cuenta_corriente
        SET id_medio_pago = 4
           ,id_marca_tarjeta = 2
        WHERE id_alumno_cc IN (89,90,7);

        --Inserto los registros que deben tener como medio de pago tarjeta de débito visa
        UPDATE transaccion_cuenta_corriente
        SET id_medio_pago = 3
           ,id_marca_tarjeta = 1
        WHERE id_alumno_cc NOT IN (76,139,47,146,145,120,131,89,90,7);

        ALTER TABLE transaccion_cuenta_corriente
            ALTER COLUMN id_medio_pago SET NOT NULL;

        ALTER TABLE transaccion_cuenta_corriente
            ALTER COLUMN id_marca_tarjeta SET NOT NULL;

        ALTER TABLE medio_pago
            ADD COLUMN se_muestra_alta_manual character(1) DEFAULT 'N'::bpchar;

        ALTER TABLE marca_tarjeta
            ADD COLUMN permite_posnet character(1) DEFAULT 'N'::bpchar;

        UPDATE marca_tarjeta
        SET permite_posnet = 'S'
        WHERE id_marca_tarjeta IN (1,2);

        UPDATE medio_pago
        SET se_muestra_alta_manual = 'S'
        WHERE id_medio_pago IN (2,5);

    END IF;
END;
$$ LANGUAGE 'plpgsql';



--Alejandro feature/alta-manual-pagos 10/03/2022
CREATE OR REPLACE FUNCTION cambios_tablas_archivo_respuesta() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'archivo_respuesta' and column_name = 'cuota') THEN

        --agrego en la tabla archivo_respuesta el campo cuota
        ALTER TABLE archivo_respuesta
            ADD COLUMN cuota character(6) NOT NULL;

        ALTER TABLE archivo_respuesta_detalle
            ADD COLUMN codigo_error_debito character(3);
        ALTER TABLE archivo_respuesta_detalle
            ADD COLUMN descripcion_error_debito character(40);
        ALTER TABLE archivo_respuesta_detalle
            ADD COLUMN fecha_origen_venc_debito character(8);

        --alta de un nuevo medio de pago
        INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, se_muestra_alta_manual, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Depósito', 'Depósito', 'S', '', 6);

        --cambio de tipo de dato a 2 columnas de archivo_respuesta
        ALTER TABLE archivo_respuesta
            ALTER COLUMN importe_total_debitos TYPE numeric(15,2) USING (importe_total_debitos::integer);

        ALTER TABLE archivo_respuesta
            ALTER COLUMN cantidad_total_debitos TYPE numeric(7,0) USING (cantidad_total_debitos::integer);

END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 04/04/2022
CREATE OR REPLACE FUNCTION cambios_tablas_parametros_sistema() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'parametros_sistema' and column_name = 'id_parametro') THEN

        -- Sequence: public.sq_id_parametro
        -- DROP SEQUENCE public.sq_id_parametro;
        CREATE SEQUENCE public.sq_id_parametro
            INCREMENT 1
            MINVALUE 1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ALTER TABLE public.sq_id_parametro
            OWNER TO postgres;

        ALTER TABLE parametros_sistema
            ADD COLUMN id_parametro integer;

        UPDATE parametros_sistema SET id_parametro = 1 WHERE parametro = 'numero_establecimiento_debito_visa';
        UPDATE parametros_sistema SET id_parametro = 2 WHERE parametro = 'numero_establecimiento_debito_mc';
        UPDATE parametros_sistema SET id_parametro = 3 WHERE parametro = 'importe_inscripcion_inicial';
        UPDATE parametros_sistema SET id_parametro = 4 WHERE parametro = 'importe_inscripcion_primario';
        UPDATE parametros_sistema SET id_parametro = 5 WHERE parametro = 'criterio_generacion_cuota';
        UPDATE parametros_sistema SET id_parametro = 6 WHERE parametro = 'importe_mensual_cuota';
        UPDATE parametros_sistema SET id_parametro = 7 WHERE parametro = 'importe_inscripcion_anual';
        UPDATE parametros_sistema SET id_parametro = 8 WHERE parametro = 'importe_mensual_cuota_x_grado';

        ALTER TABLE parametros_sistema
            ALTER COLUMN id_parametro SET NOT NULL;

        ALTER TABLE parametros_sistema
            ALTER COLUMN id_parametro SET DEFAULT nextval('sq_id_parametro'::regclass);

        ALTER TABLE parametros_sistema
            ADD PRIMARY KEY (id_parametro);

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 05/04/2022
CREATE OR REPLACE FUNCTION alta_nuevo_parametro_sistema() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM parametros_sistema WHERE parametro = 'permite_pagos_parciales') THEN

        INSERT INTO parametros_sistema (id_parametro, parametro, descripcion, desc_corta, valor, version_publicacion)
        VALUES (NEXTVAL('sq_id_parametro'), 'permite_pagos_parciales', 'Permite el cobro parcial de cuotas', 'Permite cobro parical', 'SI', '1.0.0');

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 06/04/2022
CREATE OR REPLACE FUNCTION spAfectacionArchivoDebito() RETURNS void AS $$
DECLARE idArchivoRespuesta INTEGER;
BEGIN

    --SE CREAN TABLAS TEMPORALES PARA ALMACENAR CONTENIDOS DE LOS ARCHIVOS
    IF EXISTS(SELECT table_name FROM information_schema.tables
              WHERE table_name LIKE 'temparchivordeblimc') THEN
        DROP TABLE tempArchivoRDEBLIMC;
    END IF;

    IF EXISTS(SELECT table_name FROM information_schema.columns
              WHERE table_name='temparchivordebliqc') THEN
        DROP TABLE tempArchivoRDEBLIQC;
    END IF;

    IF EXISTS(SELECT table_name FROM information_schema.columns
              WHERE table_name='temparchivoldebliqd') THEN
        DROP TABLE tempArchivoLDEBLIQD;
    END IF;

/*
IF EXISTS(SELECT table_name FROM information_schema.columns
		WHERE table_name='temparchivordebliqd') THEN
	DROP TABLE tempArchivoRDEBLIQD;
END IF;*/

    CREATE TABLE tempArchivoRDEBLIMC(
        contenido VARCHAR(300)
    );


    CREATE TABLE tempArchivoRDEBLIQC(
        contenido character varying(300)
    );

    CREATE TABLE tempArchivoLDEBLIQD(
        contenido character varying(300)
    );

    /*CREATE TABLE tempArchivoRDEBLIQD(
        contenido character varying(300)
    );*/



--SE COMPIAN LOS DATOS DE LOS ARCHIVOS RECIBIDOS A LAS TABLAS TEMPORALES
    COPY tempArchivoRDEBLIMC (contenido)
        FROM    '/home/ale/Escritorio/RDEBLIMC.txt';
--FROM    'C:\Archivos Debito\recibido\RDEBLIMC.txt';

    COPY tempArchivoRDEBLIQC (contenido)
        FROM    '/home/ale/Escritorio/RDEBLIQC.txt';
--FROM    'C:\Archivos Debito\recibido\RDEBLIQC.txt';

    COPY tempArchivoLDEBLIQD (contenido)
        FROM    '/home/ale/Escritorio/LDEBLIQD.txt';
    --FROM    'C:\Archivos Debito\recibido\LDEBLIQD.txt';

/*COPY tempArchivoRDEBLIQD (contenido)
FROM    'C:\Archivos Debito\recibido\RDEBLIQD.txt';*/


--SE INSERTAN DATOS DE LAS TABLAS TEMPORALES DE CADA ARCHIVO EN TABLAS archivo_respuesta Y archivo_respuesta_detalle

--RDEBLIQC - RDEBLIMC

--RDEBLIMC
--INSERTO EN archivo_respuesta
    INSERT INTO archivo_respuesta (id_marca_tarjeta, id_medio_pago,
                                   nombre_archivo, numero_establecimiento,
                                   cantidad_total_debitos, importe_total_debitos,
                                   usuario_alta, fecha_generacion, cuota)
    SELECT 	   CASE WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIQC' THEN 1
                      WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIMC' THEN 2
                     END AS id_marca_tarjeta,
                 4 AS id_medio_pago,
                 CONCAT(SUBSTRING(contenido from 2 for 8), '_',
                        SUBSTRING(contenido from 30 for 12)) AS nombre_archivo,
                 SUBSTRING(contenido from 20 for 10) AS numero_establecimiento,
                 CAST(SUBSTRING(contenido from 42 for 7) AS INTEGER) AS cantidad_total_debitos,
                 CAST(CONCAT(LPAD(SUBSTRING(contenido from 49 for 15), 13),
                             '.',
                             RPAD(SUBSTRING(contenido from 62 for 2), 13)) AS DECIMAL)
                     AS importe_total_debitos,
                 '25619133' AS usuario_alta,
                 NOW() AS fecha_generacion,
                 CONCAT(SUBSTRING(contenido from 34 for 2),
                        SUBSTRING(contenido from 30 for 4)) AS cuota
    FROM tempArchivoRDEBLIMC
    WHERE lpad(contenido, 1) = '9';

    SELECT  MAX(id_archivo_respuesta) INTO idArchivoRespuesta
    FROM archivo_respuesta;

--INSERTO EN archivo_respuesta_detalle
    INSERT INTO archivo_respuesta_detalle (id_archivo_respuesta, registro, numero_codigo_banco_pagador,
                                           numero_sucursal_banco_pagador, numero_lote,
                                           codigo_transaccion, numero_establecimiento,
                                           numero_tarjeta, id_alumno_cc,
                                           fecha_presentacion, fecha_origen_venc_debito,
                                           importe, id_alumno, codigo_alta_identificador,
                                           cuenta_debito_fondos, estado_movimiento,
                                           rechazo1, descripcion_rechazo1, rechazo2,
                                           descripcion_rechazo2, codigo_error_debito,
                                           descripcion_error_debito, numero_tarjeta_nueva,
                                           fecha_devolucion_respuesta, fecha_pago,
                                           numero_cartera_cliente, contenido, fecha_generacion,
                                           usuario_alta)
    SELECT  idArchivoRespuesta AS id_archivo_respuesta,
            CAST(SUBSTRING(contenido from  1 for  1) AS SMALLINT) AS Registro,
            SUBSTRING(contenido from  2 for  3)  AS numero_codigo_banco_pagador,
            SUBSTRING(contenido from  5 for  3)  AS numero_sucursal_banco_pagador,
            SUBSTRING(contenido from  8 for  4)  AS numero_lote,
            SUBSTRING(contenido from 12 for  4)  AS codigo_transaccion,
            SUBSTRING(contenido from 17 for 10)  AS numero_establecimiento,
            SUBSTRING(contenido from 27 for 16)  AS numero_tarjeta,
            CAST(SUBSTRING(contenido from 43 for  8) AS INTEGER)  AS id_alumno_cc,
            LTRIM(RTRIM(SUBSTRING(contenido from 51 for  6)))  AS fecha_presentacion,
            ''				     AS fecha_origen_venc_debito,
            CAST(CONCAT(LPAD(SUBSTRING(contenido from 63 for 15), 13),
                        '.',
                        RPAD(SUBSTRING(contenido from 76 for 2), 13)) AS DECIMAL)
                AS importe,
            CAST(SUBSTRING(contenido from 95 for 15) AS INTEGER)  AS id_alumno,
            SUBSTRING(contenido from 110 for 1)  AS codigo_alta_identificador,
            SUBSTRING(contenido from 111 for 10) AS cuenta_debito_fondos,
            SUBSTRING(contenido from 130 for 1)  AS estado_movimiento,
            SUBSTRING(contenido from 131 for 2)  AS rechazo1,
            SUBSTRING(contenido from 133 for 29) AS descripcion_rechazo1,
            SUBSTRING(contenido from 162 for 2)  AS rechazo2,
            SUBSTRING(contenido from 164 for 29) AS descripcion_rechazo2,
            ''				     AS codigo_error_debito,
            ''				     AS descripcion_error_debito,
            SUBSTRING(contenido from 209 for 16) AS numero_tarjeta_nueva,
            LTRIM(RTRIM(SUBSTRING(contenido from 225 for 6)))  AS fecha_devolucion_respuesta,
            LTRIM(RTRIM(SUBSTRING(contenido from 231 for 6)))  AS fecha_pago,
            SUBSTRING(contenido from 237 for 2)  AS numero_cartera_cliente,
            contenido,
            NOW() AS fecha_generacion,
            '25619133' AS usuario_alta
    FROM tempArchivoRDEBLIMC
    WHERE lpad(contenido, 1) = '1';




    ----RDEBLIQC
--INSERTO EN archivo_respuesta
    INSERT INTO archivo_respuesta (id_marca_tarjeta, id_medio_pago,
                                   nombre_archivo, numero_establecimiento,
                                   cantidad_total_debitos, importe_total_debitos,
                                   usuario_alta, fecha_generacion, cuota)
    SELECT 	   CASE WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIQC' THEN 1
                      WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIMC' THEN 2
                     END AS id_marca_tarjeta,
                 4 AS id_medio_pago,
                 CONCAT(SUBSTRING(contenido from 2 for 8), '_',
                        SUBSTRING(contenido from 30 for 12)) AS nombre_archivo,
                 SUBSTRING(contenido from 20 for 10) AS numero_establecimiento,
                 CAST(SUBSTRING(contenido from 42 for 7) AS INTEGER) AS cantidad_total_debitos,
                 CAST(CONCAT(LPAD(SUBSTRING(contenido from 49 for 15), 13),
                             '.',
                             RPAD(SUBSTRING(contenido from 62 for 2), 13)) AS DECIMAL)
                     AS importe_total_debitos,
                 '25619133' AS usuario_alta,
                 NOW() AS fecha_generacion,
                 CONCAT(SUBSTRING(contenido from 34 for 2),
                        SUBSTRING(contenido from 30 for 4)) AS cuota
    FROM tempArchivoRDEBLIQC
    WHERE lpad(contenido, 1) = '9';


    SELECT MAX(id_archivo_respuesta) INTO idArchivoRespuesta
    FROM archivo_respuesta;

--INSERTO EN archivo_respuesta_detalle
    INSERT INTO archivo_respuesta_detalle (id_archivo_respuesta, registro, numero_codigo_banco_pagador,
                                           numero_sucursal_banco_pagador, numero_lote,
                                           codigo_transaccion, numero_establecimiento,
                                           numero_tarjeta, id_alumno_cc,
                                           fecha_presentacion,fecha_origen_venc_debito,
                                           importe, id_alumno, codigo_alta_identificador,
                                           cuenta_debito_fondos, estado_movimiento,
                                           rechazo1, descripcion_rechazo1, rechazo2,
                                           descripcion_rechazo2, codigo_error_debito,
                                           descripcion_error_debito, numero_tarjeta_nueva,
                                           fecha_devolucion_respuesta, fecha_pago,
                                           numero_cartera_cliente, contenido, fecha_generacion,
                                           usuario_alta)
    SELECT  idArchivoRespuesta AS id_archivo_respuesta,
            CAST(SUBSTRING(contenido from  1 for  1) AS SMALLINT) AS Registro,
            SUBSTRING(contenido from  2 for  3)  AS numero_codigo_banco_pagador,
            SUBSTRING(contenido from  5 for  3)  AS numero_sucursal_banco_pagador,
            SUBSTRING(contenido from  8 for  4)  AS numero_lote,
            SUBSTRING(contenido from 12 for  4)  AS codigo_transaccion,
            SUBSTRING(contenido from 17 for 10)  AS numero_establecimiento,
            SUBSTRING(contenido from 27 for 16)  AS numero_tarjeta,
            CAST(SUBSTRING(contenido from 43 for  8) AS INTEGER)  AS id_alumno_cc,
            LTRIM(RTRIM(SUBSTRING(contenido from 51 for  6)))  AS fecha_presentacion,
            ''					AS fecha_origen_venc_debito,
            CAST(CONCAT(LPAD(SUBSTRING(contenido from 63 for 15), 13),
                        '.',
                        RPAD(SUBSTRING(contenido from 76 for 2), 13)) AS DECIMAL)
                AS importe,
            CAST(SUBSTRING(contenido from 95 for 15) AS INTEGER)  AS id_alumno,
            SUBSTRING(contenido from 110 for 1)  AS codigo_alta_identificador,
            SUBSTRING(contenido from 111 for 10) AS cuenta_debito_fondos,
            SUBSTRING(contenido from 130 for 1)  AS estado_movimiento,
            SUBSTRING(contenido from 131 for 2)  AS rechazo1,
            SUBSTRING(contenido from 133 for 29) AS descripcion_rechazo1,
            SUBSTRING(contenido from 162 for 2)  AS rechazo2,
            SUBSTRING(contenido from 164 for 29) AS descripcion_rechazo2,
            ''				     AS codigo_error_debito,
            ''				     AS descripcion_error_debito,
            SUBSTRING(contenido from 209 for 16) AS numero_tarjeta_nueva,
            LTRIM(RTRIM(SUBSTRING(contenido from 225 for 6))) AS fecha_devolucion_respuesta,
            LTRIM(RTRIM(SUBSTRING(contenido from 231 for 6))) AS fecha_pago,
            SUBSTRING(contenido from 237 for 2) AS numero_cartera_cliente,
            contenido,
            NOW() AS fecha_generacion,
            '25619133' AS usuario_alta
    FROM tempArchivoRDEBLIQC
    WHERE lpad(contenido, 1) = '1';



    ----RDEBLIQD - LDEBLIQD
--=====================
--RDEBLIQD
--========
--INSERTO EN archivo_respuesta
--============================
/*INSERT INTO archivo_respuesta (id_marca_tarjeta, id_medio_pago,
				nombre_archivo, numero_establecimiento,
				cantidad_total_debitos, importe_total_debitos,
				usuario_alta, fecha_generacion)
SELECT 	   CASE WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIQD' THEN 1
		WHEN SUBSTRING(contenido from 2 for 8) = 'LDEBLIQD' THEN 1
	   END AS id_marca_tarjeta,
	   3 AS id_medio_pago,
	   SUBSTRING(contenido from 2 for 8) AS nombre_archivo,
	   SUBSTRING(contenido from 20 for 10) AS numero_establecimiento,
	   CAST(SUBSTRING(contenido from 42 for 7) AS INTEGER) AS cantidad_total_debitos,
	   CAST(CONCAT(LPAD(SUBSTRING(contenido from 49 for 15), 13),
		'.',
	       RPAD(SUBSTRING(contenido from 62 for 2), 13)) AS DECIMAL)
	   AS importe_total_debitos,
	   '25619133' AS usuario_alta,
	   NOW() AS fecha_generacion
FROM tempArchivoRDEBLIQD
WHERE lpad(contenido, 1) = '9';


SELECT MAX(id_archivo_respuesta) INTO idArchivoRespuesta
FROM archivo_respuesta;

--INSERTO EN archivo_respuesta_detalle
INSERT INTO archivo_respuesta_detalle (id_archivo_respuesta, registro, numero_codigo_banco_pagador,
				     numero_sucursal_banco_pagador, numero_lote,
				     codigo_transaccion, numero_establecimiento,
				     numero_tarjeta, id_alumno_cc,
				     fecha_presentacion, fecha_origen_venc_debito,
				     importe, id_alumno, codigo_alta_identificador,
				     cuenta_debito_fondos, estado_movimiento,
				     rechazo1, descripcion_rechazo1, rechazo2,
				     descripcion_rechazo2, codigo_error_debito,
				     descripcion_error_debito, numero_tarjeta_nueva,
				     fecha_devolucion_respuesta, fecha_pago,
				     numero_cartera_cliente, contenido, fecha_generacion,
				     usuario_alta)
SELECT  idArchivoRespuesta AS id_archivo_respuesta,
	CAST(SUBSTRING(contenido from  1 for  1) AS SMALLINT) AS Registro,
	''  				     AS numero_codigo_banco_pagador,
	''  				     AS numero_sucursal_banco_pagador,
	''  				     AS numero_lote,
	''  				     AS codigo_transaccion,
	''  				     AS numero_establecimiento,
	SUBSTRING(contenido from 2  for 16)  AS numero_tarjeta,
	CAST(SUBSTRING(contenido from 21 for  8) AS INTEGER)  AS id_alumno_cc,
	''				     AS fecha_presentacion,
	LTRIM(RTRIM(SUBSTRING(contenido from 29 for  8)))  AS fecha_origen_venc_debito,
	CAST(CONCAT(LPAD(SUBSTRING(contenido from 41 for 15), 13),
		'.',
	       RPAD(SUBSTRING(contenido from 54 for 2), 13)) AS DECIMAL)
	AS importe,
	CAST(SUBSTRING(contenido from 56 for 15) AS INTEGER)  AS id_alumno,
	SUBSTRING(contenido from 71 for 1)   AS codigo_alta_identificador,
	''  				     AS cuenta_debito_fondos,
	''  				     AS estado_movimiento,
	''  				     AS rechazo1,
	''  				     AS descripcion_rechazo1,
	''  				     AS rechazo2,
	''  				     AS descripcion_rechazo2,
	SUBSTRING(contenido from 101 for 3)  AS codigo_error_debito,
	SUBSTRING(contenido from 104 for 40) AS descripcion_error_debito,
	''  				     AS numero_tarjeta_nueva,
	''  				     AS fecha_devolucion_respuesta,
	''  				     AS fecha_pago,
	''  				     AS numero_cartera_cliente,
	contenido,
	NOW() 				     AS fecha_generacion,
	'25619133' 			     AS usuario_alta
FROM tempArchivoRDEBLIQD
WHERE lpad(contenido, 1) = '1';
*/

--LDEBLIQD
--========
--INSERTO EN archivo_respuesta
--============================
    INSERT INTO archivo_respuesta (id_marca_tarjeta, id_medio_pago,
                                   nombre_archivo, numero_establecimiento,
                                   cantidad_total_debitos, importe_total_debitos,
                                   usuario_alta, fecha_generacion, cuota)
    SELECT 	   CASE WHEN SUBSTRING(contenido from 2 for 8) = 'RDEBLIQD' THEN 1
                      WHEN SUBSTRING(contenido from 2 for 8) = 'LDEBLIQD' THEN 1
                     END AS id_marca_tarjeta,
                 3 AS id_medio_pago,
                 CONCAT(SUBSTRING(contenido from 2 for 8), '_',
                        SUBSTRING(contenido from 30 for 12)) AS nombre_archivo,
                 SUBSTRING(contenido from 20 for 10) AS numero_establecimiento,
                 CAST(SUBSTRING(contenido from 42 for 7) AS INTEGER) AS cantidad_total_debitos,
                 CAST(CONCAT(LPAD(SUBSTRING(contenido from 49 for 15), 13),
                             '.',
                             RPAD(SUBSTRING(contenido from 62 for 2), 13)) AS DECIMAL)
                     AS importe_total_debitos,
                 '25619133' AS usuario_alta,
                 NOW() AS fecha_generacion,
                 CONCAT(SUBSTRING(contenido from 34 for 2),
                        SUBSTRING(contenido from 30 for 4)) AS cuota
    FROM tempArchivoLDEBLIQD
    WHERE lpad(contenido, 1) = '9';


    SELECT MAX(id_archivo_respuesta) INTO idArchivoRespuesta
    FROM archivo_respuesta;

--INSERTO EN archivo_respuesta_detalle
    INSERT INTO archivo_respuesta_detalle (id_archivo_respuesta, registro, numero_codigo_banco_pagador,
                                           numero_sucursal_banco_pagador, numero_lote,
                                           codigo_transaccion, numero_establecimiento,
                                           numero_tarjeta, id_alumno_cc,
                                           fecha_presentacion, fecha_origen_venc_debito,
                                           importe, id_alumno, codigo_alta_identificador,
                                           cuenta_debito_fondos, estado_movimiento,
                                           rechazo1, descripcion_rechazo1, rechazo2,
                                           descripcion_rechazo2, codigo_error_debito,
                                           descripcion_error_debito, numero_tarjeta_nueva,
                                           fecha_devolucion_respuesta, fecha_pago,
                                           numero_cartera_cliente, contenido, fecha_generacion,
                                           usuario_alta)
    SELECT  idArchivoRespuesta AS id_archivo_respuesta,
            CAST(SUBSTRING(contenido from  1 for  1) AS SMALLINT) AS Registro,
            ''  				     AS numero_codigo_banco_pagador,
            ''  				     AS numero_sucursal_banco_pagador,
            ''  				     AS numero_lote,
            ''  				     AS codigo_transaccion,
            ''  				     AS numero_establecimiento,
            SUBSTRING(contenido from 2  for 16)  AS numero_tarjeta,
            CAST(SUBSTRING(contenido from 21 for  8) AS INTEGER)  AS id_alumno_cc,
            ''				     AS fecha_presentacion,
            LTRIM(RTRIM(SUBSTRING(contenido from 29 for  8)))  AS fecha_origen_venc_debito,
            CAST(CONCAT(LPAD(SUBSTRING(contenido from 41 for 15), 13),
                        '.',
                        RPAD(SUBSTRING(contenido from 54 for 2), 13)) AS DECIMAL)
                AS importe,
            CAST(SUBSTRING(contenido from 56 for 15) AS INTEGER)  AS id_alumno,
            SUBSTRING(contenido from 71 for 1)   AS codigo_alta_identificador,
            ''  				     AS cuenta_debito_fondos,
            ''  				     AS estado_movimiento,
            ''  				     AS rechazo1,
            ''  				     AS descripcion_rechazo1,
            ''  				     AS rechazo2,
            ''  				     AS descripcion_rechazo2,
            SUBSTRING(contenido from 101 for 3)  AS codigo_error_debito,
            SUBSTRING(contenido from 104 for 40) AS descripcion_error_debito,
            ''  				     AS numero_tarjeta_nueva,
            ''  				     AS fecha_devolucion_respuesta,
            ''  				     AS fecha_pago,
            ''  				     AS numero_cartera_cliente,
            contenido,
            NOW() 				     AS fecha_generacion,
            '25619133' 			     AS usuario_alta
    FROM tempArchivoLDEBLIQD
    WHERE lpad(contenido, 1) = '1';


END;
$$ LANGUAGE plpgsql;


--Alejandro feature/alta-manual-pagos 22/04/2022
CREATE OR REPLACE FUNCTION alta_nuevos_parametros_sistema() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM parametros_sistema WHERE parametro = 'muestra_nombre_con_legajo') THEN

        INSERT INTO parametros_sistema (id_parametro, parametro, descripcion, desc_corta, valor, version_publicacion)
        VALUES (NEXTVAL('sq_id_parametro'), 'muestra_nombre_con_legajo', 'Muestra el nombre del alumno con legajo', 'Muestra nombre con legajo', 'SI', '1.0.0');
        INSERT INTO parametros_sistema (id_parametro, parametro, descripcion, desc_corta, valor, version_publicacion)
        VALUES (NEXTVAL('sq_id_parametro'), 'nombre_institucion', 'Nombre Institución', 'Nombre Institución', 'Escuela Sagrada Familia', '1.0.0');

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 25/04/2022
CREATE OR REPLACE FUNCTION cambios_tabla_transaccion_cuenta_corriente() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'transaccion_cuenta_corriente' and column_name = 'codigo_rechazo2') THEN

        --quito la fk con id_motivo_rechazo
        ALTER TABLE transaccion_cuenta_corriente
            DROP CONSTRAINT id_motivo_rechazo;
        --quito la columna id_motivo_rechazo
        ALTER TABLE transaccion_cuenta_corriente
            DROP COLUMN id_motivo_rechazo;
        --agrego las nuevas columnas
        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN id_motivo_rechazo1 INTEGER;
        ALTER TABLE transaccion_cuenta_corriente
            ADD COLUMN id_motivo_rechazo2 INTEGER;
        --agrego las nuevas fk
        ALTER TABLE transaccion_cuenta_corriente
            ADD CONSTRAINT id_motivo_rechazo1 FOREIGN KEY (id_motivo_rechazo1)
                REFERENCES public.motivo_rechazo (id_motivo_rechazo) MATCH FULL
                ON UPDATE NO ACTION ON DELETE NO ACTION;
        ALTER TABLE transaccion_cuenta_corriente
            ADD CONSTRAINT id_motivo_rechazo2 FOREIGN KEY (id_motivo_rechazo2)
                REFERENCES public.motivo_rechazo (id_motivo_rechazo) MATCH FULL
                ON UPDATE NO ACTION ON DELETE NO ACTION;

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 25/04/2022
CREATE OR REPLACE FUNCTION alta_parametro_sistema_ingresa_importe_en_generacion_cargos() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM parametros_sistema WHERE parametro = 'ingresa_importe_en_generacion_cargos') THEN

        INSERT INTO parametros_sistema (id_parametro, parametro, descripcion, desc_corta, valor, version_publicacion)
        VALUES (NEXTVAL('sq_id_parametro'), 'ingresa_importe_en_generacion_cargos', 'Permite el ingreso del importe en la generación de cargos', 'Ingresa importe en generación cargos', 'SI', '1.0.0');

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 05/05/2022
CREATE OR REPLACE FUNCTION cambio_campos_archivo_respuesta() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'archivo_respuesta' and column_name = 'fecha_generacion_archivo_respuesta') THEN

        ALTER TABLE public.archivo_respuesta DISABLE TRIGGER tauditoria_archivo_respuesta;

        ALTER TABLE public_auditoria.logs_archivo_respuesta
            RENAME COLUMN fecha_generacion TO fecha_generacion_archivo_respuesta;
        ALTER TABLE public_auditoria.logs_archivo_respuesta
            DROP COLUMN fecha_generacion_archivo_respuesta;

        ALTER TABLE public_auditoria.logs_archivo_respuesta
            ADD COLUMN fecha_generacion_archivo_respuesta DATE;

        ALTER TABLE public.archivo_respuesta
            RENAME COLUMN fecha_generacion TO fecha_generacion_archivo_respuesta;
        ALTER TABLE public.archivo_respuesta
            DROP COLUMN fecha_generacion_archivo_respuesta;

        ALTER TABLE public.archivo_respuesta
            ADD COLUMN fecha_generacion_archivo_respuesta DATE;

        UPDATE public.archivo_respuesta
        SET fecha_generacion_archivo_respuesta = CAST(SUBSTRING(nombre_archivo, 10, 8) AS DATE)
        WHERE 1=1;

        UPDATE public_auditoria.logs_archivo_respuesta
        SET fecha_generacion_archivo_respuesta = CAST(SUBSTRING(nombre_archivo, 10, 8) AS DATE)
        WHERE 1=1;

        ALTER TABLE public.archivo_respuesta ENABLE TRIGGER tauditoria_archivo_respuesta;

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 13/05/2022
CREATE OR REPLACE FUNCTION agregar_campos_transaccion_cuenta_corriente() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'transaccion_cuenta_corriente' and column_name = 'codigo_error_debito') THEN

        ALTER TABLE public.transaccion_cuenta_corriente
            ADD COLUMN codigo_error_debito varchar(3);

        ALTER TABLE public.transaccion_cuenta_corriente
            ADD COLUMN descripcion_error_debito varchar(40);

        --quito la fk con id_motivo_rechazo1
        ALTER TABLE public.transaccion_cuenta_corriente
            DROP CONSTRAINT id_motivo_rechazo1;

        --quito la fk con id_motivo_rechazo2
        ALTER TABLE public.transaccion_cuenta_corriente
            DROP CONSTRAINT id_motivo_rechazo2;

        ALTER TABLE public.transaccion_cuenta_corriente DISABLE TRIGGER tauditoria_transaccion_cuenta_corriente;

        --cambio el tipo de dato del campo fecha_respuesta_prisma
        ALTER TABLE public.transaccion_cuenta_corriente
            ALTER COLUMN fecha_respuesta_prisma TYPE varchar(8);

        ALTER TABLE public_auditoria.logs_transaccion_cuenta_corriente
            ALTER COLUMN fecha_respuesta_prisma TYPE varchar(8);

        ALTER TABLE public.transaccion_cuenta_corriente ENABLE TRIGGER tauditoria_transaccion_cuenta_corriente;

    END IF;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 14/07/2022
CREATE OR REPLACE FUNCTION actualizar_usuario_a_tutores() RETURNS VOID AS
$$
BEGIN
    UPDATE persona
    SET usuario = (SELECT ptd.numero as identificacion
                   FROM persona p
                            INNER JOIN (SELECT distinct(p2.id_persona) as id_persona, es_alumno
                                        FROM persona p2
                                                 INNER JOIN persona_allegado pa on p2.id_persona = pa.id_persona and pa.tutor = 'S'
                                        WHERE es_alumno = 'N') p2 on p2.id_persona = p.id_persona
                            LEFT OUTER JOIN (persona_tipo_documento ptd JOIN tipo_documento td on ptd.id_tipo_documento = td.id_tipo_documento)
                                            ON ptd.id_persona = p.id_persona AND td.jerarquia = (SELECT MIN(X1.jerarquia)
                                                                                                 FROM tipo_documento X1
                                                                                                    ,persona_tipo_documento X2
                                                                                                 WHERE X1.id_tipo_documento = X2.id_tipo_documento
                                                                                                   AND X2.id_persona = p.id_persona)
                   WHERE p.id_persona = p.id_persona
                   ORDER BY p2.id_persona)
    WHERE 1=1;
END;
$$ LANGUAGE 'plpgsql';


--Alejandro feature/alta-manual-pagos 13/10/2022
CREATE OR REPLACE FUNCTION insertar_jerarquia_grado() RETURNS VOID AS
$$
BEGIN
    IF NOT EXISTS ( SELECT '' FROM information_schema.columns WHERE table_name = 'grado' and column_name = 'jerarquia') THEN

        ALTER TABLE public.grado
            ADD COLUMN jerarquia integer;

        UPDATE grado SET jerarquia = 1 WHERE id_grado = 1;
        UPDATE grado SET jerarquia = 2 WHERE id_grado = 2;
        UPDATE grado SET jerarquia = 3 WHERE id_grado = 3;
        UPDATE grado SET jerarquia = 4 WHERE id_grado = 4;
        UPDATE grado SET jerarquia = 5 WHERE id_grado = 5;
        UPDATE grado SET jerarquia = 6 WHERE id_grado = 6;
        UPDATE grado SET jerarquia = 7 WHERE id_grado = 7;
        UPDATE grado SET jerarquia = 8 WHERE id_grado = 8;

    END IF;
END;
$$ LANGUAGE 'plpgsql';