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

        $rta['rta'] = 'ok';
        if (!isset($this->datos_persona['id_persona'])) {
            $rta['id_persona'] = $persona->get_persona_insertada();
        }
        return $rta;
	}

    public function validar()
    {
        if (!$this->dar_baja) {
            if (empty($this->documentos)) {
                throw new toba_error("Debe al menos cargar un documento.");
            }
            // Validaci�n de cada documento
            foreach ($this->documentos as $documento) {
                // Verificar que 'id_tipo_documento' y 'identificacion' est�n definidos
                if (empty($documento['id_tipo_documento']) || empty($documento['identificacion'])) {
                    throw new toba_error("El tipo de documento y el n�mero son obligatorios.");
                }

                // Validaciones espec�ficas seg�n el tipo de documento
                switch ($documento['tipo_documento']) {
                    case 'Documento Nacional de Identidad':
                        if (!preg_match('/^\d{8}$/', $documento['identificacion'])) {
                            throw new toba_error("El n�mero debe tener 8 d�gitos para el Documento Nacional de Identidad (DNI).");
                        }
                        break;

                    case 'Clave �nica de identificaci�n laboral':
                        if (!preg_match('/^\d{11}$/', $documento['identificacion'])) {
                            throw new toba_error("El CUIL debe consistir en 11 d�gitos.");
                        }
                        break;

                    case 'Clave �nica de identificaci�n tributaria':
                        if (!preg_match('/^\d{11}$/', $documento['identificacion'])) {
                            throw new toba_error("El CUIT debe consistir en 11 d�gitos.");
                        }
                        break;

                    case 'Pasaporte':
                        // Validar seg�n tu criterio (ej. longitud)
                        if (strlen($documento['identificacion']) < 6) { // Ejemplo: m�nimo 6 caracteres
                            throw new toba_error("El n�mero de pasaporte debe tener al menos 6 caracteres.");
                        }
                        break;

                    default:
                        throw new toba_error("Tipo de documento no reconocido.");
                }
            }
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