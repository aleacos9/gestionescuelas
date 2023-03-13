<?php
class ci_abm_parametros_sistema extends gestionescuelas_ext_ci
{
    //---- cuadro -----------------------------------------------------------------------

    public function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['aplica_perfil_datos'] = false;
            if (isset($this->s__id_perfil_datos)) {
                $this->s__datos_filtro['aplica_perfil_datos'] = true;
            }
            $datos = $this->dep('datos')->get_listado($this->s__datos_filtro);
            $cuadro->set_datos($datos);
        }
    }

    public function evt__cuadro__seleccion($datos)
    {
        $this->dep('datos')->cargar($datos);
    }

    //---- formulario -------------------------------------------------------------------

    public function conf__formulario($form)
    {
        if ($this->dep('datos')->esta_cargada()) {
            $form->set_datos($this->dep('datos')->get());
        }

        /*if (!in_array('admin', $this->s__perfil_funcional)) {
            $form->evento('modificacion')->ocultar();
            $form->evento('cancelar')->ocultar();
        }*/
    }

    public function evt__formulario__alta($datos)
    {
        //Asocio el tipo de proyecto según el perfil del usuario
        if ($this->s__id_perfil_datos) {
            $datos['id_tipo_proyecto'] = $this->s__id_perfil_datos;
        }
        /*if (($datos['parametro'] == 'usa_roles_x_perfil_datos') or ($datos['parametro'] == 'usa_estados_x_perfil_datos')) {
            $this->validar_dato($datos);
        }*/
        $this->dep('datos')->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
        toba::notificacion()->agregar('El alta del parámetro: <b>'.$datos['parametro'].'</b> fue realizado con éxito.', "info");
    }

    public function evt__formulario__modificacion($datos)
    {
        /*if (($datos['parametro'] == 'usa_roles_x_perfil_datos') or ($datos['parametro'] == 'usa_estados_x_perfil_datos')) {
            $this->validar_dato($datos);
        }*/
        $this->dep('datos')->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
        toba::notificacion()->agregar('La modificación del parámetro: <b>'.$datos['parametro'].'</b> fue realizada con éxito.', "info");
    }

    public function evt__formulario__baja($datos)
    {
        //Verifico si el parametro se puede borrar
        consultas_filtros::get_parametros_no_borrar($datos);

        try {
            $this->dep('datos')->eliminar_todo();
            $this->resetear();
            toba::notificacion()->agregar('La eliminación del parámetro: <b>'.$datos['parametro'].'</b> fue realizada con éxito.', "info");
        } catch (toba_error $e) {
            toba::notificacion()->agregar('No se puede eliminar el parámetro:  <b>'.$datos['parametro'].'</b>.', 'error');
        }
    }

    public function evt__formulario__cancelar()
    {
        $this->resetear();
    }

    /*
     * Valida que los datos de algunos campos solo sean SI o NO
     */
    public function validar_dato($datos = null)
    {
        if ($datos['valor'] != 'SI') {
            if ($datos['valor'] != 'NO') {
                throw new toba_error('El parámetro solo acepta valores SI/NO.');
            }
        }
        toba::notificacion()->agregar('Tenga en cuenta que si cambia este parámetro deberá verificar la configuración <br>'
            . 'para cada tipo de proyecto desde la operacion <b>Configurar carga web por tipo de proyecto</b>.', "info");
    }
}