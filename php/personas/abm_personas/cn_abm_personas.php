<?php
class cn_abm_personas extends gestionescuelas_cn
{
    protected $datos_persona;
    protected $domicilios;
    protected $documentos;
    protected $datos_alumno;
    protected $dar_baja;

    public function evt__procesar_especifico()
	{
        $this->validar();

        if (isset($this->datos_persona['id_persona'])) {
            $persona = new persona($this->datos_persona['id_persona']);
        } else {
            $persona = new persona();
        }

        $persona->set_datos_persona($this->datos_persona);
        $persona->set_persona_documentos($this->documentos);
        $persona->set_datos_alumno($this->datos_persona);

        if ($this->dar_baja) {
            $persona->set_activo('B');
        }
        $persona->grabar();

        $mensaje = '';
        if ($this->dar_baja) {
            $mensaje .= " La persona fue dada de baja.";
            toba::notificacion()->agregar($mensaje, 'info');
        }
	    return "ok";
	}

    public function validar()
    {
        if (!$this->dar_baja) {
            if (empty($this->documentos)) {
                throw new toba_error("Debe al menos cargar un documento.");
            }

            /*if (empty($this->domicilios)) {
                throw new toba_error("Debe cargar al menos un domicilio.");
            }*/
        }
    }

    public function set_datos_persona($datos)
    {
        $this->datos_persona = $datos;
    }

    public function set_domicilios($datos)
    {
        $this->domicilios = $datos;
    }

    public function set_documentos($datos)
    {
        $this->documentos = $datos;
    }

    public function set_datos_alumno($datos)
    {
        $this->datos_alumno = $datos;
    }

    public function set_dar_baja($baja)
    {
        $this->dar_baja = (bool)$baja;
    }

}