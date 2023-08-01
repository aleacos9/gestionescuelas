<?php
class ci_deuda_corriente extends ci_cuenta_corriente
{
    protected $s__datos_deuda_corriente = array();

    public function cargar_datos()
    {
        if (!empty($this->s__alumno_editar)) {
            $persona = new persona($this->s__alumno_editar);
            $this->s__datos_deuda_corriente = $persona->get_datos_deuda_corriente();
            $this->s__nombre_alumno = $persona->get_nombre_completo_alumno();
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

        if (isset($this->s__perfil_funcional)) {
            if ((in_array('tutor', $this->s__perfil_funcional))) {
                if (isset($this->s__id_persona)) {
                    $obj_persona = new persona($this->s__id_persona);
                    $alumnos_vinculados = $obj_persona->get_alumnos_vinculados();
                    //Debo recorrer cada alumno asociado a la persona y obtener el saldo de la deuda corriente y el saldo actualizado
                    if (isset($alumnos_vinculados)) {
                        foreach (array_keys($alumnos_vinculados) as $alumno) {
                            $obj_alumno = new persona($alumnos_vinculados[$alumno]['id_persona_alumno']);
                            $saldo_deuda_corriente = $obj_alumno->get_saldo_deuda_corriente();
                            $total_cuotas_adeudadas = $obj_alumno->get_total_cuotas_adeudadas();
                            if (isset($saldo_deuda_corriente)) {
                                //Si el parámetro actualiza_deuda_a_valor_cuota_actual está en S => le actualizo el saldo
                                if (dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") == 'SI') {
                                    $deuda_corriente = $obj_alumno->get_datos_deuda_corriente();
                                    if (isset($deuda_corriente)) {
                                        $importe_cuota_actual = dao_consultas::catalogo_de_parametros("importe_mensual_cuota");
                                        foreach (array_keys($deuda_corriente) as $deuda) {
                                            $deuda_corriente[$deuda]['saldo_mensual_actualizado'] = ($importe_cuota_actual - $deuda_corriente[$deuda]['importe']) + $deuda_corriente[$deuda]['importe'];
                                            $alumnos_vinculados[$alumno]['saldo_actualizado'] = ($deuda_corriente[$deuda]['saldo_mensual_actualizado']) * $total_cuotas_adeudadas;
                                        }
                                    }
                                }
                                $alumnos_vinculados[$alumno]['saldo'] = $saldo_deuda_corriente['saldo'];
                            }
                        }
                    }
                    $cuadro->set_datos($alumnos_vinculados);
                }
            }
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
        if (isset($this->s__datos_deuda_corriente)) {
            //Si el parámetro actualiza_deuda_a_valor_cuota_actual está en S => actualizo la deuda
            if (dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual") == 'SI') {
                foreach (array_keys($this->s__datos_deuda_corriente) as $deuda) {
                    $importe_cuota_actual = dao_consultas::catalogo_de_parametros("importe_mensual_cuota");
                    $this->s__datos_deuda_corriente[$deuda]['importe_actualizado'] = ($importe_cuota_actual - $this->s__datos_deuda_corriente[$deuda]['importe']) + $this->s__datos_deuda_corriente[$deuda]['importe'];
                }
            }
            $cuadro->set_datos($this->s__datos_deuda_corriente);
        }
    }
}