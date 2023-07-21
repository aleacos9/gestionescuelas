<?php
class ci_listado_deudores extends ci_abm_personas
{
    protected $s__datos_deuda_corriente;

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

    function evt__cuadro__seleccion($seleccion)
    {
        $this->s__persona_editar = $seleccion['id_persona'];
        $this->set_pantalla('edicion');
        $this->cargar_datos();
    }

    public function cargar_datos()
    {
        if (!empty($this->s__persona_editar)) {
            $persona = new persona($this->s__persona_editar);
            $this->s__datos_deuda_corriente = $persona->get_datos_deuda_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
        }
    }

    //---- cuadro_deuda_corriente ----------------------------------------------------------

    function conf__cuadro_deuda_corriente($cuadro)
    {
        if ($this->s__persona_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">'.$this->s__nombre_alumno.'</div>');
        }
        if (!empty($this->s__datos_deuda_corriente)) {
            $cuadro->set_datos($this->s__datos_deuda_corriente);
        }
    }

    //---- Eventos ----------------------------------------------------------------------

    public function evt__cancelar()
    {
        $this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
        $this->controlador()->set_pantalla('seleccion');
    }



}