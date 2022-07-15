<?php
class ci_cuenta_corriente extends ci_alta_manual_pagos //gestionescuelas_ext_ci
{
    protected $s__datos_cuenta_corriente = array();

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_cuenta_corriente = $persona->get_datos_cuenta_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__perfil_funcional)) {
            if ((in_array('tutor', $this->s__perfil_funcional))) {
                if (isset($this->s__id_persona)) {
                    $obj_persona = new persona($this->s__id_persona);
                    $alumnos_vinculados = $obj_persona->get_alumnos_vinculados();
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

    //---- filtro -----------------------------------------------------------------------

    public function conf__filtro($form)
    {
        if (!isset($this->s__datos_filtro)) {
            $fecha = new fecha();
            $fechas_defecto = array('fecha_desde' => date("Y-01-01"), 'fecha_hasta' => $fecha->get_fecha_db());
            $form->set_datos_defecto($fechas_defecto);
        } else {
            $form->set_datos($this->s__datos_filtro);
        }
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

    function conf__cuadro_cuenta_corriente($cuadro)
    {
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }
        /*if (!empty($this->s__datos_cuenta_corriente)) {
            $cuadro->set_datos($this->s__datos_cuenta_corriente);
        }*/
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['id_persona'] = $this->s__alumno_editar;
            $cuadro->set_datos(dao_personas::get_deuda_por_alumno($this->s__datos_filtro));
        }
    }

}