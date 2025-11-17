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
     * Procesa la generación de cargos para una persona en función de su grado siguiente y el tipo de cargo especificado.
     *
     * Este método evalúa varias condiciones para determinar si corresponde generar un cargo para la persona:
     * 1. Si el siguiente grado es "Fin de ciclo" y el cargo a generar es INSCRIPCION_ANUAL, no se genera el cargo.
     * 2. Si el siguiente grado es "Fin de ciclo" y el cargo a generar es CUOTA_MENSUAL, sí se genera el cargo.
     * 3. Si el siguiente grado es null y el cargo a generar es INSCRIPCION_ANUAL, se genera el cargo.
     * 4. Si el siguiente grado es null y el cargo a generar es CUOTA_MENSUAL, no se genera el cargo.
     *
     * Además, para cargos de inscripción anual, el método evalúa si se permite el pago en cuotas
     * y calcula el importe correspondiente según el grado siguiente.
     *
     * @param array $persona Datos de la persona para la cual se procesarán los cargos.
     * @throws toba_error Si el cargo no corresponde y la forma de generación es individual ('I').
     */
    public function procesar_especifico($persona)
    {
        $persona = new persona($persona);
        $this->resumen['total_alumnos']++;

        //Inicializo variables
        $this->datos_formulario['actualiza_pago_inscripcion_en_cuotas'] = false;
        $cargo_a_generar = $this->datos_formulario['cargo_a_generar'];
        $siguiente_grado = $persona->get_grado_siguiente_cursada();

        //Procesamiento según tipo de cargo
        if ($cargo_a_generar == constantes::get_valor_constante('INSCRIPCION_ANUAL')) {
            if ($siguiente_grado === "Fin de ciclo") {
                //Caso 1: siguiente grado es "Fin de ciclo" y cargo INSCRIPCION_ANUAL
                toba::logger()->info("No se generará cargo para el alumno {$persona->get_id_alumno()} ya que su siguiente grado de cursada es 'Fin de ciclo'.");
                if ($this->datos_formulario['forma_generacion'] == 'I') {
                    throw new toba_error("Al alumno {$persona->get_nombre_completo_alumno()} no le corresponde la generación de la inscripción.");
                }
                $this->resumen['cargos_no_generados']++;
                return;
            }

            if (is_null($siguiente_grado)) {
                //Caso 2: siguiente grado es null y cargo INSCRIPCION_ANUAL, debo validar que sea inscripción del año siguiente
                toba::logger()->info("El alumno {$persona->get_id_alumno()} tiene grado siguiente NULL. Intentando determinarlo según el año siguiente...");

                // 1) Obtengo el año activo
                $anio_activo = dao_consultas::get_anios(['solo_activos' => 'S']);
                if (!empty($anio_activo)) {
                    $anio_activo_id = $anio_activo[0]['id_anio'];
                    $anio_activo_valor = $anio_activo[0]['anio'];

                    // 2) Busco si existe un año posterior dado de alta (estado = 'I') y que sea inactivo
                    $anio_siguiente = dao_consultas::get_anio_posterior_de_alta_inactivo($anio_activo_id);

                    if (!empty($anio_siguiente)) {
                        toba::logger()->info("Año siguiente encontrado: {$anio_siguiente['anio']} (id={$anio_siguiente['id_anio']})");

                        // 3) Busco si el alumno tiene datos de cursada para ese año
                        $id_alumno = $persona->get_id_alumno();

                        $sql_cursada = "SELECT id_grado
                                        FROM alumno_datos_cursada
                                        WHERE id_alumno = {$id_alumno}
                                            AND anio_cursada = {$anio_siguiente['id_anio']}
                                        LIMIT 1
                                       ";

                        $datos_cursada = toba::db()->consultar_fila($sql_cursada);

                        if (!empty($datos_cursada)) {
                            // 4) Con el id_grado, obtengo el grado siguiente desde la tabla grado
                            $sql_grado_siguiente = "SELECT id_grado_siguiente
                                                    FROM grado
                                                    WHERE id_grado = {$datos_cursada['id_grado']}
                                                   ";

                            $grado_siguiente = toba::db()->consultar_fila($sql_grado_siguiente);

                            if (!empty($grado_siguiente) && !empty($grado_siguiente['id_grado_siguiente'])) {
                                $siguiente_grado = $grado_siguiente['id_grado_siguiente'];
                                toba::logger()->info("Grado siguiente determinado dinámicamente: {$siguiente_grado} (desde id_grado={$datos_cursada['id_grado']})");
                            } else {
                                toba::logger()->info("El grado {$datos_cursada['id_grado']} no tiene grado siguiente definido en la tabla grado.");
                                $this->resumen['mensajes'][] = "El grado {$datos_cursada['id_grado']} del alumno {$persona->get_nombre_completo_alumno()} no tiene grado siguiente definido.";
                            }
                        } else {
                            toba::logger()->info("El alumno {$id_alumno} no tiene registro en alumno_datos_cursada para el año {$anio_siguiente['anio']}.");
                            $this->resumen['mensajes'][] = "El alumno {$persona->get_nombre_completo_alumno()} no tiene cursada cargada para el año {$anio_siguiente['anio']}.";
                        }
                    } else {
                        toba::logger()->info("No existe un año posterior al activo ({$anio_activo_valor}) con estado 'I'.");
                        $this->resumen['mensajes'][] = "No existe un año posterior al activo ({$anio_activo_valor}) con estado 'I'. No se pudieron generar inscripciones anuales.";
                    }
                } else {
                    toba::logger()->info("No se pudo determinar el año activo.");
                    $this->resumen['mensajes'][] = "No se pudo determinar el año activo en la base de datos.";
                }
            }

            //Generación de cargos INSCRIPCION_ANUAL
            $cobra_en_cuotas = dao_consultas::catalogo_de_parametros(
                $siguiente_grado == 2 ? "cobra_inscripcion_en_cuotas_inicial" : "cobra_inscripcion_en_cuotas_primario"
            );

            if ($cobra_en_cuotas == 'SI') {
                $this->actualizar_pago_en_cuotas($persona);
            }

            $this->datos_formulario['importe_cuota'] = dao_consultas::catalogo_de_parametros(
                    $siguiente_grado == 2 ? "importe_inscripcion_inicial" : "importe_inscripcion_primario"
                ) ?? 0;

            $cant_cuotas = dao_consultas::catalogo_de_parametros("cant_cuotas_cobro_inscripcion");
            if ($cant_cuotas == 1) {
                $this->generar_cargo_persona($persona);
            } else {
                $this->procesar_inscripcion_multiple_cuotas($persona);
            }
            return;
        }

        if ($cargo_a_generar == constantes::get_valor_constante('CUOTA_MENSUAL')) {
            if ($siguiente_grado === "Fin de ciclo") {
                //Caso 3: siguiente grado es "Fin de ciclo" y cargo CUOTA_MENSUAL
                toba::logger()->info("Se generará cargo mensual para el alumno {$persona->get_id_alumno()} ya que su siguiente grado de cursada es 'Fin de ciclo'.");
                $this->generar_cargo_persona($persona);
                return;
            }

            if (is_null($siguiente_grado)) {
                //Caso 4: siguiente grado es null y cargo CUOTA_MENSUAL
                toba::logger()->info("No se generará cargo mensual para el alumno {$persona->get_id_alumno()} ya que su siguiente grado de cursada es null.");
                $this->resumen['cargos_no_generados']++;
                return;
            }
        }

        //Generación para otros casos
        $this->generar_cargo_persona($persona);
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

        //Agrego mensajes adicionales si existen
        if (!empty($this->resumen['mensajes'])) {
            $mensaje .= "<br /><br /><strong>Observaciones:</strong><ul>";
            foreach ($this->resumen['mensajes'] as $msg) {
                $mensaje .= "<li>{$msg}</li>";
            }
            $mensaje .= "</ul>";
        }

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