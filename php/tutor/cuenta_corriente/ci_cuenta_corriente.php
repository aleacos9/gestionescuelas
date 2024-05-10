<?php
class ci_cuenta_corriente extends gestionescuelas_ext_ci
{
    protected $s__datos_cuenta_corriente = array();
    protected $s__datos_cuadro_cuenta_corriente = array();

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_cuenta_corriente = $persona->get_datos_cuenta_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    //---- filtro -----------------------------------------------------------------------

    public function conf__filtro($form)
    {
        if (!isset($this->s__datos_filtro)) {
            $fecha = new fecha();
            $fecha->set_timestamp($fecha->get_fecha_desplazada("+1"));

            if (dao_consultas::catalogo_de_parametros("fecha_desde_listado_cuenta_corriente")) {
                $anio = dao_consultas::catalogo_de_parametros("fecha_desde_listado_cuenta_corriente");
                $fecha_desde = date($anio.'-01-01');
            } else {
                $fecha_desde = date("Y-01-01");
            }
            $this->s__datos_filtro = array('fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha->get_fecha_db());
        }
        $form->set_datos($this->s__datos_filtro);
    }

    public function evt__filtro__filtrar($datos)
    {
        $fecha = new fecha();

        if (empty($datos['fecha_desde'])) {
            $datos['fecha_desde'] = $fecha->get_fecha_db();
        }

        //Valida las fechas desde y hasta
        $fecha->set_fecha($datos['fecha_hasta']);
        if ($fecha->es_menor_que($datos['fecha_desde'])) {
            throw new excepcion_pilaga("La 'fecha desde' debe ser menor o igual a la 'fecha hasta'");
        }
        $this->s__datos_filtro = $datos;
    }

    public function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if ($this->controlador()->get_id()[1] == '82000055') {
            $cuadro->eliminar_columnas(array('saldo_actualizado'));
        }
        if (isset($this->s__perfil_funcional)) {
            if ((in_array('tutor', $this->s__perfil_funcional))) {
                if (isset($this->s__id_persona)) {
                    $obj_persona = new persona($this->s__id_persona);
                    $alumnos_vinculados = $obj_persona->get_alumnos_vinculados();
                    //Debo recorrer cada alumno asociado a la persona y obtener el saldo de la deuda corriente
                    if (isset($alumnos_vinculados)) {
                        foreach (array_keys($alumnos_vinculados) as $alumno) {
                            $obj_alumno = new persona($alumnos_vinculados[$alumno]['id_persona_alumno']);
                            $saldo_deuda_corriente = $obj_alumno->get_saldo_deuda_corriente();
                            $alumnos_vinculados[$alumno]['saldo'] = $saldo_deuda_corriente['saldo'];
                        }
                    }
                    $cuadro->set_datos($alumnos_vinculados);
                }
            }
        }
    }

    function evt__cuadro__seleccion($seleccion)
    {
        $this->s__alumno_editar = $seleccion['id_persona_alumno'];
        $this->set_pantalla('edicion');
        $this->cargar_datos();
    }

    //---- cuadro_cuenta_corriente ------------------------------------------------------

    function conf__cuadro_cuenta_corriente($cuadro)
    {
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['id_persona'] = $this->s__alumno_editar;
            $this->s__datos_cuadro_cuenta_corriente = dao_personas::get_deuda_por_alumno($this->s__datos_filtro);
            $cuadro->set_datos($this->s__datos_cuadro_cuenta_corriente);
        }
    }

    function conf_evt__cuadro_cuenta_corriente__descargar_comprobante($evento, $fila)
    {
        parent::conf_evt__cuadro_cuenta_corriente__descargar_comprobante($evento, $fila);
    }
}