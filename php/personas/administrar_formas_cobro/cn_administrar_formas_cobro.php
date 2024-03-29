<?php
class cn_administrar_formas_cobro extends gestionescuelas_cn
{
    protected $datos_formas_cobro;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';
        $this->validar();
        $persona = array();

        foreach ($this->datos_formas_cobro as $formas_cobro) {
            $persona = new persona($formas_cobro['id_alumno']);
            break;
        }
        $persona->set_datos_formas_cobro($this->datos_formas_cobro);
        $persona->grabar_datos_formas_cobro();
        return $ok;
    }

    public function validar()
    {
        //Valido que se inserte al menos 1 allegado
        if (($this->datos_formas_cobro) == null) {
            throw new toba_error('Para procesar debe ingresar datos.');
        }
    }

    public function set_datos_formas_cobro($datos)
    {
        $this->datos_formas_cobro = $datos;
    }
}