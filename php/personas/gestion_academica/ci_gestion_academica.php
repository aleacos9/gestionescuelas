<?php
class ci_gestion_academica extends ci_vincular_allegados
{
    protected $s__datos_gestion_academica = array();

    //---- Funciones ---------------------------------------------------------------------

    /*
     * Retorna los grados
     */
    public function get_grados_ci()
    {
        return dao_consultas::get_grados();
    }

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_gestion_academica = $persona->get_datos_academicos();
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_academicos($this->s__datos_gestion_academica);
    }

    //---- formulario_ml ------------------------------------------------------------------

    function conf__formulario_ml($form_ml)
    {
        if (!empty($this->s__datos_gestion_academica)) {
            $form_ml->set_datos($this->s__datos_gestion_academica);
        }
    }

    function evt__formulario_ml__modificacion($datos)
    {
        $this->s__datos_gestion_academica = $datos;
        foreach (array_keys($this->s__datos_gestion_academica) as $gestion_academica) {
            if (empty($this->s__datos_gestion_academica[$gestion_academica]['id_alumno'])) {
                $this->s__datos_gestion_academica[$gestion_academica]['id_alumno'] = $this->s__alumno_editar;
            }
        }
    }
}
