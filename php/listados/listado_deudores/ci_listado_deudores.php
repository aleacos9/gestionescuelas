<?php
class ci_listado_deudores extends ci_abm_personas
{
    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['con_saldo'] = true;
            $datos = dao_consultas::get_saldo_cuenta_corriente($this->s__datos_filtro);
            if (!empty($datos)) {
                $this->dep('filtro')->colapsar();
            }
            $cuadro->set_datos($datos);
        }
    }

}