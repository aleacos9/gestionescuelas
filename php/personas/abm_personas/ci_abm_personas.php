<?php
class ci_abm_personas extends gestionescuelas_ext_ci
{
    protected $s__datos_persona = array();
    protected $s__domicilios = array();
    protected $s__documentos = array();
    protected $s__datos_alumno = array();
    protected $s__dar_baja = false;

    //---- Funciones ---------------------------------------------------------------------

    /*
     * Retorna los tipos de documentos
     */
    public function get_tipos_documentos_ci()
    {
        return dao_consultas::get_tipos_documentos();
    }

    /*
     * Retorna los tipos de documentos
     */
    public function get_sexo_ci()
    {
        return dao_consultas::get_sexo();
    }

    public function get_persona_editar()
    {
        return $this->s__persona_editar;
    }

    //---- filtro -----------------------------------------------------------------------

    public function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
    }

    public function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $datos = dao_personas::get_datos_personas($this->s__datos_filtro);
            if (!empty($datos)) {
                $this->dep('filtro')->colapsar();
            }
            $cuadro->set_datos($datos);
        }
    }

    function evt__cuadro__seleccion($seleccion)
	{
        $this->s__persona_editar = $seleccion['id_persona'];
        $this->set_pantalla('edicion');
	}

    function evt__cuadro__eliminar($seleccion)
    {
        $persona = new persona($seleccion['id_persona']);

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

        $this->s__dar_baja = true;
        $this->set_datos_cn();
        $this->cn()->procesar();
        $this->controlador()->disparar_limpieza_memoria(array('s__datos_filtro'));
    }

    /* este método solo será ocupado por el evt__cuadro__eliminar */
    public function set_datos_cn()
    {
        $this->cn()->set_datos_persona($this->s__datos_persona);
        //$this->cn()->set_domicilios($this->s__domicilios);
        $this->cn()->set_documentos($this->s__documentos);
        $this->cn()->set_datos_alumno($this->s__datos_alumno);
        $this->cn()->set_dar_baja($this->s__dar_baja);
    }
}