<?php
class cn_generar_cargos_alumnos extends gestionescuelas_cn
{
    protected $datos_formulario;
    protected $resumen;
    protected $tipo_procesamiento;
    protected $tipo_cargo;

    public function set_datos_formulario($datos)
    {
        $this->datos_formulario = $datos;
        $this->tipo_procesamiento = (is_array($datos['id_persona']) && $datos['forma_generacion'] == 'G') ? 'Grupal' : 'Individual';
        $this->tipo_cargo = $datos['cargo_a_generar'];
    }

    /**
     * Procesa la generación de cargos para alumnos a partir de los datos del formulario.
     *
     * Este método realiza las siguientes acciones:
     * - Valida la entrada de datos mediante el método `validar()`.
     * - Inicializa el resumen de cargos generados y no generados.
     * - Determina si la generación de cargos debe realizarse para un solo alumno o para varios,
     *   basado en la forma de generación especificada en el formulario.
     * - Si se trata de múltiples alumnos (forma de generación 'G'), recorre cada ID de persona y llama a `procesar_especifico()` para cada uno.
     * - Si se trata de un solo alumno (forma de generación 'I'), procesa ese alumno directamente.
     * - Si los datos no cumplen con las condiciones esperadas, se lanza un error informando del problema.
     * - Finalmente, muestra un resumen de los resultados de la generación de cargos utilizando el método `mostrar_resumen()`.
     */
    function evt__procesar_especifico()
    {
        $this->validar();

        $this->resumen['total_alumnos'] = 0;
        $this->resumen['cargos_generados'] = 0;
        $this->resumen['cargos_no_generados'] = 0;

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
            $this->mostrar_resumen();
        }
    }

    /**
     * Procesa la generación de cargos para una persona específica.
     *
     * Este método se encarga de lo siguiente:
     * - Crea una instancia de la clase `persona` con el ID proporcionado.
     * - Incrementa el contador total de alumnos procesados.
     * - Inicializa las variables necesarias para el procesamiento.
     * - Determina si el cargo a generar corresponde a una inscripción anual.
     *
     * Si el cargo es de inscripción anual:
     * - Verifica si se permite el pago en cuotas según el nivel actual del alumno, y si es así,
     *   actualiza el estado del pago en cuotas.
     * - Establece el importe de la cuota según el nivel de la persona.
     * - Genera los cargos de inscripción basándose en la cantidad de cuotas permitidas.
     *   Si hay solo una cuota, se llama a `generar_cargo_persona()`, de lo contrario,
     *   se procesa la inscripción en múltiples cuotas a través del método `procesar_inscripcion_multiple_cuotas()`.
     *
     * En caso de que el cargo no sea de inscripción anual, genera el cargo correspondiente
     * utilizando `generar_cargo_persona()`.
     *
     * @param mixed $persona Identificador de la persona para la cual se generarán los cargos.
     */
    public function procesar_especifico($persona)
    {
        $persona = new persona($persona);
        $this->resumen['total_alumnos']++;

        //Inicializo variables
        $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = false;
        $cargo_a_generar = $this->datos_formulario['cargo_a_generar'];
        $siguiente_grado = $persona->get_grado_siguiente_cursada();

        if (is_null($siguiente_grado) || $siguiente_grado === "Fin de ciclo") {
            toba::logger()->info("No se generará cargo para el alumno {$persona->get_id_alumno()} ya que su siguiente grado de cursada es null o fin de ciclo.");
            if ($this->datos_formulario['forma_generacion'] == 'I') {
                throw new toba_error("Al alumno {$persona->get_nombre_completo_alumno()} no le corresponde la generación de la inscripción.");
            }
            $this->resumen['cargos_no_generados']++;
            return;
        }

        if ($cargo_a_generar == constantes::get_valor_constante('INSCRIPCION_ANUAL')) {
            //Determino si la inscripción permite el pago en cuotas en función del nivel
            $cobra_en_cuotas = dao_consultas::catalogo_de_parametros(
                $siguiente_grado == 2 ? "cobra_inscripcion_en_cuotas_inicial" : "cobra_inscripcion_en_cuotas_primario"
            );

            if ($cobra_en_cuotas == 'SI') {
                $this->actualizar_pago_en_cuotas($persona);
            }

            //Establezco el importe de cuota según el siguiente grado
            $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros(
                    $siguiente_grado == 2 ? "importe_inscripcion_inicial" : "importe_inscripcion_primario"
                ) ?? 0;

            //Genero los cargos de inscripción en función de la cantidad de cuotas permitidas
            $cant_cuotas = dao_consultas::catalogo_de_parametros("cant_cuotas_cobro_inscripcion");
            if ($cant_cuotas == 1) {
                $this->generar_cargo_persona($persona);
            } else {
                $this->procesar_inscripcion_multiple_cuotas($persona);
            }
        } else {
            //Genero cargos para otros casos
            $this->generar_cargo_persona($persona);
        }
    }

    /**
     * Actualiza el estado del pago de inscripción en cuotas para una persona,
     * en función de la cantidad de alumnos tutelados por su tutor.
     *
     * Este método realiza lo siguiente:
     * - Obtiene los datos del tutor de la persona proporcionada.
     * - Si la persona tiene un tutor asignado, se recupera la cantidad de alumnos que
     *   están bajo su tutela.
     * - Si el tutor tiene más de un alumno a su cargo, se establece el indicador
     *   'actualiza_pago_inscripcion_en_cuotas' en `true`, lo que indica que se
     *   debe considerar el pago en cuotas para la inscripción.
     *
     * @param persona $persona Objeto que representa a la persona cuyos datos de pago se
     *                          están actualizando.
     */
    private function actualizar_pago_en_cuotas($persona)
    {
        $persona->get_datos_tutor();
        $id_tutor = $persona->get_id_tutor();

        if ($id_tutor) {
            $tutorias = dao_personas::get_cantidad_tutorias_x_persona(['id_persona' => $id_tutor]);
            $cantidad_alumnos_tutoria = $tutorias[0]['tutorias'] ?? 0;

            if ($cantidad_alumnos_tutoria > 1) {
                $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = true;
            }
        }
    }

    /**
     * Procesa la inscripción de un alumno en múltiples cuotas, verificando si
     * corresponde generar la inscripción según su grado y año actual de cursada.
     *
     * Este método realiza las siguientes acciones:
     * - Obtiene el año actual activo.
     * - Verifica si el alumno está cursando el último grado (grado 8)
     *   y si está en el año actual de cursada. Si es así, se registra
     *   un error en el log y se lanza una excepción si la forma de generación
     *   es 'I', indicando que no se debe generar la inscripción.
     * - Si el alumno no está en el último grado, se obtiene el
     *   importe correspondiente a la cuota según su grado actual
     *   y se genera el cargo para la persona.
     *
     * @param persona $persona Objeto que representa al alumno cuya inscripción
     *                          se está procesando.
     */
    private function procesar_inscripcion_multiple_cuotas($persona)
    {
        $parametro = $this->obtener_parametro_cuota($persona->get_grado_actual_cursada());
        $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros($parametro) ?? 0;
        $this->generar_cargo_persona($persona);
    }

    /**
     * Obtiene el parámetro correspondiente al importe de la cuota de inscripción
     * según el grado actual del alumno y el número de cuota indicado en los datos
     * del formulario.
     *
     * Este método determina si el grado actual del alumno pertenece a los niveles
     * iniciales (SALA4 o SALA5) o a niveles primarios. Luego, dependiendo del
     * número de cuota de inscripción, devuelve la constante correspondiente que
     * representa el importe de la cuota.
     *
     * @param int|string $grado_actual Grado actual del alumno, que puede ser
     *                                  un valor entero o una cadena que
     *                                  representa el grado.
     * @return string Nombre del parámetro que corresponde al importe de la
     *                cuota de inscripción.
     */
    private function obtener_parametro_cuota($grado_actual)
    {
        $nivel = [
            constantes::get_valor_constante('SALA4'),
            constantes::get_valor_constante('SALA5')
        ];

        if (in_array($grado_actual, $nivel)) {
            return $this->datos_formulario['numero_cuota_inscripcion'] == 1
                ? "importe_cuota_uno_nivel_inicial"
                : "importe_cuota_dos_nivel_inicial";
        } else {
            return $this->datos_formulario['numero_cuota_inscripcion'] == 1
                ? "importe_cuota_uno_nivel_primario"
                : "importe_cuota_dos_nivel_primario";
        }
    }

    /**
     * Genera un cargo para un alumno especifico.
     *
     * Este método establece el ID del alumno en los datos del formulario y
     * configura la información necesaria para la generación de cargos. Luego,
     * invoca el método `generar_cargos_persona()` de la clase `persona` para
     * crear el cargo correspondiente.
     *
     * Después de intentar generar el cargo, se valida el resultado. Si no hay
     * errores, se incrementa el contador de cargos generados; de lo contrario,
     * se incrementa el contador de cargos no generados.
     *
     * @param persona $persona Objeto que representa al alumno para el cual
     *                         se generará el cargo.
     */
    private function generar_cargo_persona($persona)
    {
        $this->datos_formulario['id_alumno'] = $persona->get_id_alumno();
        $persona->set_datos_generacion_cargos($this->datos_formulario);
        $resultado = $persona->generar_cargos_persona();

        //Valido si el cargo se generó o no
        if (empty($resultado['error'])) {
            $this->resumen['cargos_generados']++;
        } else {
            $this->resumen['cargos_no_generados']++;
        }
    }

    /**
     * Muestra un resumen de la generación de cargos procesados.
     *
     * Este método construye un mensaje que incluye el total de alumnos procesados,
     * la cantidad de cargos generados y la cantidad de cargos no generados.
     * Luego, envía este mensaje como una notificación de información al sistema
     * para que sea visible para el usuario.
     */
    private function mostrar_resumen()
    {
        //Obtengo la descripcion del cargo generado
        $cargo_info = dao_consultas::get_cargos_cuenta_corriente(array('id_cargo_cuenta_corriente' => $this->tipo_cargo));
        if (!empty($cargo_info)) {
            $descripcion_tipo_cargo = $cargo_info[0]['nombre'];
        } else {
            $descripcion_tipo_cargo = 'Descripción no disponible';
        }

        $mensaje = "Resumen de la generación de cargos:<br />";
        $mensaje .= "Tipo de cargo generado: {$descripcion_tipo_cargo}<br />";
        $mensaje .= "Tipo de procesamiento: {$this->tipo_procesamiento}<br />";
        $mensaje .= "Total de alumnos procesados: {$this->resumen['total_alumnos']}<br />";
        $mensaje .= "Total de cargos generados: {$this->resumen['cargos_generados']}<br />";
        $mensaje .= "Total de cargos no generados: {$this->resumen['cargos_no_generados']}";

        toba::notificacion()->agregar($mensaje, 'info');
    }

    /**
     * Valida los datos del formulario antes de procesar la generación de cargos.
     *
     * Este método verifica que se cumplan ciertas condiciones según la forma de generación seleccionada
     * y el tipo de cargo a generar. Asegura que:
     * - Si la forma de generación es 'Individual', se haya seleccionado una persona.
     * - Si el cargo a generar es 'Inscripción Anual', se debe especificar el año y, si aplica, el número de cuota.
     * - Si el cargo a generar es 'Cuota Mensual', se deben proporcionar tanto la cuota como el año.
     * - Si el cargo a generar es 'Materiales', se requiere un número de cuota y el año correspondiente.
     * - Si el parámetro de configuración 'ingresa_importe_en_generacion_cargos' es 'SI', se debe ingresar un importe.
     *
     * Si alguna de estas validaciones falla, se lanza una excepción con un mensaje de error correspondiente.
     */
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
                if ($this->datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('INSCRIPCION_ANUAL')) {
                    if (dao_consultas::catalogo_de_parametros("cant_cuotas_cobro_inscripcion") > 1) {
                        if (empty($this->datos_formulario['numero_cuota_inscripcion'])) {
                            throw new toba_error("Debe seleccionar un número de cuota antes de procesar.");
                        }
                    }
                    if (empty($this->datos_formulario['anio'])) {
                        throw new toba_error("Debe seleccionar un año antes de procesar.");
                    }
                }
                //Valido que si se selecciona en el cargo a generar Cuota mensual => tenga datos en cuota y año
                if ($this->datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('CUOTA_MENSUAL')) {
                    if (empty($this->datos_formulario['cuota']) OR empty($this->datos_formulario['anio']))  {
                        throw new toba_error("Debe seleccionar una cuota y un año antes de procesar.");
                    }
                }
                //Valido que si se selecciona en el cargo a generar Materiales => tenga datos en cantidad de cuota y año
                if ($this->datos_formulario['cargo_a_generar'] == constantes::get_valor_constante('MATERIALES')) {
                    if (empty($this->datos_formulario['numero_cuota']) OR empty($this->datos_formulario['anio']))  {
                        throw new toba_error("Debe seleccionar número de cuota y un año antes de procesar.");
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