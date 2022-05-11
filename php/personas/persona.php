<?php


class persona
{
    protected $persona;
    protected $id_persona;
    protected $apellidos;
    protected $nombres;
    protected $nombre_completo_persona;
    protected $nombre_completo_alumno;
    protected $sexo;
    protected $fecha_nacimiento;
    protected $correo_electronico;
    protected $recibe_notif_x_correo;
    protected $telefono;
    protected $id_localidad_nacimiento;
    protected $id_localidad_residencia;
    protected $id_nacionalidad;
    protected $activo;
    protected $es_alumno;
    protected $fecha_alta;
    protected $usuario_alta;
    protected $fecha_ultima_modificacion;
    protected $usuario_ultima_modificacion;
    protected $usuario;
    protected $estado;

    protected $sexo_activo;

    protected $id_alumno;
    protected $legajo;
    protected $extranjero;
    protected $regular;
    protected $id_motivo_desercion;
    protected $es_celiaco;
    protected $direccion_calle;
    protected $direccion_numero;
    protected $direccion_piso;
    protected $direccion_depto;

    protected $id_grado;
    protected $division;
    protected $anio_cursada;
    protected $genero_costo_inscripcion;
    protected $pago_inscripcion;

    protected $id_medio_pago;
    protected $id_marca_tarjeta;
    protected $id_entidad_bancaria;
    protected $numero_tarjeta;
    protected $nombre_titular;
    protected $activo_tarjeta;

    protected $cargo_a_generar;
    protected $descripcion_cuota;
    protected $cuota;
    protected $anio_cuota;
    protected $cuota_completa;
    protected $importe_cuota;

    protected $id_alumno_cc;
    protected $fecha_pago;
    protected $fecha_transaccion;
    protected $fecha_respuesta_prisma;
    protected $id_estado_cuota;
    protected $estado_movimiento;
    protected $id_motivo_rechazo1;
    protected $descripcion_rechazo1;
    protected $id_motivo_rechazo2;
    protected $descripcion_rechazo2;
    protected $numero_comprobante;
    protected $numero_lote;
    protected $numero_autorizacion;
    protected $importe_pago;
    protected $mostrar_mensaje_individual;
    protected $modo;

    protected $datos = array();
    protected $datos_alumno = array();
    protected $datos_alumnos_vinculados = array();
    protected $persona_documentos = array();
    protected $persona_sexos = array();
    protected $persona_allegados = array();
    protected $datos_academicos = array();
    protected $datos_formas_cobro = array();
    protected $datos_generacion_cargos = array();
    protected $datos_cuenta_corriente = array();

    public function __construct($persona = null)
    {
        toba::logger()->info("Constructor de la persona. Persona = ".$persona);

        if ($persona <> null) {
            $this->persona = $persona;
            $this->cargar_persona();
        }
    }

    //---------------------------------------------------------------------
    //                     GETTERS y SETTERS
    //---------------------------------------------------------------------

    public function set_datos_persona($datos = null)
    {
        toba::logger()->info("set_datos_persona");

        $this->set_apellidos($datos['apellidos']);
        $this->set_nombres($datos['nombres']);
        $this->set_fecha_nacimiento($datos['fecha_nacimiento']);
        $this->set_correo_electronico($datos['correo_electronico']);
        $this->set_recibe_notif_x_correo($datos['recibe_notif_x_correo']);
        $this->set_telefono($datos['telefono']);
        $this->set_id_localidad_nacimiento($datos['id_localidad_nacimiento']);
        $this->set_id_localidad_residencia($datos['id_localidad_residencia']);
        $this->set_id_nacionalidad($datos['id_nacionalidad']);
        $this->set_activo($datos['activo']);
        $this->set_es_alumno($datos['es_alumno']);
        $this->set_usuario($datos['usuario']);
        $this->set_fecha_alta($datos['fecha_alta']);
        $this->set_usuario_alta($datos['usuario_alta']);
        $this->set_fecha_ultima_modificacion($datos['fecha_ultima_modificacion']);
        $this->set_usuario_ultima_modificacion($datos['usuario_ultima_modificacion']);

        $this->set_sexo($datos['id_sexo']);

        $this->datos = $datos;
    }

    public function set_datos_alumno($datos_alumno = null)
    {
        toba::logger()->info("set_datos_alumno");

        $this->set_legajo($datos_alumno['legajo']);
        $this->set_extranjero($datos_alumno['extranjero']);
        $this->set_regular($datos_alumno['regular']);
        $this->set_id_motivo_desercion($datos_alumno['id_motivo_desercion']);
        $this->set_es_celiaco($datos_alumno['es_celiaco']);
        $this->set_direccion_calle($datos_alumno['direccion_calle']);
        $this->set_direccion_numero($datos_alumno['direccion_numero']);
        $this->set_direccion_piso($datos_alumno['direccion_piso']);
        $this->set_direccion_depto($datos_alumno['direccion_depto']);

        $this->datos_alumno = $datos_alumno;
    }

    public function set_datos_formas_cobro($datos_formas_cobro = null)
    {
        toba::logger()->info("set_datos_formas_cobro");

        $this->set_id_medio_pago($datos_formas_cobro['id_medio_pago']);
        $this->set_id_marca_tarjeta($datos_formas_cobro['id_marca_tarjeta']);
        $this->set_id_entidad_bancaria($datos_formas_cobro['id_entidad_bancaria']);
        $this->set_numero_tarjeta($datos_formas_cobro['numero_tarjeta']);
        $this->set_nombre_titular($datos_formas_cobro['nombre_titular']);
        $this->set_activo($datos_formas_cobro['activo']);

        $this->datos_formas_cobro = $datos_formas_cobro;
    }

    public function set_datos_academicos($datos_academicos = null)
    {
        toba::logger()->info("set_datos_academicos");

        $this->set_id_grado($datos_academicos['id_grado']);
        $this->set_division($datos_academicos['division']);
        $this->set_anio_cursada($datos_academicos['anio_cursada']);
        $this->set_genero_costo_inscripcion($datos_academicos['genero_costo_inscripcion']);
        $this->set_pago_inscripcion($datos_academicos['pago_inscripcion']);

        $this->datos_academicos = $datos_academicos;
    }

    public function set_datos_generacion_cargos($datos_generacion_cargos)
    {
        toba::logger()->info("set_datos_generacion_cargos");

        $this->set_cargo_a_generar($datos_generacion_cargos['cargo_a_generar']);
        $this->set_cuota($datos_generacion_cargos['cuota']);
        $this->set_anio_cuota($datos_generacion_cargos['anio']);

        if ($datos_generacion_cargos['cargo_a_generar'] == 1) { //inscripcion anual
            $descripcion = 'Inscrpción anual del Año ' .$datos_generacion_cargos['anio'];
            $this->set_descripcion_cuota($descripcion);
        } elseif($datos_generacion_cargos['cargo_a_generar'] == 2) { //cuota mensual
            $descripcion = 'Cuota mensual '.$datos_generacion_cargos['cuota'].' del Año ' .$datos_generacion_cargos['anio'];
            $this->set_descripcion_cuota($descripcion);
            if ($datos_generacion_cargos['cuota'] < 10) {
                $datos_generacion_cargos['cuota'] = '0'.$datos_generacion_cargos['cuota'];
            }
            $cuota = $datos_generacion_cargos['cuota'].$datos_generacion_cargos['anio'];
            $this->set_cuota_completa($cuota);
        }
        $this->set_importe_cuota($datos_generacion_cargos['importe_cuota']);

        $this->datos_generacion_cargos = $datos_generacion_cargos;
    }

    public function set_datos_cuenta_corriente($datos_cuenta_corriente)
    {
        toba::logger()->info("set_datos_cuenta_corriente");

        $this->set_id_alumno_cc($datos_cuenta_corriente['id_alumno_cc']);
        $this->set_id_medio_pago($datos_cuenta_corriente['id_medio_pago']);
        $id_marca_tarjeta = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['id_marca_tarjeta'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_id_marca_tarjeta($id_marca_tarjeta);

        if (!empty($datos_cuenta_corriente['id_estado_cuota'])) {
            $this->set_id_estado_cuota($datos_cuenta_corriente['id_estado_cuota']);
        } else {
            $this->set_id_estado_cuota(3);
        }

        $fecha_pago = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['fecha_pago'], 'STR');
        $this->set_fecha_pago($fecha_pago);
        $fecha_respuesta_prisma = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['fecha_devolucion_respuesta'], 'STR');
        if ($fecha_respuesta_prisma != 'null') {
            $fecha_respuesta_prisma = date($fecha_respuesta_prisma);
        }
        $this->set_fecha_respuesta_prisma($fecha_respuesta_prisma);

