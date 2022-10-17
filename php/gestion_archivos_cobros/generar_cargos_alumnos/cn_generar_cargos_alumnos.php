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
                    $persona = new persona($value);
                    if ($this->datos_formulario['cargo_a_generar'] == 1) {
                        if ($persona->get_nivel_actual_cursada() == 1) {
                            $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_inicial");
                        } elseif ($persona->get_nivel_actual_cursada() == 2) {
                            $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_primario");
                        }
                    }
                    $persona->set_datos_generacion_cargos($this->datos_formulario);
                    $persona->generar_cargos_persona();
                }
            } elseif ((!is_array($this->datos_formulario['id_persona'])) && ($this->datos_formulario['forma_generacion'] == 'I')) {
                $persona = new persona($this->datos_formulario['id_persona']);
                if ($this->datos_formulario['cargo_a_generar'] == 1) {
                    if ($persona->get_nivel_actual_cursada() == 1) {
                        $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_inicial");
                    } elseif ($persona->get_nivel_actual_cursada() == 2) {
                        $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros("importe_inscripcion_primario");
                    }
                }
                $persona->set_datos_generacion_cargos($this->datos_formulario);
                $persona->generar_cargos_persona();
            } else {
                throw new toba_error("Ha ocurrido un error en la generación de los cargos a alumnos, revise los datos ingresados o contáctese con un administrador del sistema.");
            }
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