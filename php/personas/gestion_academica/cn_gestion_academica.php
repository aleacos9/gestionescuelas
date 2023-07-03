<?php
class cn_gestion_academica extends gestionescuelas_cn
{
    protected $datos_getion_academica;

    public function evt__procesar_especifico()
    {
        $ok = 'ok';
        $this->validar();
        $persona = array();

        foreach ($this->datos_getion_academica as $datos_academicos) {
            $persona = new persona($datos_academicos['id_alumno']);
            break;
        }
        $persona->set_datos_academicos($this->datos_getion_academica);
        $persona->grabar_datos_getion_academica();
        return $ok;
    }

    public function validar()
    {
        //Valido que se inserte al menos 1 allegado
        if (($this->datos_getion_academica) == null) {
            throw new toba_error('Para procesar debe ingresar datos.');
        }
    }

    public function set_datos_academicos($datos)
    {
        $this->datos_getion_academica = $datos;
    }
}