<?php
class ci_alta_manual_pagos extends ci_administrar_formas_cobro
{

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $this->s__datos_filtro['solo_alumnos'] = true;
            $this->s__datos_filtro['administrar_forma_cobro'] = true;
            $this->s__datos_filtro['con_saldo_actual'] = true;
        }
        parent::conf__cuadro($cuadro);
    }

}