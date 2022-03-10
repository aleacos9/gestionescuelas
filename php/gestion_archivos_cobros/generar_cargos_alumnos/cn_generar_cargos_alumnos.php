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
                    $persona->set_datos_generacion_cargos($this->datos_formulario);
                    $persona->generar_cargos_persona();
                }
            } elseif ((!is_array($this->datos_formulario['id_persona'])) && ($this->datos_formulario['forma_generacion'] == 'I')) {
                $persona = new persona($this->datos_formulario['id_persona']);
                $persona->set_datos_generacion_cargos($this->datos_formulario);
                $persona->generar_cargos_persona();
            } else {
                throw new toba_error("Ha ocurrido un error en la generaci�n de los cargos a alumnos, revise los datos ingresados o cont�ctese con un administrador del sistema.");
            }
        }
	}

    public function validar()
    {
        if (isset($this->datos_formulario)) {
            //Valido que si se selecciona como forma de generaci�n Individual => tenga una persona seleccionada
            if (isset($this->datos_formulario['forma_generacion'])) {
                if ($this->datos_formulario['forma_generacion'] == 'I') {
                    if (empty($this->datos_formulario['id_persona'])) {
                        throw new toba_error("Debe seleccionar una persona antes de procesar.");
                    }
                }
            }

            if (isset($this->datos_formulario['cargo_a_generar'])) {
                //Valido que si se selecciona en el cargo a generar Inscripci�n Anual => tenga datos en el a�o
                if ($this->datos_formulario['cargo_a_generar'] == 1) { //inscripci�n anual
                    if (empty($this->datos_formulario['anio'])) {
                        throw new toba_error("Debe seleccionar un a�o antes de procesar.");
                    }
                }
                //Valido que si se selecciona en el cargo a generar Cuota mensual => tenga datos en cuota y a�o
                if ($this->datos_formulario['cargo_a_generar'] == 2) { //cuota mensual
                    if (empty($this->datos_formulario['cuota']) OR empty($this->datos_formulario['anio']))  {
                        throw new toba_error("Debe seleccionar una cuota y un a�o antes de procesar.");
                    }
                }
            }
        }
    }
}