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

    public function activar_pestanias()
    {
        //Activo/desactivo la pantalla Alumno en función del valor del ef es_alumno
        if (!empty($this->s__datos_persona)) {
            if ($this->s__datos_persona['es_alumno'] == 'N') {
                $this->pantalla()->tab('pant_datos_alumno')->desactivar();
            } else {
                $this->pantalla()->tab('pant_datos_alumno')->activar();
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
            foreach (array_keys($this->s__documentos) as $k) {
                if ($k == $this->s__documento_seleccionado) {
                    $datos = $this->s__documentos[$k];
                }
            }
            $form->set_datos($datos);
        }
    }

	public function evt__form_documentos__alta($datos)
	{
        $this->s__documentos[] = $datos;
	}

	public function evt__form_documentos__modificacion($datos)
	{
        $this->s__documentos[$this->s__documento_seleccionado] = $datos;
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
            foreach (array_keys($this->s__documentos) as $k) {
                $this->s__documentos[$k]['id_tipo_documento'] = $k;
            }
            $cuadro->set_datos($this->s__documentos);
        }
	}

	public function evt__cuadro_documentos__seleccion($seleccion)
	{
	    $this->s__documento_seleccionado = $seleccion['id_tipo_documento'];
	}

	public function evt__cuadro_documentos__eliminar($seleccion)
	{
        foreach (array_keys($this->s__documentos) as $d) {
            if ($d == $seleccion['id']) {
                unset($this->s__documentos[$d]);
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

	//---- Eventos ----------------------------------------------------------------------

    public function evt__procesar()
	{
        $this->validar();
        $this->set_datos_cn();
        $rta = $this->cn()->procesar();
        if (strtolower($rta) == 'ok') {
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
        if (strtolower($rta) == 'ok') {
            //$this->limpiar_memoria();
            //$this->controlador()->disparar_limpieza_memoria();
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
?>