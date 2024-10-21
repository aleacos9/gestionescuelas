<?php
class ci_abm_personas_interno extends ci_abm_personas
{
    protected $s__persona_seleccionada;
    protected $s__documento_seleccionado;

    public function conf()
    {
        $this->s__persona_seleccionada = $this->controlador()->get_persona_editar();
        if (empty($this->s__datos_persona) && !empty($this->s__persona_seleccionada)) {
            $persona = new persona($this->s__persona_seleccionada);

            $this->s__datos_persona['id_persona'] = $persona->get_id_persona();
            $this->s__datos_persona['apellidos'] = $persona->get_apellidos();
            $this->s__datos_persona['nombres'] = $persona->get_nombres();
            $this->s__datos_persona['fecha_nacimiento'] = $persona->get_fecha_nacimiento();
            $this->s__datos_persona['correo_electronico'] = $persona->get_correo_electronico();
            $this->s__datos_persona['recibe_notif_x_correo'] = $persona->get_recibe_notif_x_correo();
            $this->s__datos_persona['telefono'] = $persona->get_telefono();
            $this->s__datos_persona['id_localidad_nacimiento'] = $persona->get_id_localidad_nacimiento();
            $this->s__datos_persona['id_localidad_residencia'] = $persona->get_id_localidad_residencia();
            $this->s__datos_persona['id_nacionalidad'] = $persona->get_id_nacionalidad();
            $this->s__datos_persona['activo'] = $persona->get_estado_persona();
            $this->s__datos_persona['id_sexo'] = $persona->get_sexo_activo();
            $this->s__datos_persona['es_alumno'] = $persona->get_es_alumno();
            $this->s__datos_persona['usuario'] = $persona->get_usuario();

            $this->s__datos_persona['id_alumno'] = $persona->get_id_alumno();
            $this->s__datos_persona['legajo'] = $persona->get_legajo();
            $this->s__datos_persona['extranjero'] = $persona->get_extranjero();
            $this->s__datos_persona['regular'] = $persona->get_regular();
            $this->s__datos_persona['id_motivo_desercion'] = $persona->get_id_motivo_desercion();
            $this->s__datos_persona['es_celiaco'] = $persona->get_es_celiaco();
            $this->s__datos_persona['direccion_calle'] = $persona->get_direccion_calle();
            $this->s__datos_persona['direccion_numero'] = $persona->get_direccion_numero();
            $this->s__datos_persona['direccion_piso'] = $persona->get_direccion_piso();
            $this->s__datos_persona['direccion_depto'] = $persona->get_direccion_depto();

            $documentos = $persona->get_documentos();
            if (is_array($documentos)) {
                foreach (array_keys($documentos) as $i) {
                    $this->s__documentos[] = array( "id_tipo_documento"=>$documentos[$i]["id_tipo_documento"]
                                                    ,"id"=>$documentos[$i]["id_tipo_documento"]
                                                    ,"tipo_documento"=>$documentos[$i]["documento_largo"]
                                                    ,"identificacion"=>$documentos[$i]["identificacion"]
                                                    ,"activo_completo"=>$documentos[$i]["activo_completo"]
                                                    ,"activo"=>$documentos[$i]["activo"]);
                }
            }
            $this->s__datos_allegados = $persona->get_alumnos_vinculados();
        }
    }

    public function conf__pant_datos_persona()
    {
        $this->activar_pestanias();
    }

    public function conf__pant_domicilios()
    {
        $this->activar_pestanias();
    }

    public function conf__pant_documentos()
    {
        $this->activar_pestanias();
    }

    public function conf__pant_datos_alumno()
    {
        $this->activar_pestanias();
    }

    public function conf__pant_datos_allegados()
    {
        $this->activar_pestanias();
    }

    public function activar_pestanias()
    {
        //Activo/desactivo la pantalla Alumno en función del valor del ef es_alumno
        if (!empty($this->s__datos_persona)) {
            if ($this->s__datos_persona['es_alumno'] == 'N') {
                $this->pantalla()->tab('pant_datos_alumno')->desactivar();
                $this->pantalla()->tab('pant_datos_allegados')->activar();
            } else {
                $this->pantalla()->tab('pant_datos_alumno')->activar();
                $this->pantalla()->tab('pant_datos_allegados')->desactivar();
            }
        }
    }

