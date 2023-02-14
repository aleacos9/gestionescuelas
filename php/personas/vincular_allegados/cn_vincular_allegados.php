<?php
class cn_vincular_allegados extends gestionescuelas_cn
{
    protected $datos_allegados;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';
        $this->validar();
        $persona = array();

        foreach ($this->datos_allegados as $allegado) {
            $persona = new persona($allegado['id_alumno']);
            break;
        }
        $persona->set_allegados($this->datos_allegados);
        $persona->grabar_persona_allegados();
        return $ok;
    }

    public function validar()
    {
        //Valido que se inserte al menos 1 allegado
        if (($this->datos_allegados) == null) {
            throw new toba_error('Para procesar debe ingresar datos.');
        }
    }

    public function set_datos_allegados($datos)
    {
        $this->datos_allegados = $datos;
    }
}