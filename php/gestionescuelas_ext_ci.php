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

        //***INICIO obtención del id_persona del usuario loggueado***//
        $this->s__id_persona = dao_consultas::get_id_persona_x_id_usuario($this->s__usuario);
        if (isset($this->s__id_persona[0])) {
            $this->s__id_persona = $this->s__id_persona[0]['id_persona'];
        }
        //***FIN obtención del id_persona del usuario loggueado***//

        //Obtengo el año activo en el sistema
        $sql = "SELECT anio FROM anio WHERE estado = 'A' ORDER BY anio DESC LIMIT 1";
        $anio_sistema = toba::db()->consultar_fila($sql); // Obtiene el año activo más reciente
        $anio_actual = date('Y');

        //Verifico cuántos años están marcados como activos en la base de datos
        $sql_anios_activos = "SELECT COUNT(*) AS cantidad FROM anio WHERE estado = 'A'";
        $anios_activos = toba::db()->consultar_fila($sql_anios_activos);

        if ($anios_activos['cantidad'] == 0) {
            //No hay ningún año activo en el sistema
            toba::notificacion()->agregar("No hay ningún año activo en el sistema. Debe activarse al menos un año.", "error");
        } elseif ($anios_activos['cantidad'] > 1) {
            //Hay más de un año activo en el sistema
            toba::notificacion()->agregar("Hay más de un año activo en el sistema. Solo debe haber uno marcado como activo.", "error");
        }

        //Verifico si el año actual está registrado en la tabla
        $sql_existe = "SELECT COUNT(*) AS existe FROM anio WHERE anio = $anio_actual";
        $anio_existe = toba::db()->consultar_fila($sql_existe);

        if ($anio_existe['existe'] == 0) {
            //El año actual no está registrado en la tabla
            toba::notificacion()->agregar("El año $anio_actual no está registrado en el sistema. Debe darse de alta.", "error");
        } elseif (empty($anio_sistema) || $anio_actual != $anio_sistema['anio']) {
            //El año actual está registrado, pero no está marcado como activo
            toba::notificacion()->agregar("El año $anio_actual está registrado pero no está marcado como activo.", "error");
        }
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
            // Agregar los parámetros necesarios
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

    /*
     * Retorna los nombres de los alumnos
     */
    public function get_nombres_alumnos_ci()
    {
        $filtro['solo_alumnos'] = true;
        $filtro['con_dni'] = true;
        return dao_consultas::get_nombres_persona($filtro);
    }

    //-----------------------------------------------------------------------------------
    //---- FIN Métodos Varios -----------------------------------------------------------
    //-----------------------------------------------------------------------------------
}