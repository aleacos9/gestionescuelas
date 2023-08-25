<?php
class ci_deuda_corriente extends ci_cuenta_corriente
{
    protected $s__datos_deuda_corriente = array();
    protected $s__nivel_actual_cursada;
    protected $s__importe_cuota_actual;
    protected $s__importe_materiales;
    protected $s__importe_inscripcion_inicial;
    protected $s__importe_inscripcion_primario;

    public function ini()
    {
        parent::ini();
        if (dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") == 'SI') {
            $this->s__importe_cuota_actual = dao_consultas::catalogo_de_parametros("importe_mensual_cuota");
            $this->s__importe_materiales = dao_consultas::catalogo_de_parametros("importe_materiales");
            $this->s__importe_inscripcion_inicial = dao_consultas::catalogo_de_parametros("importe_inscripcion_inicial");
            $this->s__importe_inscripcion_primario = dao_consultas::catalogo_de_parametros("importe_inscripcion_primario");
        }
    }

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_deuda_corriente = $persona->get_datos_deuda_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
            $this->s__nivel_actual_cursada = $persona->get_nivel_actual_cursada();
        }
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        //Si el parámetro actualiza_deuda_a_valor_cuota_actual NO está en S => oculto la columna donde se muestra el saldo actualizado
        if (dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") != 'SI') {
            $cuadro->eliminar_columnas(array('saldo_actualizado'));
        } else {
            $cuadro->set_titulo('<div class="titulo_alumno">'."IMPORTANTE: El valor de las cuotas atrasadas, se actualizará al valor actual de la cuota".'</div>');
        }

        if (isset($this->s__perfil_funcional) && in_array('tutor', $this->s__perfil_funcional) && isset($this->s__id_persona)) {
            $obj_persona = new persona($this->s__id_persona);
            $alumnos_vinculados = $obj_persona->get_alumnos_vinculados();
            if (isset($alumnos_vinculados)) {
                foreach ($alumnos_vinculados as &$alumno) {
                    $alumno['saldo_actualizado'] = 0;
                    $obj_alumno = new persona($alumno['id_persona_alumno']);
                    $saldo_deuda_corriente = $obj_alumno->get_saldo_deuda_corriente();
                    if (isset($saldo_deuda_corriente) && dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") == 'SI') {
                        $deuda_corriente = $obj_alumno->get_datos_deuda_corriente();
                        if (isset($deuda_corriente)) {
                            foreach ($deuda_corriente as $deuda) {
                                if (!$deuda['id_medio_pago']) {
                                    $saldo_a_sumar = 0;
                                    switch ($deuda['id_cargo_cuenta_corriente']) {
                                        case constantes::get_valor_constante('INSCRIPCION_ANUAL'):
                                            $saldo_a_sumar = ($this->s__importe_inscripcion_inicial - $deuda['importe']) + $deuda['importe'];
                                            break;
                                        case constantes::get_valor_constante('CUOTA_MENSUAL'):
                                            $saldo_a_sumar = ($this->s__importe_cuota_actual - $deuda['importe']) + $deuda['importe'];
                                            break;
                                        case constantes::get_valor_constante('MATERIALES'):
                                            $saldo_a_sumar = ($this->s__importe_materiales - $deuda['importe']) + $deuda['importe'];
                                            break;
                                    }
                                    $alumno['saldo_actualizado'] += $saldo_a_sumar;
                                }
                            }
                        }
                    }
                    if (isset($saldo_deuda_corriente)) {
                        $alumno['saldo'] = $saldo_deuda_corriente['saldo'];
                    }
                }
                unset($alumno);
            }
            $cuadro->set_datos($alumnos_vinculados);
        }
    }

    //---- cuadro_deuda_corriente ------------------------------------------------------

    function conf__cuadro_deuda_corriente($cuadro)
    {
        //Si el parámetro actualiza_deuda_a_valor_cuota_actual NO está en S => oculto la columna donde se muestra el saldo actualizado
        if (dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") != 'SI') {
            $cuadro->eliminar_columnas(array('importe_actualizado'));
        }

        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">' . $this->s__nombre_alumno . '</div>');
        }
        //Si el parámetro actualiza_deuda_a_valor_cuota_actual está en S => actualizo la deuda
        if (isset($this->s__datos_deuda_corriente) && dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") == 'SI') {
            foreach ($this->s__datos_deuda_corriente as &$deuda) {
                $importe = $deuda['importe'];
                /*
                 * Solo hago las actualizaciones de los importes en el caso de que no sea un pago
                 * esto es necesario aclararlo pq la consulta de deuda corriente trae aquellos registros
                 * cuyo pago sea parcial (mas o menos)
                 */
                $deuda['importe_actualizado'] = null;
                if (!$deuda['id_medio_pago']) {
                    switch ($deuda['id_cargo_cuenta_corriente']) {
                        case constantes::get_valor_constante('INSCRIPCION_ANUAL'):
                            $importe_actualizado = 0;
                            if ($this->s__nivel_actual_cursada == constantes::get_valor_constante('NIVEL_INICIAL')) {
                                $importe_actualizado = ($this->s__importe_inscripcion_inicial - $importe) + $importe;
                            } elseif ($this->s__nivel_actual_cursada == constantes::get_valor_constante('NIVEL_PRIMARIO')) {
                                $importe_actualizado = ($this->s__importe_inscripcion_primario - $importe) + $importe;
                            }
                            $deuda['importe_actualizado'] = $importe_actualizado;
                            break;
                        case constantes::get_valor_constante('CUOTA_MENSUAL'):
                            $deuda['importe_actualizado'] = ($this->s__importe_cuota_actual - $importe) + $importe;
                            break;
                        case constantes::get_valor_constante('MATERIALES'):
                            $deuda['importe_actualizado'] = ($this->s__importe_materiales - $importe) + $importe;
                            break;
                    }
                    unset($deuda);
                }
            }
            $cuadro->set_datos($this->s__datos_deuda_corriente);
        }
    }
}