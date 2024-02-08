<?php
class ci_listado_estado_deuda_alumnos extends ci_abm_personas
{
    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['con_saldo'] = true;
            $datos = dao_consultas::get_listado_estado_deuda_alumnos($this->s__datos_filtro);
            if (!empty($datos)) {
                $this->dep('filtro')->colapsar();
            }
            $cuadro->set_datos($datos);
        }
    }
}