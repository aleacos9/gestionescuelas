<?php
ini_set('display_error', '1');
error_reporting(E_ALL);
use Dompdf\Dompdf;
use Dompdf\Options;

class gestionescuelas_ext_ci extends toba_ci
{
    protected $s__seleccion;
    protected $s__datos_filtro;
    protected $s__perfil_funcional;
    protected $s__usuario = null;
    protected $s__id_persona = null;
    protected $s__persona_editar;
    protected $s__alumno_editar;
    protected $s__nombre_alumno;
    protected $s__datos_allegados = array();

    protected $afip;

    public function ini()
    {
        $this->s__usuario = toba::usuario()->get_id();

        //Obtengo los perfiles funcionales asociados al usuario
        $this->s__perfil_funcional = dao_consultas::get_perfiles_funcionales_por_usuario($this->s__usuario, TRUE);

        //***INICIO obtenci�n del id_persona del usuario loggueado***//
        $this->s__id_persona = dao_consultas::get_id_persona_x_id_usuario($this->s__usuario);
        if (isset($this->s__id_persona[0])) {
            $this->s__id_persona = $this->s__id_persona[0]['id_persona'];
        }
        //***FIN obtenci�n del id_persona del usuario loggueado***//
    }

    public function estado_servidor_afip()
    {
        return dao_consultas::estado_servidor_afip();
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

    //---- cuadro_cuenta_corriente ------------------------------------------------------

    function conf_evt__cuadro_cuenta_corriente__descargar_comprobante($evento, $fila)
    {
        // Determinar la variable a utilizar
        $datos = isset($this->s__datos_cuadro_cuenta_corriente) ? $this->s__datos_cuadro_cuenta_corriente : $this->s__datos_alta_manual_pago;

        if (!isset($datos[$fila]['id_medio_pago'])) {
            $evento->anular();
        }

        if (isset($datos[$fila]['punto_venta']) &&
            isset($datos[$fila]['comprobante_tipo']) &&
            isset($datos[$fila]['comprobante_numero'])
        ) {
            $evento->activar();
            $evento->set_imagen('extension_pdf.png');
            // Agregar los par�metros necesarios
            $evento->vinculo()->agregar_parametro(utf8_encode('punto_venta'), $datos[$fila]['punto_venta']);
            $evento->vinculo()->agregar_parametro(utf8_encode('comprobante_tipo'), $datos[$fila]['comprobante_tipo']);
            $evento->vinculo()->agregar_parametro(utf8_encode('comprobante_numero'), $datos[$fila]['comprobante_numero']);
            $evento->vinculo()->agregar_parametro(utf8_encode('id_persona'), $datos[$fila]['id_persona']);
            $evento->vinculo()->agregar_parametro(utf8_encode('id_alumno_cc'), $datos[$fila]['id_alumno_cc']);
        } else {
            $evento->desactivar();
            $evento->set_imagen('error.png');
        }
    }

    //---- formulario -------------------------------------------------------------------

    /*function conf__formulario($form)
    {
        if ($this->dep('datos')->esta_cargada()){
            $form->set_datos($this->dep('datos')->get());
        }
    }*/

    //-----------------------------------------------------------------------------------
    //---- M�todos Varios ---------------------------------------------------------------
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
    //---- FIN M�todos Varios -----------------------------------------------------------
    //-----------------------------------------------------------------------------------
}