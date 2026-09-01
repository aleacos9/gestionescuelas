<?php
class cn_actualizar_importe_cuotas extends gestionescuelas_cn
{
    protected $datos_formulario;
    protected $cantidad_actualizadas = 0;
    protected $importe_vigente;

    public function set_datos_formulario($datos)
    {
        $this->datos_formulario = $datos;
    }

    function evt__procesar_especifico()
    {
        $this->validar();
        $this->actualizar_importe_cuotas_adeudadas();
    }

    /**
     * El importe vigente se resuelve y valida ANTES de tocar nada.
     *
     * El UPDATE embebia `SELECT CAST(valor AS numeric) FROM parametros_sistema`
     * dentro de si mismo, y ese subselect no distingue un importe de un
     * disparate. `catalogo_de_parametros()` solo controla que el parametro
     * exista y no sea NULL, asi que un cero cargado por error en Parametros del
     * sistema pasaba entero y escribia importe = 0 en todas las cuotas
     * alcanzadas: la deuda de esas familias desaparecia, sin un error, sin un
     * aviso, y sin forma de deshacerlo desde el sistema. Un valor negativo las
     * dejaba a favor.
     *
     * Verificado contra la base: con el parametro en '0' el subselect devuelve 0
     * y el UPDATE lo escribe. Con el parametro vacio Postgres corta con
     * "invalid input syntax for type numeric", y si falta o es NULL el CI ya no
     * deja llegar hasta aca.
     */
    public function validar()
    {
        // El trim es para no rechazar un valor bueno por un espacio invisible
        // cargado sin querer: es lo mas dificil de diagnosticar desde la pantalla.
        $valor = trim(dao_consultas::catalogo_de_parametros('importe_mensual_cuota'));

        if (! is_numeric($valor) || (float) $valor <= 0) {
            throw new toba_error(
                "El valor de cuota configurado no sirve para actualizar la deuda: "
                . "el parametro 'importe_mensual_cuota' vale '{$valor}'. "
                . "Tiene que ser un importe mayor a cero. "
                . "Corregilo en Administracion -> Parametros del sistema y volve a intentar."
            );
        }

        $this->importe_vigente = (float) $valor;
    }

    private function actualizar_importe_cuotas_adeudadas()
    {
        // El criterio vive en un solo lugar, compartido con el listado previo del cuadro.
        // Ver dao_consultas::where_cuotas_actualizables().
        $where = dao_consultas::where_cuotas_actualizables($this->datos_formulario);

        // Ya paso por validar(): es un numero mayor a cero. Se formatea con
        // sprintf para que un float no entre al SQL en notacion cientifica.
        $importe = sprintf('%.2f', $this->importe_vigente);

        $sql = "WITH cuotas_a_actualizar AS (
                        SELECT tcc.id_transaccion_cc
                              ,tcc.id_alumno_cc
                        FROM transaccion_cuenta_corriente tcc
                            JOIN alumno_cuenta_corriente acc ON tcc.id_alumno_cc = acc.id_alumno_cc
                        WHERE $where
                    )
                    UPDATE transaccion_cuenta_corriente tcc
                    SET
                        importe = {$importe},
                        importe_actualizado = true,
                        fecha_ultima_modificacion = current_timestamp,
                        fecha_actualizacion_importe = current_timestamp
                    FROM cuotas_a_actualizar c
                        JOIN alumno_cuenta_corriente acc ON acc.id_alumno_cc = c.id_alumno_cc
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
