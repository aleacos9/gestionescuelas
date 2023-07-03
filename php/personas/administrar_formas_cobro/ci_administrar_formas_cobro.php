<?php
class ci_administrar_formas_cobro extends ci_vincular_allegados
{
    protected $s__datos_formas_cobro = array();

    //---- Funciones ---------------------------------------------------------------------

    /*
    * Retorna los medios de pagos
    */
    public function get_medios_pagos_ci($filtro = null)
    {
        return dao_consultas::get_medios_pagos($filtro);
    }

    /*
    * Retorna las marcas de tarjetas
    */
    public function get_marcas_tarjetas_ci()
    {
        $filtro['permite_posnet'] = true;
        return dao_consultas::get_marcas_tarjetas($filtro);
    }

    /*
    * Retorna las entidades bancarias
    */
    public function get_entidades_bancarias_ci()
    {
        return dao_consultas::get_entidades_bancarias();
    }

    public function cargar_datos()
    {
        if (!empty($this->s__persona_editar)) {
            $persona = new persona($this->s__persona_editar);
            $this->s__datos_formas_cobro = $persona->get_datos_formas_cobro();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_formas_cobro($this->s__datos_formas_cobro);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['solo_alumnos'] = true;
            $this->s__datos_filtro['administrar_forma_cobro'] = true;
        }
        parent::conf__cuadro($cuadro);
    }

    //---- formulario_ml ------------------------------------------------------------------

    function conf__formulario_ml($form_ml)
    {
        if ($this->s__alumno_editar) {
            $form_ml->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }

        if (!empty($this->s__datos_formas_cobro)) {
            foreach (array_keys($this->s__datos_formas_cobro) as $id) {
                if (isset($this->s__datos_formas_cobro[$id]['apex_ei_analisis_fila'])) {
                    if ($this->s__datos_formas_cobro[$id]['apex_ei_analisis_fila'] == 'B') {
                        unset($this->s__datos_formas_cobro[$id]);
                    }
                }
            }
            $form_ml->set_datos($this->s__datos_formas_cobro);
        }
    }

    function evt__formulario_ml__modificacion($datos)
    {
        foreach (array_keys($datos) as $dato) {
            if ($datos[$dato]['apex_ei_analisis_fila'] == 'B') {
                unset($datos[$dato]);
            }
        }

        $this->s__datos_formas_cobro = $datos;
        foreach (array_keys($this->s__datos_formas_cobro) as $formas_cobro) {
            if (empty($this->s__datos_formas_cobro[$formas_cobro]['id_alumno'])) {
                $this->s__datos_formas_cobro[$formas_cobro]['id_alumno'] = $this->s__alumno_editar;
            }
        }
    }
}