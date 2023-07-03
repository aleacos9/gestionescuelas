<?php
ini_set('display_errors', '1');

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

    public function ini()
    {
        $this->s__usuario = toba::usuario()->get_id();

        //Obtengo los perfiles funcionales asociados al usuario
        $this->s__perfil_funcional = dao_consultas::get_perfiles_funcionales_por_usuario($this->s__usuario, TRUE);

        //***INICIO obtención del id_persona del usuario loggueado***//
        $this->s__id_persona = dao_consultas::get_id_persona_x_id_usuario($this->s__usuario);
        if (isset($this->s__id_persona[0])) {
            $this->s__id_persona = $this->s__id_persona[0]['id_persona'];
        }
        //***FIN obtención del id_persona del usuario loggueado***//


        /*$afip = new Afip(array('CUIT' => '27127112784'));
        $last_voucher = $afip->ElectronicBilling->GetLastVoucher(1,11);
        $datos['numero_comprobante'] = $last_voucher;
        $datos['tipo_comprobante'] = 11; //Factura C
        $datos['punto_venta'] = 1;
        $persona = new persona($this->s__persona_editar);
        $datos_comp = $persona::obtener_datos_comprobante_afip($datos);
        /*if (isset($datos_comp)) {
            $datos_comp['CbteNro'] = $last_voucher;
            $datos_comp['cuit'] = 27127112784;
            toba::logger()->error('datos_comp:');
            toba::logger()->error($datos_comp);
            //$persona->generar_qr_comprobante_afip($datos_comp);
        }*/

        /*$dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml('<h1>Hola mundo</h1><br><a href="https://parzibyte.me/blog">By Parzibyte</a>');
        $dompdf->render();
        $dompdf->stream();*/
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
