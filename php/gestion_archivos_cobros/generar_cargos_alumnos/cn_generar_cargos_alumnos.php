<?php
class cn_generar_cargos_alumnos extends gestionescuelas_cn
{
    protected $datos_formulario;

    public function set_datos_formulario($datos)
    {
        $this->datos_formulario = $datos;
    }

    function evt__procesar_especifico()
	{
        $this->validar();
        if (isset($this->datos_formulario)) {
            if ((is_array($this->datos_formulario['id_persona'])) && ($this->datos_formulario['forma_generacion'] == 'G')) {
                foreach ($this->datos_formulario['id_persona'] as $key => $value) {
                    $this->procesar_especifico($value);
                }
            } elseif ((!is_array($this->datos_formulario['id_persona'])) && ($this->datos_formulario['forma_generacion'] == 'I')) {
                $this->procesar_especifico($this->datos_formulario['id_persona']);
            } else {
                throw new toba_error("Ha ocurrido un error en la generación de los cargos a alumnos, revise los datos ingresados o contáctese con un administrador del sistema.");
            }
        }
	}

    public function procesar_especifico($persona)
    {
        $persona = new persona($persona);

        //Me fijo si el nivel al cual concurre actualmente el alumno, permite el pago de la inscripción en cuotas
        $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = false;
        if ($this->datos_formulario['cargo_a_generar'] == 1) {
            if ($persona->get_nivel_actual_cursada() == 1) {
                if ((dao_consultas::catalogo_de_parametros("cobra_inscripcion_en_cuotas_inicial")) == 'SI') {
                    $persona->get_datos_tutor();
                    $id_tutor = $persona->get_id_tutor();
                    if (isset($id_tutor)) {
                        $tutorias = dao_personas::get_cantidad_tutorias_x_persona(array('id_persona' => $id_tutor));
                        $cantidad_alumnos_tutoria = $tutorias[0]['tutorias'];

                        if (isset($cantidad_alumnos_tutoria) && ($cantidad_alumnos_tutoria > 1)) {
                            //actualizo el campo paga_inscripcion_en_cuotas de la tabla alumno
                            $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = true;
                        }
                    }
                }
                $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_inicial");
            }
            if ($persona->get_nivel_actual_cursada() == 2) {
                if ((dao_consultas::catalogo_de_parametros("cobra_inscripcion_en_cuotas_primario")) == 'SI') {
                    $persona->get_datos_tutor();
                    $id_tutor = $persona->get_id_tutor();
                    if (isset($id_tutor)) {
                        $tutorias = dao_personas::get_cantidad_tutorias_x_persona(array('id_persona' => $id_tutor));
                        $cantidad_alumnos_tutoria = $tutorias[0]['tutorias'];

                        if (isset($cantidad_alumnos_tutoria) && ($cantidad_alumnos_tutoria > 1)) {
                            //actualizo el campo paga_inscripcion_en_cuotas de la tabla alumno
                            $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = true;
                        }
                    }
                }
                $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_primario");
            }
        }
        if ($this->datos_formulario['cargo_a_generar'] == 1) {
            if (($persona->get_grado_actual_cursada() == 8) && ($persona->get_anio_actual_cursada() == 2)) {
                toba::logger()->error('Para el alumno: ' . $persona->get_id_alumno() . ' no se le genera el costo de la inscripción ya que está cursando 6to grado, por lo tanto es un egresado.');
            } else {
                $this->datos_formulario['id_alumno'] = $persona->get_id_alumno();
                $persona->set_datos_generacion_cargos($this->datos_formulario);
                $persona->generar_cargos_persona();
            }
        } else {
            $this->datos_formulario['id_alumno'] = $persona->get_id_alumno();
            $persona->set_datos_generacion_cargos($this->datos_formulario);
            $persona->generar_cargos_persona();
        }
    }

    public function validar()
    {
        if (isset($this->datos_formulario)) {
            //Valido que si se selecciona como forma de generación Individual => tenga una persona seleccionada
            if (isset($this->datos_formulario['forma_generacion'])) {
                if ($this->datos_formulario['forma_generacion'] == 'I') {
                    if (empty($this->datos_formulario['id_persona'])) {
                        throw new toba_error("Debe seleccionar una persona antes de procesar.");
                    }
                }
            }

            if (isset($this->datos_formulario['cargo_a_generar'])) {
                //Valido que si se selecciona en el cargo a generar Inscripción Anual => tenga datos en el año
                if ($this->datos_formulario['cargo_a_generar'] == 1) { //inscripción anual
                    if (empty($this->datos_formulario['anio'])) {
                        throw new toba_error("Debe seleccionar un año antes de procesar.");
                    }
                }
                //Valido que si se selecciona en el cargo a generar Cuota mensual => tenga datos en cuota y año
                if ($this->datos_formulario['cargo_a_generar'] == 2) { //cuota mensual
                    if (empty($this->datos_formulario['cuota']) OR empty($this->datos_formulario['anio']))  {
                        throw new toba_error("Debe seleccionar una cuota y un año antes de procesar.");
                    }
                }
            }

            //Valido que si el parámetro ingresa_importe_en_generacion_cargos está en SI => se cargue un importe
            if (dao_consultas::catalogo_de_parametros("ingresa_importe_en_generacion_cargos") == 'SI') {
                if (empty($this->datos_formulario['importe_cuota'])) {
                    throw new toba_error("Debe ingresar un importe antes de procesar.");
                }
            }
        }
    }
}