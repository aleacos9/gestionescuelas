<?php
class ci_deuda_corriente extends ci_cuenta_corriente
{

    //---- cuadro_deuda_corriente ------------------------------------------------------

    function conf__cuadro_deuda_corriente($cuadro)
    {
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }
        /*ei_arbol($this->s__datos_filtro);
        if (isset($this->s__datos_filtro)) {*/
            $this->s__datos_filtro['id_persona'] = $this->s__alumno_editar;
            $cuadro->set_datos(dao_personas::get_deuda_actual_por_alumno($this->s__datos_filtro));
        //}
    }

}