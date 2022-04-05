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