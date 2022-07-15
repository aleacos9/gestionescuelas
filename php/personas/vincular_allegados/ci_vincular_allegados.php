<?php
class ci_vincular_allegados extends ci_abm_personas
{
    //---- Funciones ---------------------------------------------------------------------

    /*
     * Retorna los tipos de allegados
     */
    public function get_tipos_allegados_ci()
    {
        return dao_consultas::get_tipos_allegados();
    }

    /*
    * Retorna los estudios alcanzados
    */
    public function get_estudios_alcanzados_ci()
    {
        return dao_consultas::get_estudios_alcanzados();
    }

    /*
    * Retorna las ocupaciones
    */
    public function get_ocupaciones_ci()
    {
        return dao_consultas::get_ocupaciones();
    }

    /*
     * Retorna los nombres de las personas que NO son alumnos
     */
    public function get_nombres_personas_ci()
    {
        $filtro['solo_alumnos'] = false;
        return dao_consultas::get_nombres_persona($filtro);
    }

    public function cargar_datos()
    {
        if (!empty($this->s__persona_editar)) {
            $persona = new persona($this->s__persona_editar);
            $this->s__datos_allegados = $persona->get_persona_allegados();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_allegados($this->s__datos_allegados);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['solo_alumnos'] = true;
            $this->s__datos_filtro['vincular_allegados'] = true;
        }
        parent::conf__cuadro($cuadro);
    }

    function evt__cuadro__seleccion($seleccion)
    {
        $this->s__persona_editar = $seleccion['id_persona'];
        $this->s__alumno_editar = $seleccion['id_alumno'];
        $this->set_pantalla('edicion');
        $this->cargar_datos();
    }

    //---- formulario_ml ------------------------------------------------------------------

    function conf__formulario_ml($form_ml)
    {
        if ($this->s__alumno_editar) {
            $form_ml->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }

        if (!empty($this->s__datos_allegados)) {
            foreach (array_keys($this->s__datos_allegados) as $id) {
                if ($this->s__datos_allegados[$id]['apex_ei_analisis_fila'] == 'B') {
                    unset($this->s__datos_allegados[$id]);
                }
            }
            $form_ml->set_datos($this->s__datos_allegados);
        }
    }

    function evt__formulario_ml__modificacion($datos)
    {
        $this->s__datos_allegados = $datos;
        foreach (array_keys($this->s__datos_allegados) as $allegado) {
            if (empty($this->s__datos_allegados[$allegado]['id_alumno'])) {
                $this->s__datos_allegados[$allegado]['id_alumno'] = $this->s__alumno_editar;
            }
        }
    }

    //---- Eventos ----------------------------------------------------------------------

    public function evt__procesar()
    {
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        if (strtolower($rta) == 'ok') {
            $this->limpiar_memoria();
            $this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
            $this->controlador()->set_pantalla("seleccion");
        } else {
            throw new toba_error("Ha ocurrido un error inesperado: $rta");
        }
    }

    public function evt__procesar_y_continuar()
    {
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        if (strtolower($rta) == 'ok') {
            //$this->limpiar_memoria();
            //$this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
        } else {
            throw new toba_error("Ha ocurrido un error inesperado: $rta");
        }
    }

    public function evt__cancelar()
    {
        $this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
        $this->controlador()->set_pantalla('seleccion');
    }

    public function validar()
    {
        //este método puede q no sea necesario, ya que puedo hacer todo desde el validar del cn
    }

}