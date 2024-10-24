<?php
class ci_generar_cargos_alumnos extends gestionescuelas_ext_ci
{
    protected $s__datos_formulario;
    //protected $s__importe_cuota;

    //---- Funciones ---------------------------------------------------------------------

    /*function ini()
    {
        $cuota_x_grado = dao_consultas::catalogo_de_parametros("importe_mensual_cuota_x_grado");
        if ($cuota_x_grado == 'NO') {
            $this->s__importe_cuota = dao_consultas::catalogo_de_parametros("importe_mensual_cuota");
        } else {
            //aca deber�a incluir la l�gica para que se el importe dependa del grado/nivel al que concurra el alumno
        }
    }*/

    /*
     * Retorna los nombres de los alumnos
     */
    public function get_nombres_alumnos_ci()
    {
        $filtro['solo_alumnos'] = true;
        $filtro['con_dni'] = true;
        return dao_consultas::get_nombres_persona($filtro);
    }

    /*
     * Retorna un array con el detalle de las cuotas para el pago de materiales,
     * definidas a trav�s de un par�metro general del sistema
     */
    public function get_cantidad_cuotas_materiales_ci()
    {
        $numero_cuota = dao_consultas::catalogo_de_parametros("cantidad_cuotas_materiales");
        if ($numero_cuota > 0) {
            $valores = [];
            for ($i = 1; $i <= $numero_cuota; $i++) {
                $valores[] = ['numero_cuota' => $i];
            }
            return $valores;
        }
    }

    /*
     * Retorna un array con el detalle de las cuotas para el pago de la inscripcion anual,
     * definidas a trav�s de un par�metro general del sistema
     */
    public function get_cantidad_cuotas_inscripcion_ci()
    {
        $numero_cuota = dao_consultas::catalogo_de_parametros("cant_cuotas_cobro_inscripcion");
        if ($numero_cuota > 0) {
            $valores = [];
            for ($i = 1; $i <= $numero_cuota; $i++) {
                $valores[] = ['numero_cuota' => $i];
            }
            return $valores;
        }
    }

    //---- formulario ----------------------------------------------------------------------

    public function evt__formulario__modificacion($datos)
    {
        $filtro_int = array();
        $this->s__datos_formulario = $datos;

        if (dao_consultas::catalogo_de_parametros("importe_mensual_cuota_x_grado") == 'NO') {
            if (dao_consultas::catalogo_de_parametros("ingresa_importe_en_generacion_cargos") == 'NO') {
                if ($this->s__datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('CUOTA_MENSUAL')) {
                    $this->s__datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_mensual_cuota");
                } elseif ($this->s__datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('MATERIALES')) {
                    $this->s__datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_materiales");
                }
            }

            if ($this->s__datos_formulario['forma_generacion'] == 'G') {
                if ($this->s__datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('MATERIALES')) {
                    $filtro_int['solo_nivel_inicial'] = true;
                }
                //Obtengo solo los alumnos y que adem�s est�n activos
                $filtro_int['activo'] = 'S';
                $filtro_int['solo_ids'] = true;
                $filtro_int['solo_alumnos'] = true;
                $filtro_int['excluir_alumnos_generacion_masiva'] = false;
                $this->s__datos_formulario['id_persona'] = dao_personas::get_datos_personas($filtro_int);
            }
        } else {
            //aca deber�a incluir la l�gica para que se el importe dependa del grado/nivel al que concurra el alumno
        }
    }

    //---- Eventos ----------------------------------------------------------------------

    public function evt__procesar()
    {
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        /*if (strtolower($rta) == 'ok') {
            $this->limpiar_memoria();
            $this->controlador()->disparar_limpieza_memoria();
            $this->controlador()->set_pantalla("seleccion");
        } else {
            throw new toba_error("Ha ocurrido un error inesperado: $rta");
        }*/
    }

    public function validar()
    {
        //este m�todo puede q no sea necesario, ya que puedo hacer todo desde el validar del cn
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_formulario($this->s__datos_formulario);
    }

}