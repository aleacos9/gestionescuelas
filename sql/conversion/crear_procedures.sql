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

END IF;
END;
$$ LANGUAGE 'plpgsql';