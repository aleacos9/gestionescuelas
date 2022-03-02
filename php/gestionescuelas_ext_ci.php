<?php
ini_set('display_errors', '1');
class gestionescuelas_ext_ci extends toba_ci
{
    protected $s__seleccion;
    protected $s__datos_filtro;

    public function ini()
    {

    }

    //---- filtro -----------------------------------------------------------------------

    public function conf__filtro($form)
    {
        if (isset($this->s__datos_filtro)) {
            return $this->s__datos_filtro;
        }
    }

    public function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
        $this->resetear_tabla();
    }

    public function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
        $this->resetear_tabla();
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {

    }

    public function evt__cuadro__seleccion($datos)
    {
        $this->dep('datos')->cargar($datos);
        $this->set_pantalla('edicion');
    }

    //---- formulario -------------------------------------------------------------------

    /*function conf__formulario($form)
    {
        if ($this->dep('datos')->esta_cargada()){
            $form->set_datos($this->dep('datos')->get());
        }
    }*/

    //-----------------------------------------------------------------------------------
    //---- Métodos Varios ---------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function resetear()
    {
        $this->get_relacion()->resetear();
    }

    public function resetear_tabla()
    {
        $this->dep('datos')->resetear();
    }

    public function get_relacion()
    {
        return $this->dep('datos');
    }

    public function get_tabla($tabla)
    {
        return $this->dep('datos')->tabla($tabla);
    }

    public function evt__agregar()
    {
        $this->set_pantalla('edicion');
    }

    /**
     * Agrega la limpieza del listado de dependencias a excluir
     */
    public function disparar_limpieza_memoria($no_borrar = array())
    {
        unset($this->_excluir_ci_com_cn);
        parent::disparar_limpieza_memoria($no_borrar);
    }

    //-----------------------------------------------------------------------------------
    //---- FIN Métodos Varios -----------------------------------------------------------
    //-----------------------------------------------------------------------------------
}
