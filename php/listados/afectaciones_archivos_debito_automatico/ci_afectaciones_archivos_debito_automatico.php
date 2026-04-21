<?php
class ci_afectaciones_archivos_debito_automatico extends ci_abm_personas
{
    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $datos = dao_consultas::get_afectacion_archivos_debito_automatico($this->s__datos_filtro);
            if (!empty($datos)) {
                $this->dep('filtro')->colapsar();
            }
            $cuadro->set_datos($datos);
        }
    }
}


