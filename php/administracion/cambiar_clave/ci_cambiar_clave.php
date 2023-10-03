<?php

class ci_cambiar_clave extends gestionescuelas_ext_ci
{
    //---- Inicializacion ---------------------------------------------------------------

    public function ini()
    {
    }

    public function ini__operacion()
    {
    }

    //---- Config. ----------------------------------------------------------------------

    public function conf()
    {
    }

    //---- Configuracion de Pantallas ---------------------------------------------------

    public function conf__pant_inicial($pantalla)
    {
    }

    //---- DEPENDENCIAS -----------------------------------------------------------------

    //---- formulario -------------------------------------------------------------------

    public function evt__formulario__modificacion($datos)
    {
        $usuario = $datos['usuario'];
        if (toba::manejador_sesiones()->invocar_autenticar($usuario, $datos['clave_vieja'], null)) {		//Si la clave anterior coincide
            if ($datos['clave_nueva'] == $datos['clave_nueva_confirmar']) {
                //Obtengo el largo minimo de la clave
                $largo_clave = toba::proyecto()->get_parametro('proyecto', 'pwd_largo_minimo', null, false);
                try {
                    toba_usuario::verificar_composicion_clave($datos['clave_nueva'], $largo_clave);

                    //Obtengo los dias de validez de la nueva clave
                    $dias = toba::proyecto()->get_parametro('dias_validez_clave', null, false);
                    toba_usuario::verificar_clave_no_utilizada($datos['clave_nueva'], $usuario);
                    toba_usuario::reemplazar_clave_vencida($datos['clave_nueva'], $usuario, $dias);
                    toba::notificacion()->agregar("Su clave de acceso fue modificada!", 'info');
                } catch (toba_error_pwd_conformacion_invalida $e) {
                    toba::logger()->info($e->getMessage());
                    toba::notificacion()->agregar($e->getMessage(), 'error');
                    return;
                }
            } else {
                toba::notificacion()->agregar("La clave y la confirmacion de la clave no son iguales!");
                return;
            }
        } else {
            throw new toba_error_usuario('La clave ingresada no es correcta');
        }
    }

    public function evt__formulario__cancelar()
    {
    }

    //El formato del retorno debe ser array('id_ef' => $valor, ...)
    public function conf__formulario($componente)
    {
        $datos = array( 'usuario' => toba::usuario()->get_id(),
                        'nombre' => toba::usuario()->get_nombre() );
        return $datos;
    }

    protected function encriptar_clave($clave, $metodo, $sal=null)
    {
        if ($metodo != 'md5') {
            return encriptar_con_sal($clave, $metodo, $sal);
        } else {
            return hash($metodo, $clave);
        }
    }
}