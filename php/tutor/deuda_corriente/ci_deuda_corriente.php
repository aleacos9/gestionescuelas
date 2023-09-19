<?php
class ci_deuda_corriente extends ci_cuenta_corriente
{
    protected $s__datos_deuda_corriente = array();
    protected $s__nivel_actual_cursada;
    protected $s__importe_cuota_actual;
    protected $s__importe_materiales;
    protected $s__importe_inscripcion_inicial;
    protected $s__importe_inscripcion_primario;
    protected $s__mostrar_importe_actualizado;

    public function ini()
    {
        parent::ini();
        $this->s__mostrar_importe_actualizado = dao_consultas::catalogo_de_parametros("actualiza_deuda_a_valor_cuota_actual");
        if ($this->s__mostrar_importe_actualizado == 'SI') {
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
        if ($this->s__mostrar_importe_actualizado != 'SI') {
            $cuadro->eliminar_columnas(array('saldo_actualizado'));
        } else {
            $cuadro->set_titulo('<div class="titulo_alumno">' . "IMPORTANTE: El valor de las cuotas atrasadas, se actualizará al valor actual de la cuota" . '</div>');
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
                        //Crear un array auxiliar para rastrear cuántas veces aparece cada id_alumno_cc
                        $repeticiones_id_alumno_cc = [];
                        $deuda_corriente = $obj_alumno->get_datos_deuda_corriente();
                        if (isset($deuda_corriente)) {
                            //Primera iteración para contar las repeticiones
                            foreach ($deuda_corriente as &$deuda) {
                                $id_alumno_cc = $deuda['id_alumno_cc'];

                                if (!isset($repeticiones_id_alumno_cc[$id_alumno_cc])) {
                                    $repeticiones_id_alumno_cc[$id_alumno_cc] = 0;
                                }
                                $repeticiones_id_alumno_cc[$id_alumno_cc]++;
                            }

                            //Segunda iteración para actualizar los importes y agregar todos los registros
                            foreach ($deuda_corriente as &$deuda) {
                                $saldo_a_sumar = 0;
                                $id_alumno_cc = $deuda['id_alumno_cc'];
                                if ($repeticiones_id_alumno_cc[$id_alumno_cc] <= 1) {
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
                                } elseif ($repeticiones_id_alumno_cc[$id_alumno_cc] == 2) {
                                    $saldo_a_sumar = ($deuda['id_medio_pago'] && $deuda['diferencia_importe_misma_cuota'] < 0) ? $this->actualizar_cargos_individuales($deuda['importe_anterior'], $deuda['importe'], $this->s__importe_cuota_actual) : 0;
                                }
                                $alumno['saldo_actualizado'] += $saldo_a_sumar;
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
        if ($this->s__alumno_editar) {
            $cuadro->set_titulo('<div class="titulo_alumno">' . $this->s__nombre_alumno . '</div>');
        }

        //Si el parámetro actualiza_deuda_a_valor_cuota_actual NO está en S => oculto la columna donde se muestra el saldo actualizado
        if ($this->s__mostrar_importe_actualizado != 'SI') {
            $cuadro->eliminar_columnas(array('importe_actualizado'));
        }

        if (isset($this->s__datos_deuda_corriente) && $this->s__mostrar_importe_actualizado == 'SI') {
            $cuadro->set_datos($this->obtener_nuevos_importes($this->s__datos_deuda_corriente, false));
        } else {
            $cuadro->set_datos($this->s__datos_deuda_corriente);
        }
    }

    //---- Funciones ---------------------------------------------------------------------

    //Función para actualizar los cargos generados
    function actualizar_cargos_individuales($monto_adeudado, $monto_pagado, $monto_actual)
    {
        if ($monto_pagado < 0) {
            $monto_pagado *= -1;
        }
        //Calcular el porcentaje pagado
        $porcentaje_pagado = round(($monto_pagado / $monto_adeudado) * 100, 2);
        $porcentaje_actualizar = 100 - $porcentaje_pagado;
        //Actualizar los cargos generados en igual proporción
        return round(($monto_actual * $porcentaje_actualizar / 100), 2);
    }

    /*
     * Función que se encarga de obtener los saldos mensuales actualizados al valor actual de la cuota
     */
    public function obtener_nuevos_importes($datos_deuda_corriente = null, $acumula)
    {
        //Crear un array auxiliar para rastrear cuántas veces aparece cada id_alumno_cc
        $repeticiones_id_alumno_cc = [];

        $datos_filtrados = [];
        //Primera iteración para contar las repeticiones
        foreach ($datos_deuda_corriente as &$deuda) {
            $id_alumno_cc = $deuda['id_alumno_cc'];

            if (!isset($repeticiones_id_alumno_cc[$id_alumno_cc])) {
                $repeticiones_id_alumno_cc[$id_alumno_cc] = 0;
            }
            $repeticiones_id_alumno_cc[$id_alumno_cc]++;
        }

        //Segunda iteración para actualizar los importes y agregar todos los registros
        foreach ($datos_deuda_corriente as &$deuda) {
            $importe = $deuda['importe'];
            $id_alumno_cc = $deuda['id_alumno_cc'];
            $importe_actualizado = 0;
            if ($repeticiones_id_alumno_cc[$id_alumno_cc] <= 1) {
                switch ($deuda['id_cargo_cuenta_corriente']) {
                    case constantes::get_valor_constante('INSCRIPCION_ANUAL'):
                        if ($this->s__nivel_actual_cursada == constantes::get_valor_constante('NIVEL_INICIAL')) {
                            $importe_actualizado = ($this->s__importe_inscripcion_inicial - $importe) + $importe;
                        } elseif ($this->s__nivel_actual_cursada == constantes::get_valor_constante('NIVEL_PRIMARIO')) {
                            $importe_actualizado = ($this->s__importe_inscripcion_primario - $importe) + $importe;
                        }
                        break;
                    case constantes::get_valor_constante('CUOTA_MENSUAL'):
                        $importe_actualizado = ($this->s__importe_cuota_actual - $importe) + $importe;
                        break;
                    case constantes::get_valor_constante('MATERIALES'):
                        $importe_actualizado = ($this->s__importe_materiales - $importe) + $importe;
                        break;
                }
            } elseif ($repeticiones_id_alumno_cc[$id_alumno_cc] == 2) {
                $importe_actualizado = ($deuda['id_medio_pago'] && $deuda['diferencia_importe_misma_cuota'] < 0) ? $this->actualizar_cargos_individuales($deuda['importe_anterior'], $deuda['importe'], $this->s__importe_cuota_actual) : 0;
            }
            $deuda['importe_actualizado'] = $importe_actualizado;
            $datos_filtrados[] = $deuda;
        }
        return $datos_filtrados;
    }

    /*
     * Función que se encarga de obtener el total de la deuda acumulada actualizada por alumno
     * ESTE MÉTODO AÚN ESTÁ EN EL conf__cuadro()
     */
    /*public function obtener_deudas_acumuladas()
    {
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
            }
            if (isset($saldo_deuda_corriente)) {
                $alumno['saldo'] = $saldo_deuda_corriente['saldo'];
            }
        }
        unset($alumno);
    }*/
}