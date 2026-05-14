<?php
class cn_actualizar_importe_cuotas extends gestionescuelas_cn
{
    protected $datos_formulario;
    protected $cantidad_actualizadas = 0;

    public function set_datos_formulario($datos)
    {
        $this->datos_formulario = $datos;
    }

    function evt__procesar_especifico()
    {
        $this->validar();
        $this->actualizar_importe_cuotas_adeudadas();
    }

    public function validar()
    {
    }

    private function actualizar_importe_cuotas_adeudadas()
    {
        $where = '';

        if (isset($this->datos_formulario)) {
            if (isset($this->datos_formulario['cuota']) && isset($this->datos_formulario['anio'])) {
                $mes = str_pad($this->datos_formulario['cuota'], 2, '0', STR_PAD_LEFT); // asegura que tenga 2 dígitos
                $anio = $this->datos_formulario['anio'];
                $cuota_completa = $mes . $anio;

                if ($this->datos_formulario['cuota'] == 11) {
                    $where .= " AND (acc.cuota = '{$cuota_completa}' OR acc.cuota = '')";
                } else {
                    $where .= " AND acc.cuota = '{$cuota_completa}'";
                }
            }
        }

        $sql = "WITH cuotas_a_actualizar AS (
                        SELECT tcc.id_transaccion_cc
                        FROM transaccion_cuenta_corriente tcc
                            JOIN alumno_cuenta_corriente acc ON tcc.id_alumno_cc = acc.id_alumno_cc
                        WHERE acc.id_cargo_cuenta_corriente = 2
                            AND tcc.id_estado_cuota = 1
                            AND tcc.importe > 0
                            AND tcc.numero_comprobante IS NULL
                            AND tcc.numero_lote IS NULL
                            AND tcc.numero_autorizacion IS NULL
                            AND tcc.id_medio_pago IS NULL
                            AND tcc.id_marca_tarjeta IS NULL
                            AND tcc.punto_venta IS NULL
                            AND tcc.comprobante_tipo IS NULL
                            AND tcc.comprobante_numero IS NULL
                            AND LEAST(
                                    COALESCE(tcc.fecha_transaccion, now()),
                                    COALESCE(acc.fecha_generacion_cc, now())
                                ) < date_trunc('month', current_date)
                            AND (
                                tcc.fecha_actualizacion_importe IS NULL
                                OR tcc.fecha_actualizacion_importe < date_trunc('month', current_date - interval '1 month')
                            )
                            -- CORREGIDO: no debe existir NINGUN pago
                            AND NOT EXISTS (
                                SELECT 1
                                FROM transaccion_cuenta_corriente pagos
                                WHERE pagos.id_alumno_cc = tcc.id_alumno_cc
                                  AND pagos.id_transaccion_cc <> tcc.id_transaccion_cc
                                  AND pagos.importe < 0
                            )
                            $where
                    )
                    UPDATE transaccion_cuenta_corriente tcc
                    SET
                        importe = (
                            SELECT CAST(valor AS numeric)
                            FROM parametros_sistema
                            WHERE parametro = 'importe_mensual_cuota'
                            LIMIT 1
                        ),
                        importe_actualizado = true,
                        fecha_ultima_modificacion = current_timestamp,
                        fecha_actualizacion_importe = current_timestamp
                    FROM cuotas_a_actualizar c
                        JOIN alumno_cuenta_corriente acc ON tcc.id_alumno_cc = acc.id_alumno_cc
                        JOIN alumno a ON acc.id_alumno = a.id_alumno
                        JOIN persona p ON a.id_persona = p.id_persona
                    WHERE tcc.id_transaccion_cc = c.id_transaccion_cc
                    RETURNING p.nombres,
                              p.apellidos,
                              acc.descripcion
                ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $datos = toba::db()->consultar($sql);
        $this->cantidad_actualizadas = count($datos);

        $detalle = '';
        foreach ($datos as $fila) {
            $detalle .= "- {$fila['apellidos']}, {$fila['nombres']} - {$fila['descripcion']} ({$this->get_mes_anterior()})<br>";
        }

        $mensaje = "Se actualizaron {$this->cantidad_actualizadas} cuotas.<br><br><strong>Detalle:</strong><br>{$detalle}";
        toba::notificacion()->agregar($mensaje, "info");
    }

    private function get_mes_anterior()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = new DateTime('first day of last month');
        return $meses[(int)$fecha->format('n')];
    }
}