    public function controlar_alta_documento($datos)
    {
        if ($this->s__documentos) {
            foreach ($this->s__documentos as $doc) {
                if (!isset($doc['estado']) || $doc['estado'] != 'B') {
                    if ( ($doc['id'] == $datos['id']) && ($doc['identificacion'] == $datos['identificacion']) ) {
                        throw new toba_error("El tipo y número de documento ya está dado de alta.");
                    }
                }
            }
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_persona($this->s__datos_persona);
        //$this->cn()->set_domicilios($this->s__domicilios);
        $this->cn()->set_documentos($this->s__documentos);
        $this->cn()->set_datos_alumno($this->s__datos_alumno);
    }

    public function validar_documento($datos)
    {
        if (empty($datos['identificacion'])) {
            throw new toba_error("El número de documento es obligatorio.");
        }
        if (empty($datos['id'])) {
            throw new toba_error("El tipo de documento es obligatorio.");
        }
        if ($datos['id'] == 8 && !preg_match('/^\d{8}$/', $datos['identificacion'])) {
            throw new toba_error("El número de documento debe tener 8 dígitos para el tipo DNI.");
        }
        if ($datos['id'] == 6  && !preg_match('/^\d{11}$/', $datos['identificacion'])) {
            throw new toba_error("El número de documento debe tener 11 dígitos para el tipo CUIT.");
        }
        if ($datos['id'] == 7  && !preg_match('/^\d{11}$/', $datos['identificacion'])) {
            throw new toba_error("El número de documento debe tener 11 dígitos para el tipo CUIL.");
        }
    }

    //---- pant_persona -----------------------------------------------------------------

    //---- form_datos_persona -----------------------------------------------------------

    public function conf__form_datos_persona($form)
    {
        $form->set_datos($this->s__datos_persona);
    }

    public function evt__form_datos_persona__modificacion($datos)
    {
        $this->s__datos_persona = array_merge($this->s__datos_persona, $datos);
    }

    //---- FIN pant_persona -------------------------------------------------------------

    //---- pant_documentos --------------------------------------------------------------

    //---- form_documentos --------------------------------------------------------------

	public function conf__form_documentos($form)
	{
        $datos = array();
        if (isset($this->s__documento_seleccionado)) {
            foreach ($this->s__documentos as $documento) {
                if ($documento['id_tipo_documento'] == $this->s__documento_seleccionado) {
                    $datos = $documento;
                    break;
                }
            }
            $form->set_datos($datos);
        }
    }

	public function evt__form_documentos__alta($datos)
	{
        $this->validar_documento($datos);

        $datos['estado'] = 'A'; //Se marca para darlo de alta luego en la base
        $this->controlar_alta_documento($datos);
        $datos['tipo_documento'] = dao_consultas::get_tipos_documentos($datos)[0]['nombre'];
        $datos['id_tipo_documento'] = $datos['id'];
        $this->s__documentos[] = $datos;
    }

    public function evt__form_documentos__modificacion($datos)
	{
        //Busco el documento con el id_tipo_documento correspondiente
        foreach ($this->s__documentos as &$documento) {
            if ($documento['id_tipo_documento'] == $this->s__documento_seleccionado) {
                //Actualizo los datos del documento encontrado
                $documento = array_merge($documento, $datos);
                break;
            }
        }
        unset($this->s__documento_seleccionado);
	}

	public function evt__form_documentos__cancelar()
	{
        unset($this->s__documento_seleccionado);
	}

	//---- cuadro_documentos ------------------------------------------------------------

	public function conf__cuadro_documentos($cuadro)
	{
        if (!empty($this->s__documentos)) {
            $salida = array();
            foreach ($this->s__documentos as $documento) {
                //Convertir el campo 'activo' en 'activo_completo'
                if ($documento['activo'] == 'A') {
                    $documento['activo_completo'] = 'Si';
                } else {
                    $documento['activo_completo'] = 'No';
                }

                // Solo agregar los documentos que no tienen estado 'B'
                if (!isset($documento['estado']) || $documento['estado'] != 'B') {
                    $salida[] = $documento;
                }
            }
            $cuadro->set_datos($salida);
        }
	}

	public function evt__cuadro_documentos__seleccion($seleccion)
	{
	    $this->s__documento_seleccionado = $seleccion['id_tipo_documento'];
	}

	public function evt__cuadro_documentos__eliminar($seleccion)
	{
        if (isset($seleccion['id_tipo_documento'])) {
            foreach ($this->s__documentos as $d => $documento) {
                if ($documento['id_tipo_documento'] == $seleccion['id_tipo_documento']) {
                    unset($this->s__documentos[$d]);
                    break;
                }
            }
        }
	}

    //---- FIN pant_documentos ----------------------------------------------------------

    //---- pant_datos_alumno ------------------------------------------------------------

    //---- form_datos_alumno ------------------------------------------------------------

    function conf__form_datos_alumno($form)
    {
        $form->set_datos($this->s__datos_persona);
    }

    function evt__form_datos_alumno__modificacion($datos)
    {
        $this->s__datos_persona = array_merge($this->s__datos_persona, $datos);
    }

    //---- FIN pant_datos_alumno ---------------------------------------------------------

    //---- cuadro_alumnos_vinculados -----------------------------------------------------

    public function conf__cuadro_alumnos_vinculados($cuadro)
    {
        if (!empty($this->s__datos_allegados)) {
            $cuadro->set_datos($this->s__datos_allegados);
        }
    }

    //---- FIN pant_datos_allegados ------------------------------------------------------

	//---- Eventos ----------------------------------------------------------------------

    public function evt__procesar()
	{
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        if (strtolower($rta['rta']) == 'ok') {
            $this->limpiar_memoria();
            $this->controlador()->disparar_limpieza_memoria();
            $this->controlador()->set_pantalla("seleccion");
        } else {
            throw new toba_error("Ha ocurrido un error inesperado: $rta");
        }
	}

    public function evt__procesar_y_continuar()
	{
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        if (strtolower($rta['rta']) == 'ok') {
            if (isset($rta['id_persona'])) {
                $this->s__datos_persona['id_persona'] = $rta['id_persona'];
            }
        } else {
            throw new toba_error("Ha ocurrido un error inesperado: $rta");
        }
	}

    public function evt__cancelar()
	{
        $this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
        $this->controlador()->set_pantalla('seleccion');
	}

    public function validar()
    {
        //este método puede q no sea necesario, ya que puedo hacer todo desde el validar del cn
    }

}