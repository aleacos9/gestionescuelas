<?php
class ci_inicio extends gestionescuelas_ext_ci
{

    public function conf__pant_inicial()
    {
        $this->pantalla()->eliminar_dep('form_notif_cutoas_adeudadas');

        //Si tiene el perfil de tutor y aún no se notificó de las cuotas adeudadas => le muestro la notificación de las cuotas adeudadas
        if (isset($this->s__perfil_funcional)) {
            if ((in_array('tutor', $this->s__perfil_funcional))) {
                if (!$this->se_notifico_de_cuotas_adeudadas_no_inscripcion($this->s__id_persona)) {
                    $this->pantalla()->agregar_dep('form_notif_cutoas_adeudadas');
                } else {
                    //$this->mostrar_logo();
                }
            } else {
                $this->mostrar_logo();
            }
        } else {
            $this->mostrar_logo();
        }
    }

    public static function mostrar_logo()
    {
        echo '<div class="logo">';
        echo toba_recurso::imagen_proyecto('logo_grande.gif', true);
        echo '</div>';
    }

    public static function se_notifico_de_cuotas_adeudadas_no_inscripcion($id_persona)
    {
        $persona = new persona($id_persona);
        $filtro['id_notificacion'] = constantes::get_valor_constante('NO_INSCRIPCION');;
        if ($persona->get_estado_notificacion_x_tipo_notificacion($filtro)) {
            return true;
        } else {
            return false;
        }
    }

    public function conf__form_notif_cutoas_adeudadas($form)
    {
        if (isset($this->s__id_persona)) {
            $persona['id_persona'] = $this->s__id_persona;
            $form->set_datos($persona);
        }
    }

    public function evt__form_notif_cutoas_adeudadas__guardar($datos)
    {
        $tipo_notificacion = constantes::get_valor_constante('NO_INSCRIPCION');
        $sql = "INSERT INTO persona_notificacion(id_persona,id_notificacion,notificado,fecha_notificacion) 
                VALUES ({$datos['id_persona']},{$tipo_notificacion},'s',current_date)";
        toba::db()->ejecutar($sql);

        toba::vinculador()->navegar_a('gestionescuelas', 2, null, false, false);
    }
}