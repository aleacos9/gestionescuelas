<?php
class ci_alta_usuarios extends gestionescuelas_ext_ci
{
    protected $s__datos_seleccion;
    protected $s__db_toba = 'toba_3';

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        $cuadro->set_datos(dao_consultas::get_tutores_con_allegados());
    }

    public function evt__cuadro__seleccion($datos)
    {
        $this->s__datos_seleccion = $datos;
    }

    //---- eventos -----------------------------------------------------------------------

    public function evt__procesar()
    {
        if (!empty($this->s__datos_seleccion)) {
            foreach ($this->s__datos_seleccion as $clave => $fila) {
                if ($fila['correo_electronico'] != '') {
                    self::crear_usuario($fila);
                }
            }
        }
    }

    public function evt__cancelar()
    {
        unset($this->s__datos_seleccion);
    }

    function crear_usuario($datos)
    {
        try {
            $proyecto = 'gestionescuelas';
            $grupo_acceso = 'tutor';

            $nombre = quote($datos['persona']);
            $username = quote(addslashes($datos['identificacion']));
            $email = quote($datos['correo_electronico']);
            $clave = addslashes($datos['identificacion']);

            $algoritmo = 'bcrypt';
            $clave = encriptar_con_sal($clave, $algoritmo);

            $usuario = self::validar_si_existe_usuario($datos);
            if ($usuario) {
                try {
                    $sql = "INSERT INTO desarrollo.apex_usuario (usuario, nombre, clave,autentificacion,email) VALUES ($username, $nombre,'" . $clave . "','$algoritmo', $email)";
                    toba::db($this->s__db_toba)->ejecutar($sql);
                    $sql = "INSERT INTO desarrollo.apex_usuario_proyecto (proyecto, usuario_grupo_acc, usuario) VALUES ('$proyecto', '$grupo_acceso', $username);";
                    toba::db($this->s__db_toba)->ejecutar($sql);
                } catch (toba_error $error) {
                    toba::notificacion()->agregar('error ' . $error->get_mensaje_log());
                }
            } else {
                toba::notificacion()->agregar('error ', 'El usuario ya existe.');
            }

        } catch (toba_error $error) {
            toba::notificacion()->agregar('error ' . $error->get_mensaje_log());
        }
    }

    public function validar_si_existe_usuario($datos)
    {
        if (!empty($datos)) {
            if (!empty($datos['identificacion'])) {
                $sql = "SELECT usuario
                        FROM desarrollo.apex_usuario
                        WHERE usuario = '".$datos['identificacion']."'";

                $datos = toba::db($this->s__db_toba)->consultar($sql);
                if (!empty($datos)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }



}
