<?php
class ci_listado_rechazos extends ci_abm_personas
{
    protected $s__datos_seleccion;

    //---- AJAX -------------------------------------------------------------------------

    function ajax__registrar_whatsapp($id, $ajax_respuesta)
    {
        dao_consultas::set_notificacion_whatsapp($id);
        $ajax_respuesta->set("OK");
    }

    //---- filtro -----------------------------------------------------------------------

    function conf__filtro($filtro)
    {
        if (!isset($this->s__datos_filtro)) {
            $this->s__datos_filtro = array(
                "notificado_mail" => "N",
                "notificado_whatsapp" => "N"
            );
        }
        $filtro->set_datos($this->s__datos_filtro);
    }

    function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
    }

    //---- cuadro -----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        if (isset($this->s__datos_filtro)) {
            $datos = dao_consultas::get_listado_rechazos_debitos($this->s__datos_filtro);

            foreach ($datos as $id => $fila) {
                // 1. Columna notificado_mail
                if ($fila['notificado_mail'] == 'SI') {
                    $img_mail = toba_recurso::imagen_toba('aplicar.png', true, null, null, 'Mail Enviado');
                } else {
                    $img_mail = toba_recurso::imagen_toba('error.png', true, null, null, 'Mail Pendiente');
                }
                $datos[$id]['notificado_mail'] = "<span class='estado-mail'>$img_mail</span>";

                // 2. Columna notificado_whatsapp (Estado + Accion)
                if ($fila['notificado_whatsapp'] == 'SI') {
                    $img_wa_estado = toba_recurso::imagen_toba('aplicar.png', true, null, null, 'WA Enviado');
                } else {
                    $img_wa_estado = toba_recurso::imagen_toba('error.png', true, null, null, 'WA Pendiente');
                }

                $wa_accion = "";
                if (!empty($fila['tutor_telefono'])) {
                    $tel_limpio = $this->limpiar_telefono($fila['tutor_telefono']);
                    $msj = envio_correo::generar_mensaje_whatsapp_rechazo($fila);
                    $msj_url = rawurlencode($msj);
                    $url_wa = "https://wa.me/{$tel_limpio}?text={$msj_url}";

                    $wa_accion = "<a href='#' onclick=\"{$this->objeto_js}.registrar_whatsapp('{$fila['id_transaccion_cc']}', '{$url_wa}'); return false;\" style='margin-left: 8px;' title='Enviar WhatsApp'>" .
                        "<img src='https://cdn-icons-png.flaticon.com/24/3670/3670051.png' width='16' height='16' style='vertical-align: middle;'>" .
                        "</a>";
                }

                $datos[$id]['notificado_whatsapp'] = "<div style='display: flex; align-items: center;'>" .
                    "<span class='estado-wa'>$img_wa_estado</span>" .
                    "$wa_accion</div>";
            }

            $cuadro->set_datos($datos);
        }
    }

    private function limpiar_telefono($numero)
    {
        $limpio = preg_replace('/[^0-9]/', '', $numero);
        if (strpos($limpio, '0') === 0) $limpio = substr($limpio, 1);
        if (strlen($limpio) == 10) $limpio = '549' . $limpio;
        return $limpio;
    }

    function evt__cuadro__seleccion($seleccion)
    {
        $this->s__datos_seleccion = $seleccion;
    }

    function evt__notificar()
    {
        // Como el evento 'notificar' en Toba Editor tiene 'Maneja Datos = NO', 
        // el framework omite procesar la seleccion del cuadro. Forzamos su procesamiento manual.
        $this->dep('cuadro')->disparar_eventos();

        if (empty($this->s__datos_seleccion)) {
            toba::notificacion()->agregar("Debe seleccionar al menos un registro para notificar.", "error");
            return;
        }

        $datos_completos = dao_consultas::get_listado_rechazos_debitos($this->s__datos_filtro);
        $datos_indexados = array();
        foreach ($datos_completos as $d) {
            $datos_indexados[$d['id_transaccion_cc']] = $d;
        }

        require_once(toba::proyecto()->get_path_php() . '/utiles/envio_correo.php');
        $enviados = 0;
        $errores = array();
        $lista_enviados = array();

        foreach ($this->s__datos_seleccion as $seleccion) {
            $id_cc = $seleccion['id_transaccion_cc'];
            if (!isset($datos_indexados[$id_cc])) continue;
            $fila = $datos_indexados[$id_cc];

            try {
                if (empty($fila['tutor_email'])) {
                    $errores[] = "El alumno {$fila['alumno']} no tiene email de tutor cargado.";
                    continue;
                }

                $correo = new envio_correo($fila['tutor_email']);
                $correo->set_asunto(envio_correo::generar_asunto_notificacion_rechazo());
                $correo->set_cuerpo(envio_correo::generar_cuerpo_notificacion_rechazo($fila));

                if ($correo->enviar()) {
                    dao_consultas::set_notificacion_mail($id_cc);
                    $enviados++;
                    $lista_enviados[] = "{$fila['alumno']} ({$fila['tutor_email']})";
                }
            } catch (Exception $e) {
                $errores[] = "Error con {$fila['alumno']}: " . $e->getMessage();
            }
        }

        if ($enviados > 0) {
            toba::notificacion()->agregar("Se enviaron $enviados correos correctamente.", 'info');
        }
        if (!empty($errores)) {
            toba::notificacion()->agregar("Hubo problemas con algunos envios.", 'warning');
        }
    }


    function extender_objeto_js()
    {
        $id_js = $this->objeto_js;
        $id_js_cuadro = $this->dep('cuadro')->objeto_js;
        echo "
            {$id_js}.evt__notificar = function() {
                var cuadro = {$id_js_cuadro};
                var seleccionados = cuadro.get_ids_seleccionados('seleccion');
                
                if (seleccionados.length == 0) {
                    alert('Debe seleccionar al menos un registro para notificar.');
                    return false;
                }

                // Toba no envia automaticamente el evento multiple del cuadro si el submit 
                // se dispara desde un boton del CI. Seteamos manualmente el evento del cuadro 
                // para que PHP invoque evt__cuadro__seleccion() antes de evt__notificar().
                document.getElementById(cuadro._input_submit).value = 'seleccion';
                
                return true;
            }

            {$id_js}.registrar_whatsapp = function(id, url) {
                window.open(url, '_blank');
                this.ajax('registrar_whatsapp', id, this, function(res) {
                    location.reload();
                });
            }

            {$id_js}.seleccionar_todos = function() {
                {$id_js_cuadro}.seleccionar_todos('seleccion');
            }
            {$id_js}.deseleccionar_todos = function() {
                {$id_js_cuadro}.deseleccionar_todos('seleccion');
            }

            $(function() {
                var barra = $('<div style=\"padding: 5px; text-align: right; font-size: 11px;\"><a href=\"#\" id=\"lnk_sel_todos\" style=\"color: #2196F3; text-decoration: none; font-weight: bold;\">Seleccionar Todos</a> | <a href=\"#\" id=\"lnk_sel_ninguno\" style=\"color: #f44336; text-decoration: none; font-weight: bold;\">Ninguno</a></div>');
                $('#cuerpo_js_cuadro_82000084_cuadro').before(barra);

                $('#lnk_sel_todos').click(function() { {$id_js}.seleccionar_todos(); return false; });
                $('#lnk_sel_ninguno').click(function() { {$id_js}.deseleccionar_todos(); return false; });

                var cont = $('#cuerpo_js_cuadro_82000084_cuadro');
                if (cont.length > 0) {
                    cont.find('input[id\$=\"_seleccion\"]').each(function() {
                        var check = $(this);
                        var fila = check.closest('tr');
                        var mail_notificado = fila.find('.estado-mail img[src*=\"aplicar.png\"]').length > 0;
                        if (mail_notificado) {
                            check.remove();
                            fila.children('td').css('opacity', '0.5');
                        }
                    });
                }
            });
        ";
    }
}
