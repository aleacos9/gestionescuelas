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
    END IF;
END;
$$ LANGUAGE 'plpgsql';