        //Datos del rechazo
        $estado_movimiento = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['estado_movimiento'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_estado_movimiento($estado_movimiento);
        //Si el movimiento arroja error => seteo el importe en 0
        if ($estado_movimiento == 1) {
            $datos_cuenta_corriente['importe'] = 0;
        }
        $this->set_importe_pago($datos_cuenta_corriente['importe']);
        //aca voy a tener que obtener el id_motivo_rechazo de la tabla motivo_rechazo asociado al codigo_rechazo que viene en el txt
        $id_motivo_rechazo1 = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['rechazo1'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_id_motivo_rechazo1($id_motivo_rechazo1);
        $descripcion_rechazo1 = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['$descripcion_rechazo1'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_descripcion_rechazo1($descripcion_rechazo1);
        $id_motivo_rechazo2 = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['rechazo2'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_id_motivo_rechazo2($id_motivo_rechazo2);
        $descripcion_rechazo2 = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['$descripcion_rechazo2'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_descripcion_rechazo2($descripcion_rechazo2);
        //Fin datos del rechazo

        $numero_comprobante = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['numero_comprobante'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_numero_comprobante($numero_comprobante);
        $numero_lote = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['numero_lote'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_numero_lote($numero_lote);
        $numero_autorizacion = conversion_tipo_datos::convertir_null_a_cadena($datos_cuenta_corriente['numero_autorizacion'], constantes::get_valor_constante('TIPO_DATO_INT'));
        $this->set_numero_autorizacion($numero_autorizacion);
        $this->set_usuario_ultima_modificacion($datos_cuenta_corriente['usuario_ultima_modificacion']);
        $this->set_fecha_ultima_modificacion($datos_cuenta_corriente['fecha_ultima_modificacion']);
        $this->set_mostrar_mensaje_individual($datos_cuenta_corriente['mostrar_mensaje_individual']);
        $this->set_cuota_completa($datos_cuenta_corriente['cuota']);

        $this->datos_cuenta_corriente = $datos_cuenta_corriente;
    }

    public function set_persona($persona)
    {
        toba::logger()->info("set_persona. Persona = ".$persona);
        $this->persona = $persona;
    }

    public function set_apellidos($apellidos)
    {
        toba::logger()->info("set_apellidos = " .$apellidos);
        $this->apellidos = $apellidos;
    }

    public function set_nombres($nombres)
    {
        toba::logger()->info("set_nombres = " . $nombres);
        $this->nombres = $nombres;
    }

    public function set_sexo($sexo)
    {
        toba::logger()->info("set_sexo = " .$sexo);
        $this->sexo = $sexo;
    }

    public function set_fecha_nacimiento($fecha_nacimiento)
    {
        toba::logger()->info("set_fecha_nacimiento = " .$fecha_nacimiento);
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    public function set_correo_electronico($correo_electronico)
    {
        toba::logger()->info("set_correo_electronico = " .$correo_electronico);
        $this->correo_electronico = $correo_electronico;
    }

    public function set_recibe_notif_x_correo($recibe_notif_x_correo)
    {
        toba::logger()->info("set_recibe_notif_x_correo = " .$recibe_notif_x_correo);
        $this->recibe_notif_x_correo = $recibe_notif_x_correo;
    }

    public function set_telefono($telefono)
    {
        toba::logger()->info("set_telefono = " .$telefono);
        $this->telefono = $telefono;
    }

    public function set_id_localidad_nacimiento($id_localidad_nacimiento)
    {
        toba::logger()->info("set_id_localidad_nacimiento = " .$id_localidad_nacimiento);
        $this->id_localidad_nacimiento = $id_localidad_nacimiento;
    }

    public function set_id_localidad_residencia($id_localidad_residencia)
    {
        toba::logger()->info("set_id_localidad_residencia = " .$id_localidad_residencia);
        $this->id_localidad_residencia = $id_localidad_residencia;
    }

    public function set_id_nacionalidad($id_nacionalidad)
    {
        toba::logger()->info("set_id_nacionalidad = " .$id_nacionalidad);
        $this->id_nacionalidad = $id_nacionalidad;
    }

    public function set_activo($activo)
    {
        toba::logger()->info("set_activo = " .$activo);
        $this->activo = $activo;
    }

    public function set_es_alumno($es_alumno)
    {
        toba::logger()->info("set_es_alumno = " .$es_alumno);
        $this->es_alumno = $es_alumno;
    }

    public function set_usuario($usuario)
    {
        toba::logger()->info("set_usuario = " .$usuario);
        $this->usuario = $usuario;
    }

    public function set_fecha_alta($fecha_alta)
    {
        toba::logger()->info("set_fecha_alta = " .$fecha_alta);
        $this->fecha_alta = $fecha_alta;
    }

    public function set_usuario_alta($usuario_alta)
    {
        toba::logger()->info("set_usuario_alta = " .$usuario_alta);
        $this->usuario_alta = $usuario_alta;
    }

    public function set_fecha_ultima_modificacion($fecha_ultima_modificacion)
    {
        toba::logger()->info("set_fecha_ultima_modificacion = " .$fecha_ultima_modificacion);
        $this->fecha_ultima_modificacion = $fecha_ultima_modificacion;
    }

    public function set_mostrar_mensaje_individual($mostrar_mensaje_individual)
    {
        toba::logger()->info("set_mostrar_mensaje_individual = " .$mostrar_mensaje_individual);
        $this->mostrar_mensaje_individual = $mostrar_mensaje_individual;
    }

    public function set_usuario_ultima_modificacion($usuario_ultima_modificacion)
    {
        toba::logger()->info("set_usuario_ultima_modificacion = " .$usuario_ultima_modificacion);
        $this->usuario_ultima_modificacion = $usuario_ultima_modificacion;
    }

    public function set_estado($estado)
    {
        toba::logger()->info("set_estado = " .$estado);
        $this->estado = $estado;
    }

    public function set_legajo($legajo)
    {
        toba::logger()->info("set_legajo = " .$legajo);
        $this->legajo = $legajo;
    }

    public function set_extranjero($extranjero)
    {
        toba::logger()->info("set_extranjero = " .$extranjero);
        $this->extranjero = $extranjero;
    }

    public function set_regular($regular)
    {
        toba::logger()->info("set_regular = " .$regular);
        $this->regular = $regular;
    }

    public function set_id_motivo_desercion($id_motivo_desercion)
    {
        toba::logger()->info("set_id_motivo_desercion = " .$id_motivo_desercion);
        $this->id_motivo_desercion = $id_motivo_desercion;
    }

    public function set_es_celiaco($es_celiaco)
    {
        toba::logger()->info("set_es_celiaco = " .$es_celiaco);
        $this->es_celiaco = $es_celiaco;
    }

    public function set_direccion_calle($direccion_calle)
    {
        toba::logger()->info("set_direccion_calle = " .$direccion_calle);
        $this->direccion_calle = $direccion_calle;
    }

    public function set_direccion_numero($direccion_numero)
    {
        toba::logger()->info("set_direccion_numero = " .$direccion_numero);
        $this->direccion_numero = $direccion_numero;
    }

    public function set_direccion_piso($direccion_piso)
    {
        toba::logger()->info("set_direccion_piso = " .$direccion_piso);
        $this->direccion_piso = $direccion_piso;
    }

    public function set_direccion_depto($direccion_depto)
    {
        toba::logger()->info("set_direccion_depto = " .$direccion_depto);
        $this->direccion_depto = $direccion_depto;
    }

    public function set_id_grado($id_grado)
    {
        toba::logger()->info("set_id_grado = " .$id_grado);
        $this->id_grado = $id_grado;
    }

    public function set_division($division)
    {
        toba::logger()->info("set_division = " .$division);
        $this->division = $division;
    }

    public function set_anio_cursada($anio_cursada)
    {
        toba::logger()->info("set_anio_cursada = " .$anio_cursada);
        $this->anio_cursada = $anio_cursada;
    }

    public function set_genero_costo_inscripcion($genero_costo_inscripcion)
    {
        toba::logger()->info("set_genero_costo_inscripcion = " .$genero_costo_inscripcion);
        $this->genero_costo_inscripcion = $genero_costo_inscripcion;
    }

    public function set_pago_inscripcion($pago_inscripcion)
    {
        toba::logger()->info("set_pago_inscripcion = " .$pago_inscripcion);
        $this->pago_inscripcion = $pago_inscripcion;
    }

    public function set_id_alumno_cc($id_alumno_cc)
    {
        toba::logger()->info("set_id_alumno_cc = " .$id_alumno_cc);
        $this->id_alumno_cc = $id_alumno_cc;
    }

    public function set_id_medio_pago($id_medio_pago)
    {
        toba::logger()->info("set_id_medio_pago = " .$id_medio_pago);
        $this->id_medio_pago = $id_medio_pago;
    }

    public function set_id_marca_tarjeta($id_marca_tarjeta)
    {
        toba::logger()->info("set_id_marca_tarjeta = " .$id_marca_tarjeta);
        $this->id_marca_tarjeta = $id_marca_tarjeta;
    }

    public function set_id_estado_cuota($id_estado_cuota)
    {
        toba::logger()->info("set_id_estado_cuota = " .$id_estado_cuota);
        $this->id_estado_cuota = $id_estado_cuota;
    }

    public function set_fecha_respuesta_prisma($fecha_respuesta_prisma)
    {
        toba::logger()->info("set_fecha_respuesta_prisma = " .$fecha_respuesta_prisma);
        $this->fecha_respuesta_prisma = $fecha_respuesta_prisma;
    }

    public function set_fecha_transaccion($fecha_transaccion)
    {
        toba::logger()->info("set_fecha_transaccion = " .$fecha_transaccion);
        $this->fecha_transaccion = $fecha_transaccion;
    }

    public function set_id_motivo_rechazo1($id_motivo_rechazo1)
    {
        toba::logger()->info("set_id_motivo_rechazo1 = " .$id_motivo_rechazo1);
        $this->id_motivo_rechazo1 = $id_motivo_rechazo1;
    }

    public function set_descripcion_rechazo1($descripcion_rechazo1)
    {
        toba::logger()->info("set_descripcion_rechazo1 = " .$descripcion_rechazo1);
        $this->descripcion_rechazo1 = $descripcion_rechazo1;
    }

    public function set_id_motivo_rechazo2($id_motivo_rechazo2)
    {
        toba::logger()->info("set_id_motivo_rechazo2 = " .$id_motivo_rechazo2);
        $this->id_motivo_rechazo2 = $id_motivo_rechazo2;
    }

    public function set_descripcion_rechazo2($descripcion_rechazo2)
    {
        toba::logger()->info("set_descripcion_rechazo2 = " .$descripcion_rechazo2);
        $this->descripcion_rechazo2 = $descripcion_rechazo2;
    }

    public function set_id_entidad_bancaria($id_entidad_bancaria)
    {
        toba::logger()->info("set_id_entidad_bancaria = " .$id_entidad_bancaria);
        $this->id_entidad_bancaria = $id_entidad_bancaria;
    }

    public function set_numero_tarjeta($numero_tarjeta)
    {
        toba::logger()->info("set_numero_tarjeta = " .$numero_tarjeta);
        $this->numero_tarjeta = $numero_tarjeta;
    }

    public function set_nombre_titular($nombre_titular)
    {
        toba::logger()->info("set_nombre_titular = " .$nombre_titular);
        $this->nombre_titular = $nombre_titular;
    }

    public function set_activo_tarjeta($activo_tarjeta)
    {
        toba::logger()->info("set_activo_tarjeta = " .$activo_tarjeta);
        $this->activo_tarjeta = $activo_tarjeta;
    }

    public function set_fecha_pago($fecha_pago)
    {
        toba::logger()->info("set_fecha_pago = " .$fecha_pago);
        $this->fecha_pago = $fecha_pago;
    }

    public function set_numero_comprobante($numero_comprobante)
    {
        toba::logger()->info("set_numero_comprobante = " .$numero_comprobante);
        $this->numero_comprobante = $numero_comprobante;
    }

    public function set_numero_autorizacion($numero_autorizacion)
    {
        toba::logger()->info("set_numero_autorizacion = " .$numero_autorizacion);
        $this->numero_autorizacion = $numero_autorizacion;
    }

    public function set_numero_lote($numero_lote)
    {
        toba::logger()->info("set_numero_lote = " .$numero_lote);
        $this->numero_lote = $numero_lote;
    }

    public function set_importe_pago($importe_pago)
    {
        toba::logger()->info("set_importe_pago = " .$importe_pago);
        $this->importe_pago = $importe_pago;
    }

    public function set_persona_documentos($persona_documentos)
    {
        toba::logger()->info("set_persona_documentos");
        $this->persona_documentos = $persona_documentos;
    }

    public function set_allegados($persona_allegados)
    {
        toba::logger()->info("set_allegados");
        $this->persona_allegados = $persona_allegados;
    }

    public function set_cargo_a_generar($cargo_a_generar)
    {
        toba::logger()->info("set_cargo_a_generar = " .$cargo_a_generar);
        $this->cargo_a_generar = $cargo_a_generar;
    }

    public function set_descripcion_cuota($descripcion_cuota)
    {
        toba::logger()->info("set_descripcion_cuota = " .$descripcion_cuota);
        $this->descripcion_cuota = $descripcion_cuota;
    }

    public function set_cuota($cuota)
    {
        toba::logger()->info("set_cuota = " .$cuota);
        $this->cuota = $cuota;
    }

    public function set_anio_cuota($anio)
    {
        toba::logger()->info("set_anio_cuota = " .$anio);
        $this->anio_cuota = $anio;
    }

    public function set_cuota_completa($cuota_completa)
    {
        toba::logger()->info("set_cuota_completa = " .$cuota_completa);
        $this->cuota_completa = $cuota_completa;
    }

    public function set_importe_cuota($importe_cuota)
    {
        toba::logger()->info("set_importe_cuota = " .$importe_cuota);
        $this->importe_cuota = $importe_cuota;
    }

    public function set_modo($modo)
    {
        toba::logger()->info("set_modo = " .$modo);
        $this->modo = $modo;
    }

    public function set_estado_movimiento($estado_movimiento)
    {
        toba::logger()->info("set_estado_movimiento = " .$estado_movimiento);
        $this->estado_movimiento = $estado_movimiento;
    }


    public function get_id_persona()
    {
        toba::logger()->info("get_id_persona");
        return $this->id_persona;
    }

    public function get_apellidos()
    {
        toba::logger()->info("get_apellidos");
        return $this->apellidos;
    }

    public function get_nombres()
    {
        toba::logger()->info("get_nombres");
        return $this->nombres;
    }

    public function get_nombre_completo_persona()
    {
        toba::logger()->info("get_nombre_completo_persona");
        return $this->nombre_completo_persona;
    }

    public function get_nombre_completo_alumno()
    {
        toba::logger()->info("get_nombre_completo_alumno");
        return $this->nombre_completo_alumno;
    }

    public function get_fecha_nacimiento()
    {
        toba::logger()->info("get_fecha_nacimiento");
        return $this->fecha_nacimiento;
    }

    public function get_correo_electronico()
    {
        toba::logger()->info("get_correo_electronico");
        return $this->correo_electronico;
    }

    public function get_recibe_notif_x_correo()
    {
        toba::logger()->info("get_recibe_notif_x_correo");
        return $this->recibe_notif_x_correo;
    }

    public function get_telefono()
    {
        toba::logger()->info("get_telefono");
        return $this->telefono;
    }

    public function get_id_localidad_nacimiento()
    {
        toba::logger()->info("get_id_localidad_nacimiento");
        return $this->id_localidad_nacimiento;
    }

    public function get_id_localidad_residencia()
    {
        toba::logger()->info("get_id_localidad_residencia");
        return $this->id_localidad_nacimiento;
    }

    public function get_id_nacionalidad()
    {
        toba::logger()->info("get_id_nacionalidad");
        return $this->id_nacionalidad;
    }

    public function get_estado_persona()
    {
        toba::logger()->info("get_estado_persona");
        return $this->activo;
    }

    public function get_es_alumno()
    {
        toba::logger()->info("get_es_alumno");
        return $this->es_alumno;
    }

    public function get_usuario()
    {
        toba::logger()->info("get_usuario");
        return $this->usuario;
    }

    public function get_sexo_activo()
    {
        toba::logger()->info("get_sexo_activo");
        if (!empty($this->persona_sexos)) {
            foreach ($this->persona_sexos as $sexo) {
                if ($sexo['activo'] == 'S') {
                    $this->sexo_activo = $sexo['id_sexo'];
                }
            }
        }
        return $this->sexo_activo;
    }

    public function get_id_alumno()
    {
        toba::logger()->info("get_id_alumno");
        return $this->id_alumno;
    }

    public function get_legajo()
    {
        toba::logger()->info("get_legajo");
        return $this->legajo;
    }

    public function get_extranjero()
    {
        toba::logger()->info("get_extranjero");
        return $this->extranjero;
    }

    public function get_regular()
    {
        toba::logger()->info("get_regular");
        return $this->regular;
    }

    public function get_id_motivo_desercion()
    {
        toba::logger()->info("get_id_motivo_desercion");
        return $this->id_motivo_desercion;
    }

    public function get_es_celiaco()
    {
        toba::logger()->info("get_es_celiaco");
        return $this->es_celiaco;
    }

    public function get_direccion_calle()
    {
        toba::logger()->info("get_direccion_calle");
        return $this->direccion_calle;
    }

    public function get_direccion_numero()
    {
        toba::logger()->info("get_direccion_numero");
        return $this->direccion_numero;
    }

    public function get_direccion_piso()
    {
        toba::logger()->info("get_direccion_piso");
        return $this->direccion_piso;
    }

    public function get_direccion_depto()
    {
        toba::logger()->info("get_direccion_depto");
        return $this->direccion_depto;
    }

    public function get_documentos()
    {
        toba::logger()->info("get_persona_documentos");
        return $this->persona_documentos;
    }

    public function get_persona_allegados()
    {
        toba::logger()->info("get_persona_allegados");
        return $this->persona_allegados;
    }

    public function get_alumnos_vinculados()
    {
        toba::logger()->info("get_alumnos_vinculados");
        return $this->datos_alumnos_vinculados;
    }

    public function get_datos_academicos()
    {
        toba::logger()->info("get_datos_academicos");
        return $this->datos_academicos;
    }

    public function get_datos_formas_cobro()
    {
        toba::logger()->info("get_datos_formas_cobro");
        return $this->datos_formas_cobro;
    }

    public function get_datos_cuenta_corriente()
    {
        toba::logger()->info("get_datos_cuenta_corriente");
        return $this->datos_cuenta_corriente;
    }

    public function get_fecha_transaccion()
    {
        toba::logger()->info("get_fecha_transaccion");
        return $this->fecha_transaccion;
    }

    public function get_modo()
    {
        toba::logger()->info("get_modo");
        return $this->modo;
    }

    //---------------------------------------------------------------------
    //                     MÉTODOS
    //---------------------------------------------------------------------

    protected function cargar_persona()
    {
        toba::logger()->info("Carga una Persona para modificar ");
        // Obtiene los datos básicos de la persona ---------------------------------
        $sql = "SELECT p.id_persona
                      ,p.apellidos
                      ,p.nombres
                      ,p.fecha_nacimiento
                      ,p.correo_electronico
                      ,p.recibe_notif_x_correo
                      ,p.telefono
                      ,p.id_localidad_nacimiento
                      ,p.id_localidad_residencia
                      ,p.id_nacionalidad
                      ,p.activo
                      ,p.es_alumno
                      ,p.usuario
                      ,p.fecha_alta
                      ,p.usuario_alta  
                      ,p.fecha_ultima_modificacion
                      ,p.usuario_ultima_modificacion
				FROM persona p
				WHERE p.id_persona = {$this->persona}
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $resultado = consultar_fuente($sql);

        $this->id_persona = $resultado[0]['id_persona'];
        $this->apellidos = $resultado[0]['apellidos'];
        $this->nombres = $resultado[0]['nombres'];
        $this->nombre_completo_persona = $resultado[0]['apellidos']. ', '. $resultado[0]['nombres'];
        $this->fecha_nacimiento = $resultado[0]['fecha_nacimiento'];
        $this->correo_electronico = $resultado[0]['correo_electronico'];
        $this->recibe_notif_x_correo = $resultado[0]['recibe_notif_x_correo'];
        $this->telefono = $resultado[0]['telefono'];
        $this->id_localidad_nacimiento = $resultado[0]['id_localidad_nacimiento'];
        $this->id_localidad_residencia = $resultado[0]['id_localidad_residencia'];
        $this->id_nacionalidad = $resultado[0]['id_nacionalidad'];
        $this->activo = $resultado[0]['activo'];
        $this->es_alumno = $resultado[0]['es_alumno'];
        $this->usuario = $resultado[0]['usuario'];
        $this->fecha_alta = $resultado[0]['fecha_alta'];
        $this->usuario_alta = $resultado[0]['usuario_alta'];
        $this->fecha_ultima_modificacion = $resultado[0]['fecha_ultima_modificacion'];
        $this->usuario_ultima_modificacion = $resultado[0]['usuario_ultima_modificacion'];

        $this->datos = $resultado[0];

        // Obtiene los datos del alumno, solo si está marcado como alumno
        if ($this->es_alumno == 'S') {
            $sql = "SELECT a.id_alumno
                          ,a.id_persona
                          ,p.nombres
                          ,p.apellidos
                          ,a.legajo
                          ,a.extranjero
                          ,a.regular
                          ,a.id_motivo_desercion
                          ,md.nombre as motivo_desercion  
                          ,a.es_celiaco
                          ,a.direccion_calle
                          ,a.direccion_numero
                          ,a.direccion_piso
                          ,a.direccion_depto
                    FROM alumno a
                        LEFT OUTER JOIN motivo_desercion md on a.id_motivo_desercion = md.id_motivo_desercion
                        INNER JOIN persona p on p.id_persona = a.id_persona
                    WHERE p.id_persona = {$this->persona}
                   ";

            toba::logger()->debug(__METHOD__." : ".$sql);
            $resultado_alumno = consultar_fuente($sql);

            $this->id_alumno = $resultado_alumno[0]['id_alumno'];
            $this->legajo = $resultado_alumno[0]['legajo'];
            $this->extranjero = $resultado_alumno[0]['extranjero'];
            $this->regular = $resultado_alumno[0]['regular'];
            $this->id_motivo_desercion = $resultado_alumno[0]['id_motivo_desercion'];
            $this->es_celiaco = $resultado_alumno[0]['es_celiaco'];
            $this->direccion_calle = $resultado_alumno[0]['direccion_calle'];
            $this->direccion_numero = $resultado_alumno[0]['direccion_numero'];
            $this->direccion_piso = $resultado_alumno[0]['direccion_piso'];
            $this->direccion_depto = $resultado_alumno[0]['direccion_depto'];
            if (dao_consultas::catalogo_de_parametros("muestra_nombre_con_legajo") == 'NO') {
                $this->nombre_completo_alumno = $resultado_alumno[0]['apellidos']. ', '. $resultado_alumno[0]['nombres'];
            } else {
                $this->nombre_completo_alumno = $resultado_alumno[0]['apellidos']. ', '. $resultado_alumno[0]['nombres'] .' - Legajo: '. $resultado_alumno[0]['legajo'] ;
            }
        } else {
            //Obtengo los datos de los allegados asociados a esa persona que NO es alumno
            $sql = "SELECT pa.id_persona_allegado
                          ,p.id_persona
                          ,(p.apellidos || ', ' || p.nombres) as nombre_persona
                          ,pa.id_alumno
                          ,subconsulta_nombre_alumno.nombre_alumno as nombre_alumno
                          ,pa.id_tipo_allegado
                          ,ta.nombre as allegado
                          ,pa.tutor
                          ,(CASE WHEN pa.tutor = 'S' THEN 'Si'
                                 WHEN pa.tutor = 'N' THEN 'No'
                           END) as tutor_completo
                          ,pa.activo
                          ,(CASE WHEN pa.activo = 'S' THEN 'Activo'
                                 WHEN pa.activo = 'N' THEN 'Inactivo'
                           END) as activo_completo
                    FROM persona_allegado pa
                             INNER JOIN alumno a on pa.id_alumno = a.id_alumno
                             INNER JOIN persona p on p.id_persona = pa.id_persona
                             INNER JOIN tipo_allegado ta on pa.id_tipo_allegado = ta.id_tipo_allegado
                             LEFT OUTER JOIN (SELECT p.id_persona, id_alumno
                                                   ,(p.apellidos || ', ' || p.nombres) as nombre_alumno
                                              FROM persona p
                                                       INNER JOIN alumno a on p.id_persona = a.id_persona) AS subconsulta_nombre_alumno ON subconsulta_nombre_alumno.id_alumno = a.id_alumno
                    WHERE p.id_persona = {$this->persona}
                        AND pa.activo = 'S'
                   ";

            toba::logger()->debug(__METHOD__." : ".$sql);
            $this->datos_alumnos_vinculados = consultar_fuente($sql);
        }

        // Obtiene los documentos asociados a la persona -----------------------------
        $sql = "SELECT ptd.id_persona_tipo_documento
                      ,ptd.id_persona
                      ,ptd.id_tipo_documento
                      ,td.nombre as documento_largo
                      ,td.nombre_corto as documento_corto
                      ,ptd.activo
                      ,(CASE WHEN ptd.activo = 'S' THEN 'Activo'
                             WHEN ptd.activo = 'N' THEN 'Inactivo'
                        END) as activo_completo
                      ,ptd.fecha_alta
                      ,ptd.usuario_alta
                      ,ptd.numero as identificacion
                      ,td.jerarquia
                      ,td.extranjero
                FROM persona_tipo_documento ptd
                     INNER JOIN tipo_documento td on td.id_tipo_documento = ptd.id_tipo_documento
                WHERE ptd.id_persona = {$this->persona}
                ORDER BY td.jerarquia, td.nombre, ptd.numero;
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->persona_documentos = consultar_fuente($sql);

        // Obtiene los sexos asociados a la persona ---------------------------------
        $sql = "SELECT ps.id_persona_sexo
                      ,ps.id_persona
                      ,s.id_sexo
                      ,ps.fecha_alta
                      ,ps.usuario_alta
                      ,s.nombre sexo_largo
                      ,s.nombre_corto as sexo_corto
                      ,ps.activo
                      ,(CASE WHEN ps.activo = 'S' THEN 'Activo'
                             WHEN ps.activo = 'N' THEN 'Inactivo'
                       END) as activo_completo
                FROM persona_sexo ps
                    INNER JOIN sexo s on ps.id_sexo = s.id_sexo
                WHERE ps.id_persona = {$this->persona}
                ORDER BY s.id_sexo
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->persona_sexos = consultar_fuente($sql);

        // Obtiene los alleagados asociados a la persona ---------------------------------
        $sql = "SELECT pa.id_persona_allegado
                      ,pa.id_persona
                      ,pa.id_alumno
                      ,pa.id_tipo_allegado
                      ,ta.nombre as allegado
                      ,pa.id_estudio_alcanzado
                      ,ea.nombre as estudio_alcanzado
                      ,pa.id_ocupacion
                      ,o.nombre as ocupacion
                      ,pa.tutor
                      ,pa.activo
                      ,(CASE WHEN pa.activo = 'S' THEN 'Activo'
                             WHEN pa.activo = 'N' THEN 'Inactivo'
                        END) as activo_completo
                      ,pa.fecha_alta
                      ,pa.usuario_alta
                      ,pa.fecha_ultima_modificacion
                      ,pa.usuario_ultima_modificacion
                FROM persona_allegado pa
                    INNER JOIN alumno a on pa.id_alumno = a.id_alumno
                    INNER JOIN persona p on p.id_persona = a.id_persona
                    INNER JOIN tipo_allegado ta on pa.id_tipo_allegado = ta.id_tipo_allegado
                    INNER JOIN estudio_alcanzado ea on pa.id_estudio_alcanzado = ea.id_estudio_alcanzado
                    INNER JOIN ocupacion o on pa.id_ocupacion = o.id_ocupacion
                WHERE p.id_persona = {$this->persona}
                ORDER BY ta.jerarquia, pa.id_tipo_allegado
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->persona_allegados = consultar_fuente($sql);

        // Obtiene los datos academicos asociados a la persona ---------------------------------
        $sql = "SELECT dc.id_alumno_dato_cursada
                      ,dc.id_alumno
                      ,dc.id_grado
                      ,g.nombre as grado
                      ,dc.division
                      ,dc.anio_cursada
                      ,dc.genero_costo_inscripcion
                      ,dc.pago_inscripcion
                FROM alumno_datos_cursada dc
                    INNER JOIN grado g on g.id_grado = dc.id_grado
                    INNER JOIN alumno a on a.id_alumno = dc.id_alumno
                    INNER JOIN persona p on p.id_persona = a.id_persona
                WHERE p.id_persona = {$this->persona} --dc.id_alumno = {$this->persona}
                ORDER BY dc.anio_cursada DESC
                        ,dc.id_grado
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->datos_academicos = consultar_fuente($sql);

        // Obtiene los datos de las formas de cobro asociados a la persona ---------------------------------
        $sql = "SELECT at.id_alumno_tarjeta
                      ,at.id_alumno
                      ,at.numero_tarjeta
                      ,at.nombre_titular  
                      ,at.id_marca_tarjeta
                      ,mt.nombre_corto as marca_tarjeta
                      ,at.id_entidad_bancaria
                      ,eb.nombre_corto as entidad_bancaria
                      ,at.id_medio_pago
                      ,mp.nombre_corto as medio_pago
                      ,at.activo
                      ,(CASE WHEN at.activo = 'S' THEN 'Activo'
                             WHEN at.activo = 'N' THEN 'Inactivo'
                    END) as activo_completo
                FROM alumno_tarjeta at  
                    INNER JOIN alumno a on a.id_alumno = at.id_alumno
                    INNER JOIN persona p on p.id_persona = a.id_persona
                    INNER JOIN entidad_bancaria eb on eb.id_entidad_bancaria = at.id_entidad_bancaria
                    INNER JOIN medio_pago mp on at.id_medio_pago = mp.id_medio_pago
                    INNER JOIN marca_tarjeta mt on at.id_marca_tarjeta = mt.id_marca_tarjeta
                WHERE p.id_persona = {$this->persona} --at.id_alumno = {$this->persona}
                ORDER BY at.id_alumno
                        ,at.id_alumno_tarjeta
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->datos_formas_cobro = consultar_fuente($sql);

        // Obtiene los datos de la cuenta corriente asociada a la persona ---------------------------------
        $sql = "SELECT acc.id_alumno_cc
                      ,acc.id_alumno
                      ,acc.usuario_alta
                      /*,(CASE WHEN acc.id_cargo_cuenta_corriente = 1 THEN 'Insc. Anual'
                             WHEN subconsulta_cuenta_corriente.numero_comprobante IS NULL THEN 'Cuota'
                             ELSE 'Pago'
                        END) AS concepto*/
                      ,(CASE WHEN (subconsulta_cuenta_corriente.numero_comprobante IS NOT NULL AND acc.id_cargo_cuenta_corriente = 1) THEN 'Pago de Inscripción Anual ' --|| acc.cuota
                             WHEN (subconsulta_cuenta_corriente.numero_comprobante IS NOT NULL AND acc.id_cargo_cuenta_corriente = 2) THEN 'Pago de cuota ' || acc.cuota
                             ELSE acc.descripcion
                        END) AS concepto
                      ,(CASE WHEN subconsulta_cuenta_corriente.numero_comprobante IS NOT NULL THEN subconsulta_cuenta_corriente.fecha_pago
                             ELSE acc.fecha_generacion_cc
                        END) AS fecha
                      ,acc.id_cargo_cuenta_corriente
                      ,subconsulta_cuenta_corriente.id_medio_pago
                      ,(CASE WHEN subconsulta_cuenta_corriente.id_medio_pago IS NOT NULL THEN mp.nombre
                             ELSE ''
                        END) AS medio_pago
                      ,subconsulta_cuenta_corriente.id_marca_tarjeta
                      ,(CASE WHEN subconsulta_cuenta_corriente.id_marca_tarjeta IS NOT NULL THEN mt.nombre
                             ELSE ''
                        END) AS marca_tarjeta
                      ,subconsulta_cuenta_corriente.id_estado_cuota  
                      ,ec.nombre as estado_cuota
                      ,subconsulta_cuenta_corriente.importe
                      ,subconsulta_cuenta_corriente.id_motivo_rechazo1
                      ,mr.nombre AS motivo_rechazo1
                      ,subconsulta_cuenta_corriente.id_motivo_rechazo2
                      ,mr2.nombre AS motivo_rechazo2
                      ,subconsulta_cuenta_corriente.numero_comprobante
                      ,subconsulta_cuenta_corriente.numero_autorizacion
                      ,subconsulta_cuenta_corriente.numero_lote
                FROM alumno_cuenta_corriente acc
                    INNER JOIN alumno a on acc.id_alumno = a.id_alumno  
                    INNER JOIN persona p on p.id_persona = a.id_persona
                    INNER JOIN (select id_alumno_cc, id_transaccion_cc, fecha_transaccion, id_estado_cuota, importe, fecha_pago
                                      ,fecha_respuesta_prisma, numero_comprobante, numero_autorizacion, numero_lote
                                      ,id_medio_pago, id_marca_tarjeta, id_motivo_rechazo1, id_motivo_rechazo2
                                from transaccion_cuenta_corriente) as subconsulta_cuenta_corriente ON subconsulta_cuenta_corriente.id_alumno_cc = acc.id_alumno_cc
                    INNER JOIN estado_cuota ec on ec.id_estado_cuota = subconsulta_cuenta_corriente.id_estado_cuota
                    LEFT OUTER JOIN motivo_rechazo mr on mr.id_motivo_rechazo = subconsulta_cuenta_corriente.id_motivo_rechazo1
                    LEFT OUTER JOIN motivo_rechazo mr2 on mr2.id_motivo_rechazo = subconsulta_cuenta_corriente.id_motivo_rechazo2
                    LEFT OUTER JOIN medio_pago mp on mp.id_medio_pago = subconsulta_cuenta_corriente.id_medio_pago
                    LEFT OUTER JOIN marca_tarjeta mt on mt.id_marca_tarjeta = subconsulta_cuenta_corriente.id_marca_tarjeta
                WHERE p.id_persona = {$this->persona} --a.id_alumno = {$this->persona}
                ORDER BY acc.id_alumno_cc
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        $this->datos_cuenta_corriente = consultar_fuente($sql);
    }

    /*public function es_alumno()
    {
        if (!empty($this->persona)) {
            $sql = "SELECT count(1) as es_alumno FROM alumno WHERE id_persona = ".$this->persona;
            toba::logger()->debug(__METHOD__." : ".$sql);
            $datos = consultar_fuente($sql);

            if ($datos['es_alumno'] == 1) {
                $this->es_alumno = 'S';
            } if ($datos['es_alumno'] == 0) {
                $this->es_alumno = 'N';
            }
        }
    }*/

    public function grabar()
    {
        if (isset($this->persona)) {
            $this->actualizar_persona();
        } else {
            $this->grabar_persona();
        }
        $this->grabar_persona_documentos();
        if ($this->get_es_alumno() == 'S') {
            $this->grabar_datos_alumno();
        }
    }

    private function grabar_persona()
    {
        toba::logger()->info("persona.grabar_persona()");

        $apellidos = conversion_tipo_datos::convertir_null_a_cadena($this->apellidos, constantes::get_valor_constante('TIPO_DATO_STR'));
        $nombres = conversion_tipo_datos::convertir_null_a_cadena($this->nombres, constantes::get_valor_constante('TIPO_DATO_STR'));
        $fecha_nacimiento = isset($this->fecha_nacimiento) ? "'" . $this->fecha_nacimiento . "'" : 'null';
        $telefono = conversion_tipo_datos::convertir_null_a_cadena($this->telefono, constantes::get_valor_constante('TIPO_DATO_STR'));
        $correo_electronico = conversion_tipo_datos::convertir_null_a_cadena($this->correo_electronico, constantes::get_valor_constante('TIPO_DATO_STR'));
        $recibe_notif_x_correo = conversion_tipo_datos::convertir_null_a_cadena($this->recibe_notif_x_correo, constantes::get_valor_constante('TIPO_DATO_STR'));
        $id_localidad_nacimiento = conversion_tipo_datos::convertir_null_a_cadena($this->id_localidad_nacimiento, constantes::get_valor_constante('TIPO_DATO_INT'));
        $id_localidad_residencia = conversion_tipo_datos::convertir_null_a_cadena($this->id_localidad_residencia, constantes::get_valor_constante('TIPO_DATO_INT'));
        $id_nacionalidad = conversion_tipo_datos::convertir_null_a_cadena($this->id_nacionalidad, constantes::get_valor_constante('TIPO_DATO_INT'));
        $activo = conversion_tipo_datos::convertir_null_a_cadena($this->activo, constantes::get_valor_constante('TIPO_DATO_STR'));
        $es_alumno = conversion_tipo_datos::convertir_null_a_cadena($this->es_alumno, constantes::get_valor_constante('TIPO_DATO_STR'));
        $usuario = conversion_tipo_datos::convertir_null_a_cadena($this->usuario, constantes::get_valor_constante('TIPO_DATO_STR'));
        $sexo = conversion_tipo_datos::convertir_null_a_cadena($this->sexo, constantes::get_valor_constante('TIPO_DATO_INT'));

        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        $usuario_alta = toba::usuario()->get_id();

        $sql = "INSERT INTO persona 
	            	(apellidos 
	            	,nombres 
	            	,fecha_nacimiento 
	            	,correo_electronico
	            	,recibe_notif_x_correo 
	            	,telefono
	            	,id_localidad_nacimiento 
	            	,id_localidad_residencia
					,id_nacionalidad
					,activo
					,es_alumno
					,usuario
					,fecha_alta
					,usuario_alta
					) 
				VALUES 
					({$apellidos} 
	            	,{$nombres} 
	            	,{$fecha_nacimiento} 
	            	,{$correo_electronico} 
	            	,{$recibe_notif_x_correo} 
	            	,{$telefono}
	            	,{$id_localidad_nacimiento} 
	            	,{$id_localidad_residencia} 
					,{$id_nacionalidad}
					,{$activo}
					,{$es_alumno}
					,{$usuario}
					,'{$hoy}'
					,'{$usuario_alta}'
					)";

        toba::logger()->debug(__METHOD__." : ".$sql);
        ejecutar_fuente($sql);

        $this->persona = recuperar_secuencia('sq_id_persona');
        if ($this->persona['es_alumno'] == 'S') {
            $this->id_alumno = recuperar_secuencia('sq_id_alumno');
        }

        $sql = "INSERT INTO persona_sexo
                    (id_persona
                    ,id_sexo
                    ,activo
                    ,fecha_alta
                    ,usuario_alta
                    )
                VALUES 
					({$this->persona} 
	            	,{$sexo} 
	            	,'S' 
	            	,'{$hoy}'
					,'{$usuario_alta}'
					)";

        toba::logger()->debug(__METHOD__." : ".$sql);
        ejecutar_fuente($sql);

        return $this->persona;
    }

    private function actualizar_persona()
    {
        $apellidos = conversion_tipo_datos::convertir_null_a_cadena($this->apellidos, constantes::get_valor_constante('TIPO_DATO_STR'));
        $nombres = conversion_tipo_datos::convertir_null_a_cadena($this->nombres, constantes::get_valor_constante('TIPO_DATO_STR'));
        $fecha_nacimiento = isset($this->fecha_nacimiento) ? "'" . $this->fecha_nacimiento . "'" : 'null';
        $telefono = conversion_tipo_datos::convertir_null_a_cadena($this->telefono, constantes::get_valor_constante('TIPO_DATO_STR'));
        $correo_electronico = conversion_tipo_datos::convertir_null_a_cadena($this->correo_electronico, constantes::get_valor_constante('TIPO_DATO_STR'));
        $recibe_notif_x_correo = conversion_tipo_datos::convertir_null_a_cadena($this->recibe_notif_x_correo, constantes::get_valor_constante('TIPO_DATO_STR'));
        $id_localidad_nacimiento = conversion_tipo_datos::convertir_null_a_cadena($this->id_localidad_nacimiento, constantes::get_valor_constante('TIPO_DATO_INT'));
        $id_localidad_residencia = conversion_tipo_datos::convertir_null_a_cadena($this->id_localidad_residencia, constantes::get_valor_constante('TIPO_DATO_INT'));
        $id_nacionalidad = conversion_tipo_datos::convertir_null_a_cadena($this->id_nacionalidad, constantes::get_valor_constante('TIPO_DATO_INT'));
        $activo = conversion_tipo_datos::convertir_null_a_cadena($this->activo, constantes::get_valor_constante('TIPO_DATO_STR')) ?? conversion_tipo_datos::convertir_null_a_cadena($this->activo, constantes::get_valor_constante('TIPO_DATO_STR'));
        $es_alumno = conversion_tipo_datos::convertir_null_a_cadena($this->es_alumno, constantes::get_valor_constante('TIPO_DATO_STR'));
        $usuario = conversion_tipo_datos::convertir_null_a_cadena($this->usuario, constantes::get_valor_constante('TIPO_DATO_STR'));
        $sexo = conversion_tipo_datos::convertir_null_a_cadena($this->sexo, constantes::get_valor_constante('TIPO_DATO_INT'));

        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        $usuario_modif = toba::usuario()->get_id();

        $sql = "UPDATE persona 
                SET apellidos = $apellidos 
				   ,nombres = $nombres
                   ,fecha_nacimiento = $fecha_nacimiento
				   ,correo_electronico = $correo_electronico
                   ,recibe_notif_x_correo = $recibe_notif_x_correo
                   ,telefono = $telefono
                   ,id_localidad_nacimiento = $id_localidad_nacimiento
                   ,id_localidad_residencia = $id_localidad_residencia
                   ,id_nacionalidad = $id_nacionalidad 
				   ,activo = $activo
				   ,es_alumno = $es_alumno
                   ,usuario = $usuario
                   ,usuario_ultima_modificacion = '{$usuario_modif}'
                   ,fecha_ultima_modificacion = '{$hoy}'
				WHERE id_persona = {$this->persona}
			   ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        ejecutar_fuente($sql);

        $sql = "UPDATE persona_sexo
                SET id_sexo = $sexo
                   ,activo = 'S'
                   ,usuario_ultima_modificacion = '{$usuario_modif}'
                   ,fecha_ultima_modificacion = '{$hoy}'
                WHERE id_persona = {$this->persona} 
               ";

        toba::logger()->debug(__METHOD__." : ".$sql);
        ejecutar_fuente($sql);
        return $this->persona;


    }

    private function grabar_datos_alumno()
    {
        toba::logger()->info("persona.grabar_datos_alumno()");

        $legajo = conversion_tipo_datos::convertir_null_a_cadena($this->legajo, constantes::get_valor_constante('TIPO_DATO_STR'));
        $extranjero = conversion_tipo_datos::convertir_null_a_cadena($this->extranjero, constantes::get_valor_constante('TIPO_DATO_STR'));
        $regular = conversion_tipo_datos::convertir_null_a_cadena($this->regular, constantes::get_valor_constante('TIPO_DATO_STR'));
        $id_motivo_desercion = conversion_tipo_datos::convertir_null_a_cadena($this->id_motivo_desercion, constantes::get_valor_constante('TIPO_DATO_INT'));
        $es_celiaco = conversion_tipo_datos::convertir_null_a_cadena($this->es_celiaco, constantes::get_valor_constante('TIPO_DATO_STR'));
        $direccion_calle = conversion_tipo_datos::convertir_null_a_cadena($this->direccion_calle, constantes::get_valor_constante('TIPO_DATO_STR'));
        $direccion_numero = conversion_tipo_datos::convertir_null_a_cadena($this->direccion_numero, constantes::get_valor_constante('TIPO_DATO_STR'));
        $direccion_piso = conversion_tipo_datos::convertir_null_a_cadena($this->direccion_piso, constantes::get_valor_constante('TIPO_DATO_STR'));
        $direccion_depto = conversion_tipo_datos::convertir_null_a_cadena($this->direccion_depto, constantes::get_valor_constante('TIPO_DATO_STR'));

        $sql = "SELECT id_persona
                      ,id_alumno  
                FROM alumno
                WHERE id_persona =  {$this->persona}
               ";

        $datos = consultar_fuente($sql);

        if ($datos == null) {
            $sql = "INSERT INTO alumno (id_persona, legajo, extranjero, regular, es_celiaco, direccion_calle, direccion_numero, direccion_piso, direccion_depto) 
					    VALUES ({$this->persona}, {$legajo}, {$extranjero}, {$regular}, {$es_celiaco}, {$direccion_calle}, {$direccion_numero}, {$direccion_piso}, {$direccion_depto})";
        } else {
            $sql = "UPDATE alumno
                    SET legajo = $legajo
                       ,extranjero = $extranjero
                       ,regular = $regular
                       ,es_celiaco = $es_celiaco
                       ,direccion_calle = $direccion_calle
                       ,direccion_numero = $direccion_numero
                       ,direccion_piso = $direccion_piso
                       ,direccion_depto = $direccion_depto
                    WHERE id_persona = {$this->persona}
                        AND id_alumno = {$datos[0]['id_alumno']}
                   ";

        }
        toba::logger()->debug(__METHOD__." : ".$sql);
        ejecutar_fuente($sql);
    }

    public function grabar_persona_allegados()
    {
        foreach ($this->persona_allegados as $persona_allegado) {
            //esta bien que queden cambiado los datos id_alumno y id_persona
            $sql = "SELECT id_persona
                          ,id_alumno 
					FROM persona_allegado 
					WHERE id_alumno = {$this->persona} 
					    AND	id_persona = '{$persona_allegado['id_persona']}'";

            $datos = consultar_fuente($sql);

            $fecha = new fecha();
            $hoy = $fecha->get_timestamp_db();
            $usuario = toba::usuario()->get_id();

            if ($datos == null) {
                $sql = "INSERT INTO persona_allegado (id_alumno, id_persona, id_tipo_allegado, id_estudio_alcanzado, id_ocupacion
                                                     ,tutor, activo, fecha_alta, usuario_alta, fecha_ultima_modificacion, usuario_ultima_modificacion) 
					    VALUES ({$this->persona},'{$persona_allegado['id_persona']}', '{$persona_allegado['id_tipo_allegado']}', '{$persona_allegado['id_estudio_alcanzado']}', '{$persona_allegado['id_ocupacion']}'
					            ,'{$persona_allegado['tutor']}', '{$persona_allegado['activo']}', '{$hoy}', '{$usuario}', '{$hoy}', '{$usuario}')";
            } else {
                $sql = "UPDATE persona_allegado 
						SET id_tipo_allegado = '{$persona_allegado['id_tipo_allegado']}'
                           ,id_estudio_alcanzado = '{$persona_allegado['id_estudio_alcanzado']}'
                           ,id_ocupacion = '{$persona_allegado['id_ocupacion']}'
                           ,tutor = '{$persona_allegado['tutor']}'
                           ,activo = '{$persona_allegado['activo']}'
                           ,fecha_ultima_modificacion = '{$hoy}'
                           ,usuario_ultima_modificacion = '{$usuario}'
						WHERE id_alumno = {$this->persona} 
						    AND	id_persona = '{$persona_allegado['id_persona']}'
					   ";
            }
            toba::logger()->debug(__METHOD__." : ".$sql);
            ejecutar_fuente($sql);
        }
    }

    public function grabar_persona_documentos()
    {
        //Primero borro todas los documentos de la persona
        $sql = "DELETE FROM persona_tipo_documento
                WHERE id_persona = {$this->persona}
               ";
        toba::logger()->debug(__METHOD__ . " : " . $sql);
        ejecutar_fuente($sql);

        //Luego recorro el array que recibo y voy insertando otra vez los datos
        foreach ($this->persona_documentos as $persona_documento) {
            /*$numero = conversion_tipo_datos::convertir_null_a_cadena($persona_documento['identificacion'], constantes::get_valor_constante('TIPO_DATO_STR'));

            $sql = "SELECT id_persona
                          ,numero
					FROM persona_tipo_documento 
					WHERE id_persona = {$this->persona} 
                        AND id_tipo_documento = '{$persona_documento['id']}'
				   ";
            toba::logger()->debug(__METHOD__ . " : " . $sql);
            $datos = consultar_fuente($sql);*/

            $fecha = new fecha();
            $hoy = $fecha->get_timestamp_db();
            $usuario = toba::usuario()->get_id();

            //if ($datos == null) {
                $sql = "INSERT INTO persona_tipo_documento (id_persona, id_tipo_documento, numero, activo, fecha_alta, usuario_alta) 
					    VALUES ({$this->persona},'{$persona_documento['id']}', '{$persona_documento['identificacion']}', '{$persona_documento['activo']}', '{$hoy}', '{$usuario}')";
            /*} else {
                $sql = "UPDATE persona_tipo_documento 
						SET numero = $numero
                           ,id_tipo_documento = '{$persona_documento['id']}'
                           ,activo = '{$persona_documento['activo']}'
						WHERE id_persona = {$this->persona}
                            AND id_tipo_documento = '{$persona_documento['id']}'
                            AND numero <> '{$datos[0]['numero']}'
					   ";
            }*/
            toba::logger()->debug(__METHOD__ . " : " . $sql);
            ejecutar_fuente($sql);
        }
    }

    public function grabar_datos_getion_academica()
    {
        foreach ($this->datos_academicos as $alumno_dato_academico) {
            if ($alumno_dato_academico['id_alumno_dato_cursada'] == null) {
                $sql = "INSERT INTO alumno_datos_cursada (id_alumno, id_grado, division, anio_cursada, genero_costo_inscripcion, pago_inscripcion) 
					    VALUES ({$this->persona},'{$alumno_dato_academico['id_grado']}', '{$alumno_dato_academico['division']}', '{$alumno_dato_academico['anio_cursada']}'
					          ,'{$alumno_dato_academico['genero_costo_inscripcion']}','{$alumno_dato_academico['pago_inscripcion']}')
				       ";
            } else {
                $sql = "UPDATE alumno_datos_cursada 
						SET id_grado = '{$alumno_dato_academico['id_grado']}'
                           ,division = '{$alumno_dato_academico['division']}'
                           ,anio_cursada = '{$alumno_dato_academico['anio_cursada']}'
                           ,genero_costo_inscripcion = '{$alumno_dato_academico['genero_costo_inscripcion']}'
                           ,pago_inscripcion = '{$alumno_dato_academico['pago_inscripcion']}'
						WHERE id_alumno = {$this->persona}
                            AND id_alumno_dato_cursada = {$alumno_dato_academico['id_alumno_dato_cursada']}
					   ";
            }
            toba::logger()->debug(__METHOD__." : ".$sql);
            ejecutar_fuente($sql);
        }
    }

    public function grabar_datos_formas_cobro()
    {
        foreach ($this->datos_formas_cobro as $alumno_dato_forma_cobro) {
            if (isset($alumno_dato_forma_cobro['apex_ei_analisis_fila'])) {
                if ($alumno_dato_forma_cobro['apex_ei_analisis_fila'] == 'B') {
                    toba::logger()->error('por lo pronto no hago, pero debo borrar el registro');
                    toba::logger()->error($alumno_dato_forma_cobro);
                    $sql = "DELETE FROM alumno_tarjeta
                            WHERE id_alumno = {$this->persona}
                                AND id_alumno_tarjeta = {$alumno_dato_forma_cobro['id_alumno_tarjeta']}
                           ";
                } elseif ($alumno_dato_forma_cobro['id_alumno_tarjeta'] == null) {
                    $sql = "INSERT INTO alumno_tarjeta (id_alumno, id_medio_pago, id_marca_tarjeta, id_entidad_bancaria, numero_tarjeta, nombre_titular, activo) 
					        VALUES ({$this->persona},'{$alumno_dato_forma_cobro['id_medio_pago']}', '{$alumno_dato_forma_cobro['id_marca_tarjeta']}', '{$alumno_dato_forma_cobro['id_entidad_bancaria']}'
					            ,'{$alumno_dato_forma_cobro['numero_tarjeta']}','{$alumno_dato_forma_cobro['nombre_titular']}', '{$alumno_dato_forma_cobro['activo']}')
				           ";
                    //}


                    //if ($alumno_dato_forma_cobro['id_alumno_tarjeta'] == null) {

                } else {
                    $sql = "UPDATE alumno_tarjeta 
                            SET id_medio_pago = '{$alumno_dato_forma_cobro['id_medio_pago']}'
                               ,id_marca_tarjeta = '{$alumno_dato_forma_cobro['id_marca_tarjeta']}'
                               ,id_entidad_bancaria = '{$alumno_dato_forma_cobro['id_entidad_bancaria']}'
                               ,numero_tarjeta = '{$alumno_dato_forma_cobro['numero_tarjeta']}'
                               ,nombre_titular = '{$alumno_dato_forma_cobro['nombre_titular']}'
                               ,activo = '{$alumno_dato_forma_cobro['activo']}'
                            WHERE id_alumno = {$this->persona}
                                AND id_alumno_tarjeta = {$alumno_dato_forma_cobro['id_alumno_tarjeta']}
                           ";
                }
                toba::logger()->debug(__METHOD__ . " : " . $sql);
                ejecutar_fuente($sql);
            }
        }
    }

    public function generar_cargos_persona()
    {
        toba::logger()->info("persona.generar_cargos_persona()");

        $alumnos_con_error = array();
        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        $usuario = toba::usuario()->get_id();

        //Valido por cada persona que ya no tenga ese cargo generado para ese mes/año
        if ($this->validar_generacion_cargo($this->datos_generacion_cargos)) {
            //Primero, inserto en la tabla alumno_cuenta_corriente
            $sql = "INSERT INTO alumno_cuenta_corriente (id_alumno, usuario_alta, fecha_generacion_cc, cuota, descripcion, id_cargo_cuenta_corriente) 
				    VALUES ({$this->persona},'{$usuario}', '{$hoy}', '{$this->cuota_completa}', '{$this->descripcion_cuota}', '{$this->cargo_a_generar}')
			   ";

            toba::logger()->debug(__METHOD__ . " : " . $sql);
            ejecutar_fuente($sql);

            //Obtengo el id del cuenta_corriente generado
            $sql = "SELECT currval('sq_id_alumno_cc') as seq";
            $datos = consultar_fuente($sql);
            $id_alumno_cc = $datos[0]['seq'];

            //Segundo, inserto en la tabla transaccion_cuenta_corriente
            $sql1 = "INSERT INTO transaccion_cuenta_corriente (id_alumno_cc, fecha_transaccion, id_estado_cuota, importe, usuario_ultima_modificacion, fecha_ultima_modificacion)
                     VALUES ({$id_alumno_cc}, '{$hoy}', 1, '{$this->importe_cuota}', '{$usuario}', '{$hoy}');                                        
                   ";

            toba::logger()->debug(__METHOD__ . " : " . $sql1);
            ejecutar_fuente($sql1);
        } else {
            $alumnos_con_error['id_persona'] = $this->persona;
        }
        if (!empty($alumnos_con_error)) {
            $this->mostrar_mensajes($alumnos_con_error);
        } else {
            toba::notificacion()->agregar('Los cargos en los alumnos fueron generados con éxito.', 'info');
        }
    }

    /*
     * Valida que para ese alumno ya no se haya generado ese cargo para un año y cuota determinado
     */
    protected function validar_generacion_cargo()
    {
        $salida = true;
        $sql = "SELECT ''
                FROM alumno_cuenta_corriente
                WHERE id_alumno = {$this->persona}
                    AND cuota = '{$this->cuota_completa}'
                    AND id_cargo_cuenta_corriente = '{$this->cargo_a_generar}'
               ";

        toba::logger()->debug(__METHOD__ . " : " . $sql);
        $datos = consultar_fuente($sql);

        if ($datos) {
            $salida = false;
        }
        return $salida;
    }

    protected function mostrar_mensajes($datos = null)
    {
        $errores = $this->cargar_mensajes_error($datos);

        //Se prepara el mensaje de error para la notificación
        if (!empty($errores)) {
            $str_error = '';
            foreach ($errores as $error) {
                $str_error .= $error."<br />";
            }
            toba::notificacion()->agregar($str_error, 'error');
        }
    }

    protected function cargar_mensajes_error($array_datos_invalidos)
    {
        $mensaje = '';
        $errores = [];
        if (isset($array_datos_invalidos['id_persona'])) {
            foreach ($array_datos_invalidos as $clave => $id_persona) {
                $filtro['id_alumno'] = $id_persona;
                $filtro['solo_alumnos'] = true;
                $persona = dao_consultas::get_nombres_persona($filtro);
                if ($persona) {
                    $nombre_persona = $persona[0]['nombre_completo'];
                    $mensaje .= $nombre_persona. "<br />";
                } else {
                    $mensaje .= $id_persona. "<br />";
                }
            }
            $errores[] = "Los siguientes alumnos ya tienen el cargo generado para el período seleccionado: <br />" . $mensaje;
            unset($mensaje);
        }
        return $errores;
    }

    public function grabar_pago_persona()
    {
        toba::logger()->info("persona.grabar_pago_persona()");

        $sql = "INSERT INTO transaccion_cuenta_corriente (id_alumno_cc, fecha_transaccion, id_estado_cuota, importe, fecha_pago, fecha_respuesta_prisma
                                                         ,usuario_ultima_modificacion, fecha_ultima_modificacion
                                                         ,numero_comprobante, numero_lote, numero_autorizacion, id_medio_pago
                                                         ,id_marca_tarjeta, codigo_rechazo1, codigo_rechazo2) 
				VALUES ({$this->id_alumno_cc}, now(),'{$this->id_estado_cuota}', '{$this->importe_pago}', {$this->fecha_pago}, {$this->fecha_respuesta_prisma}
				       ,'{$this->usuario_ultima_modificacion}', now()
				       ,{$this->numero_comprobante}, {$this->numero_lote}, {$this->numero_autorizacion}, '{$this->id_medio_pago}'
				       ,{$this->id_marca_tarjeta}, {$this->id_motivo_rechazo1}, {$this->id_motivo_rechazo2})
			   ";

        toba::logger()->debug(__METHOD__ . " : " . $sql);
        ejecutar_fuente($sql);

        //Actualizo el campo procesado de la tabla archivo_respuesta_detalle solo si viene del alta masiva
        if ($this->modo == 'alta_masiva') {
            $sql = "UPDATE archivo_respuesta_detalle 
                    SET procesado = 1
                    FROM (SELECT ard.id_archivo_respuesta_detalle
                          FROM archivo_respuesta_detalle ard
                            INNER JOIN archivo_respuesta ar on ard.id_archivo_respuesta = ar.id_archivo_respuesta
                          WHERE id_alumno_cc = {$this->id_alumno_cc} 
                            AND ar.cuota = '{$this->cuota_completa}') AS subconsulta
                    WHERE archivo_respuesta_detalle.id_archivo_respuesta_detalle = subconsulta.id_archivo_respuesta_detalle
               ";

            toba::logger()->debug(__METHOD__ . " : " . $sql);
            ejecutar_fuente($sql);
        }

        if ($this->mostrar_mensaje_individual) {
            toba::notificacion()->agregar('El alta del pago fue realizada con éxito.', 'info');
        }
    }
}
