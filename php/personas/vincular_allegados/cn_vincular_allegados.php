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
        //agregar todas las validaciones necesarias
    }

    public function set_datos_allegados($datos)
    {
        $this->datos_allegados = $datos;
    }
}