<?php
class ci_alta_usuarios extends gestionescuelas_ext_ci
{
    protected $s__datos_seleccion;
    protected $s__array_datos_invalidos = array();
    protected $s__array_usuarios_creados = array();
    protected $s__db_toba = 'toba_3_3';

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
        $this->s__array_datos_invalidos = array();
        if (!empty($this->s__datos_seleccion)) {
            foreach ($this->s__datos_seleccion as $clave => $fila) {
                if ($this->validar_si_existe_usuario($fila) == false) {
                    unset($fila[$clave]);
                } else {
                    if ($this->validar_datos_cargados($fila) == false) {
                        unset($fila[$clave]);
                    } else {
                        $this->crear_usuario($fila);
                    }
                }
            }
            $this->mostrar_mensajes();
        }
    }

    public function evt__cancelar()
    {
        unset($this->s__datos_seleccion);
    }

    public function crear_usuario($datos)
    {
        $proyecto = 'gestionescuelas';
        $grupo_acceso = 'tutor';

        $nombre = quote($datos['persona']);
        $username = quote(addslashes($datos['identificacion']));
        $email = quote($datos['correo_electronico']);
        $clave = addslashes($datos['identificacion']);

        $algoritmo = 'bcrypt';
        $clave = encriptar_con_sal($clave, $algoritmo);

        try {
            $sql = "INSERT INTO desarrollo.apex_usuario (usuario, nombre, clave,autentificacion,email) VALUES ($username, $nombre,'" . $clave . "','$algoritmo', $email)";
            toba::db($this->s__db_toba)->ejecutar($sql);
            $sql = "INSERT INTO desarrollo.apex_usuario_proyecto (proyecto, usuario_grupo_acc, usuario) VALUES ('$proyecto', '$grupo_acceso', $username);";
            toba::db($this->s__db_toba)->ejecutar($sql);

            $this->s__array_datos_invalidos['usuarios'][] = $datos['identificacion'];
        } catch (toba_error $error) {
            toba::notificacion()->agregar('error ' . $error->get_mensaje_log());
        }
    }

    protected function validar_si_existe_usuario($persona)
    {
        if (!empty($persona)) {
            if (!empty($persona['identificacion'])) {
                $sql = "SELECT usuario
                        FROM desarrollo.apex_usuario
                        WHERE usuario = '".$persona['identificacion']."'";

                $datos = toba::db($this->s__db_toba)->consultar($sql);
                if (empty($datos)) {
                    return true;
                } else {
                    $this->s__array_datos_invalidos['personas_repetidas'][] = $persona['identificacion'];
                    return false;
                }
            }
        }
    }

    protected function validar_datos_cargados($datos)
    {
        if (!empty($datos)) {
            if (!empty($datos['correo_electronico'])) {
                return true;
            } else {
                $this->s__array_datos_invalidos['correo_no_cargado'][] = $datos['identificacion'];
                return false;
            }
        }
    }

    protected function mostrar_mensajes()
    {
        if (isset($this->s__array_datos_invalidos)) {
            $array_datos_invalidos = $this->s__array_datos_invalidos;
            $errores = $this->cargar_mensajes_error($array_datos_invalidos);
        }

        //Se prepara el mensaje de error para la notificación
        if (!empty($errores)) {
            $str_error = '';
            foreach ($errores as $error) {
                $str_error .= $error."<br />";
            }
            toba::notificacion()->agregar($str_error, 'info');
        }
    }

    protected function cargar_mensajes_error($array_datos_invalidos)
    {
        $mensaje = '';
        $errores = [];
        if (isset($array_datos_invalidos['correo_no_cargado'])) {
            foreach ($array_datos_invalidos['correo_no_cargado'] as $clave => $dni) {
                $mensaje .= $dni."<br />";
            }
            $errores[] = "A el/los siguiente/s Nro/s de Documento/s no se les creó el usuario ya que no tienen un correo asociado. Por favor, asócielos desde el ABM de Personas: <br />".$mensaje;
            unset($mensaje);
        }

        $mensaje = '';
        if (isset($array_datos_invalidos['personas_repetidas'])) {
            foreach ($array_datos_invalidos['personas_repetidas'] as $clave => $dni) {
                $mensaje .= $dni."<br />";
            }
            $errores[] = "El/Los siguiente/s Nro/s de Documento/s ya tienen usuario generados y serán omitidos: <br />".$mensaje;
            unset($mensaje);
        }

        $mensaje = '';
        if (isset($array_datos_invalidos['usuarios'])) {
            foreach ($array_datos_invalidos['usuarios'] as $clave => $dni) {
                $mensaje .= $dni."<br />";
            }
            $errores[] = "A el/los siguiente/s Nro/s de Documento/s se les generó un usuario: <br />".$mensaje;
            unset($mensaje);
        }

        return $errores;
    }
}
