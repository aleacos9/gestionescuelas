<?php
class ci_deuda_corriente extends ci_cuenta_corriente
{
    protected $s__datos_deuda_corriente = array();

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_deuda_corriente = $persona->get_datos_deuda_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    //---- cuadro_deuda_corriente ------------------------------------------------------

    function conf__cuadro_deuda_corriente($cuadro)
    {
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">' . $this->s__nombre_alumno . '</div>');
        }
        if (isset($this->s__datos_deuda_corriente)) {
            $cuadro->set_datos($this->s__datos_deuda_corriente);
        }
    }
}