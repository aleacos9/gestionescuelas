--CREATE DATABASE gestion_escuelas with owner postgres;

--SET statement_timeout = 0;
SET client_encoding = 'LATIN1';
--SET standard_conforming_strings = off;
--SELECT pg_catalog.set_config('search_path', '', false);
--SET check_function_bodies = false;
--SET client_min_messages = warning;
--SET escape_string_warning = off;

--CREATE SCHEMA public;
--ALTER SCHEMA public OWNER TO postgres;

CREATE SCHEMA auditoria;
ALTER SCHEMA auditoria OWNER TO postgres;

CREATE TABLE parametros_sistema
(
    parametro character varying(42) NOT NULL,
    descripcion character varying(255) NOT NULL,
    desc_corta character varying(120) NOT NULL,
    valor character varying(120) NOT NULL,
    version_publicacion character varying(20) NOT NULL
);

ALTER TABLE parametros_sistema OWNER TO postgres;
COMMENT ON TABLE parametros_sistema IS 'Tabla que contendra los distintos parametros generales del sistema.';


CREATE SEQUENCE sq_id_localidad
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_localidad OWNER TO postgres;
GRANT ALL ON SEQUENCE sq_id_localidad TO postgres;


CREATE SEQUENCE sq_id_pais
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_pais OWNER TO postgres;

CREATE TABLE pais
(
    id_pais integer DEFAULT nextval(('sq_id_pais')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    CONSTRAINT id_pais PRIMARY KEY (id_pais)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE pais OWNER TO postgres;


CREATE SEQUENCE sq_id_provincia
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_provincia OWNER TO postgres;

CREATE TABLE provincia
(
    id_provincia integer DEFAULT nextval(('sq_id_provincia')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    id_pais integer NOT NULL,
    CONSTRAINT id_provincia PRIMARY KEY (id_provincia),
    CONSTRAINT id_pais FOREIGN KEY (id_pais)
        REFERENCES pais(id_pais) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE provincia OWNER TO postgres;


CREATE TABLE localidad
(
    id_localidad integer DEFAULT nextval(('sq_id_localidad')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    id_provincia integer NOT NULL,
    CONSTRAINT id_localidad PRIMARY KEY (id_localidad),
    CONSTRAINT id_provincia FOREIGN KEY (id_provincia)
        REFERENCES provincia(id_provincia) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE localidad OWNER TO postgres;


CREATE SEQUENCE sq_id_nacionalidad
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_nacionalidad OWNER TO postgres;

CREATE TABLE nacionalidad
(
    id_nacionalidad integer DEFAULT nextval(('sq_id_nacionalidad')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    CONSTRAINT id_nacionalidad PRIMARY KEY (id_nacionalidad)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE nacionalidad OWNER TO postgres;


CREATE SEQUENCE sq_id_estudio_alcanzado
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_estudio_alcanzado OWNER TO postgres;

CREATE TABLE estudio_alcanzado
(
    id_estudio_alcanzado integer DEFAULT nextval(('sq_id_estudio_alcanzado')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_estudio_alcanzado PRIMARY KEY (id_estudio_alcanzado)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE estudio_alcanzado OWNER TO postgres;


CREATE SEQUENCE sq_id_ocupacion
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_ocupacion OWNER TO postgres;

CREATE TABLE ocupacion
(
    id_ocupacion integer DEFAULT nextval(('sq_id_ocupacion')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_ocupacion PRIMARY KEY (id_ocupacion)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE ocupacion OWNER TO postgres;

CREATE SEQUENCE sq_id_persona
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_persona OWNER TO postgres;

CREATE TABLE persona
(
    id_persona integer DEFAULT nextval(('sq_id_persona')::regclass) NOT NULL,
    apellidos character varying(100) NOT NULL,
    nombres character varying(100) NOT NULL,
    fecha_nacimiento date,
    id_localidad_nacimiento integer,
    id_localidad_residencia integer,
    id_nacionalidad integer,
    correo_electronico character varying(120) NOT NULL,
    activo character(1) DEFAULT 'N'::bpchar,
    es_alumno character(1) DEFAULT 'N'::bpchar,
    usuario character varying(20),
    recibe_notif_x_correo character(1) DEFAULT 'N'::bpchar,
    telefono character varying(30),
    fecha_alta timestamp without time zone,
    usuario_alta character varying(120),
    fecha_ultima_modificacion timestamp without time zone,
    usuario_ultima_modificacion character varying(120),
    CONSTRAINT id_persona PRIMARY KEY (id_persona),
    CONSTRAINT id_localidad_nacimiento FOREIGN KEY (id_localidad_nacimiento)
        REFERENCES localidad (id_localidad) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_localidad_residencia FOREIGN KEY (id_localidad_residencia)
        REFERENCES localidad (id_localidad) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_nacionalidad FOREIGN KEY (id_nacionalidad)
        REFERENCES nacionalidad (id_nacionalidad) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE persona OWNER TO postgres;


CREATE SEQUENCE sq_id_nivel
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_nivel OWNER TO postgres;

CREATE TABLE nivel
(
    id_nivel integer DEFAULT nextval(('sq_id_nivel')::regclass) NOT NULL,
    nombre character varying(60) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_nivel PRIMARY KEY (id_nivel)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE nivel OWNER TO postgres;


CREATE SEQUENCE sq_id_grado
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_grado OWNER TO postgres;

CREATE TABLE grado
(
    id_grado integer DEFAULT nextval(('sq_id_grado')::regclass) NOT NULL,
    nombre character varying(60) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    id_nivel integer NOT NULL,
    CONSTRAINT id_grado PRIMARY KEY (id_grado),
    CONSTRAINT id_nivel FOREIGN KEY (id_nivel)
        REFERENCES nivel (id_nivel) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE grado OWNER TO postgres;


CREATE SEQUENCE sq_id_motivo_desercion
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_motivo_desercion OWNER TO postgres;

CREATE TABLE motivo_desercion
(
    id_motivo_desercion integer DEFAULT nextval(('sq_id_motivo_desercion')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_motivo_desercion PRIMARY KEY (id_motivo_desercion)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE motivo_desercion OWNER TO postgres;


CREATE SEQUENCE sq_id_alumno
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_alumno OWNER TO postgres;

CREATE TABLE alumno
(
    id_alumno integer DEFAULT nextval(('sq_id_alumno')::regclass) NOT NULL,
    id_persona integer NOT NULL,
    legajo character varying(20),
    extranjero character(1) DEFAULT 'N'::bpchar,
    regular character(1) DEFAULT 'S'::bpchar,
    id_motivo_desercion integer,
    es_celiaco character(1) DEFAULT 'N'::bpchar,
    direccion_calle character varying(120),
    direccion_numero character varying(20),
    direccion_piso character varying(10),
    direccion_depto character varying(10),
    CONSTRAINT id_alumno PRIMARY KEY (id_alumno),
    CONSTRAINT id_persona FOREIGN KEY (id_persona)
        REFERENCES persona (id_persona) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_motivo_desercion FOREIGN KEY (id_motivo_desercion)
        REFERENCES motivo_desercion (id_motivo_desercion) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE alumno OWNER TO postgres;


CREATE SEQUENCE sq_id_dato_cursada
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_dato_cursada OWNER TO postgres;

CREATE TABLE alumno_datos_cursada
(
    id_alumno_dato_cursada integer DEFAULT nextval(('sq_id_dato_cursada')::regclass) NOT NULL,
    id_alumno integer NOT NULL,
    id_grado integer NOT NULL,
    division character varying(10),
    anio_cursada character varying(10),
    genero_costo_inscripcion character(1) DEFAULT 'N'::bpchar,
    pago_inscripcion character(1) DEFAULT 'N'::bpchar,
    CONSTRAINT id_alumno_dato_cursada PRIMARY KEY (id_alumno_dato_cursada),
    CONSTRAINT id_alumno FOREIGN KEY (id_alumno)
        REFERENCES alumno (id_alumno) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_grado FOREIGN KEY (id_grado)
        REFERENCES grado (id_grado) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT alumno_grado_uq UNIQUE (id_alumno, id_grado)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE alumno_datos_cursada OWNER TO postgres;


CREATE SEQUENCE sq_id_rol
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_rol OWNER TO postgres;

CREATE TABLE rol
(
    id_rol integer DEFAULT nextval(('sq_id_rol')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(60) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_rol PRIMARY KEY (id_rol)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE rol OWNER TO postgres;


CREATE SEQUENCE sq_id_persona_rol
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_persona_rol OWNER TO postgres;

CREATE TABLE persona_rol
(
    id_persona_rol integer DEFAULT nextval(('sq_id_persona_rol')::regclass) NOT NULL,
    id_persona integer NOT NULL,
    id_rol integer NOT NULL,
    fecha_alta timestamp without time zone,
    usuario_alta character varying(120),
    CONSTRAINT id_persona_rol PRIMARY KEY (id_persona_rol),
    CONSTRAINT id_persona FOREIGN KEY (id_persona)
        REFERENCES persona (id_persona) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_rol FOREIGN KEY (id_rol)
        REFERENCES rol (id_rol) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE persona_rol OWNER TO postgres;


CREATE SEQUENCE sq_id_sexo
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_sexo OWNER TO postgres;

CREATE TABLE sexo
(
    id_sexo integer DEFAULT nextval(('sq_id_sexo')::regclass) NOT NULL,
    nombre character varying(60) NOT NULL,
    nombre_corto character(2) NOT NULL,
    CONSTRAINT id_sexo PRIMARY KEY (id_sexo)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE nacionalidad OWNER TO postgres;


CREATE SEQUENCE sq_id_persona_sexo
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_persona_sexo OWNER TO postgres;

CREATE TABLE persona_sexo
(
    id_persona_sexo integer DEFAULT nextval(('sq_id_persona_sexo')::regclass) NOT NULL,
    id_persona integer NOT NULL,
    id_sexo integer NOT NULL,
    activo character(1) DEFAULT 'N'::bpchar,
    fecha_alta timestamp without time zone,
    usuario_alta character varying(120),
    usuario_ultima_modificacion character varying(120),
    fecha_ultima_modificacion timestamp without time zone,
    CONSTRAINT id_persona_sexo PRIMARY KEY (id_persona_sexo),
    CONSTRAINT id_persona FOREIGN KEY (id_persona)
        REFERENCES persona (id_persona) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_sexo FOREIGN KEY (id_sexo)
        REFERENCES sexo (id_sexo) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT persona_sexo_uq UNIQUE (id_persona, id_sexo)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE persona_sexo OWNER TO postgres;


CREATE SEQUENCE sq_id_tipo_allegado
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_tipo_allegado OWNER TO postgres;

CREATE TABLE tipo_allegado
(
    id_tipo_allegado integer DEFAULT nextval(('sq_id_tipo_allegado')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    jerarquia integer,
    CONSTRAINT id_tipo_allegado PRIMARY KEY (id_tipo_allegado)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE tipo_allegado OWNER TO postgres;


CREATE SEQUENCE sq_id_tipo_documento
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_tipo_documento OWNER TO postgres;

CREATE TABLE tipo_documento
(
    id_tipo_documento integer DEFAULT nextval(('sq_id_tipo_documento')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(50) NOT NULL,
    jerarquia integer,
    observaciones character varying(120),
    extranjero character(1) DEFAULT 'N'::bpchar,
    CONSTRAINT id_tipo_documento PRIMARY KEY (id_tipo_documento)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE tipo_documento OWNER TO postgres;


CREATE SEQUENCE sq_id_persona_allegado
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_persona_allegado OWNER TO postgres;

CREATE TABLE persona_allegado
(
    id_persona_allegado integer DEFAULT nextval(('sq_id_persona_allegado')::regclass) NOT NULL,
    id_persona integer NOT NULL,
    id_alumno integer NOT NULL,
    id_tipo_allegado integer NOT NULL,
    id_estudio_alcanzado integer,
    id_ocupacion integer,
    tutor character(1) DEFAULT 'N'::bpchar,
    activo character(1) DEFAULT 'N'::bpchar,
    fecha_alta timestamp without time zone,
    usuario_alta character varying(120),
    fecha_ultima_modificacion timestamp without time zone,
    usuario_ultima_modificacion character varying(120),
    CONSTRAINT id_persona_allegado PRIMARY KEY (id_persona_allegado),
    CONSTRAINT id_persona FOREIGN KEY (id_persona)
        REFERENCES persona (id_persona) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_alumno FOREIGN KEY (id_alumno)
        REFERENCES alumno (id_alumno) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_tipo_allegado FOREIGN KEY (id_tipo_allegado)
        REFERENCES tipo_allegado (id_tipo_allegado) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_estudio_alcanzado FOREIGN KEY (id_estudio_alcanzado)
        REFERENCES estudio_alcanzado (id_estudio_alcanzado) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_ocupacion FOREIGN KEY (id_ocupacion)
        REFERENCES ocupacion (id_ocupacion) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT alumno_persona_uq UNIQUE (id_persona, id_alumno)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE persona_allegado OWNER TO postgres;


CREATE SEQUENCE sq_id_persona_tipo_documento
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_persona_tipo_documento OWNER TO postgres;

CREATE TABLE persona_tipo_documento
(
    id_persona_tipo_documento integer DEFAULT nextval(('sq_id_persona_tipo_documento')::regclass) NOT NULL,
    id_persona integer NOT NULL,
    id_tipo_documento integer NOT NULL,
    numero character varying(20) NOT NULL,
    activo character(1) DEFAULT 'N'::bpchar,
    fecha_alta timestamp without time zone,
    usuario_alta character varying(120),
    CONSTRAINT id_persona_tipo_documento PRIMARY KEY (id_persona_tipo_documento),
    CONSTRAINT id_persona FOREIGN KEY (id_persona)
        REFERENCES persona (id_persona) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_tipo_documento FOREIGN KEY (id_tipo_documento)
        REFERENCES tipo_documento (id_tipo_documento) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT persona_documento_uq UNIQUE (id_persona, id_tipo_documento, numero)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE persona_tipo_documento OWNER TO postgres;


CREATE SEQUENCE sq_id_medio_pago
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_medio_pago OWNER TO postgres;

CREATE TABLE medio_pago
(
    id_medio_pago integer DEFAULT nextval(('sq_id_medio_pago')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    jerarquia integer,
    CONSTRAINT id_medio_pago PRIMARY KEY (id_medio_pago)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE medio_pago OWNER TO postgres;


CREATE SEQUENCE sq_id_marca_tarjeta
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_marca_tarjeta OWNER TO postgres;

CREATE TABLE marca_tarjeta
(
    id_marca_tarjeta integer DEFAULT nextval(('sq_id_marca_tarjeta')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    jerarquia integer,
    CONSTRAINT id_marca_tarjeta PRIMARY KEY (id_marca_tarjeta)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE marca_tarjeta OWNER TO postgres;


CREATE SEQUENCE sq_id_entidad_bancaria
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_entidad_bancaria OWNER TO postgres;


CREATE TABLE entidad_bancaria
(
    id_entidad_bancaria integer DEFAULT nextval(('sq_id_entidad_bancaria')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_entidad_bancaria PRIMARY KEY (id_entidad_bancaria)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE entidad_bancaria OWNER TO postgres;


CREATE SEQUENCE sq_id_alumno_tarjeta
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_alumno_tarjeta OWNER TO postgres;

CREATE TABLE alumno_tarjeta
(
    id_alumno_tarjeta integer DEFAULT nextval(('sq_id_alumno_tarjeta')::regclass) NOT NULL,
    id_alumno integer NOT NULL,
    id_medio_pago integer NOT NULL,
    id_marca_tarjeta integer NOT NULL,
    id_entidad_bancaria integer,
    numero_tarjeta character(16) NOT NULL,
    activo character(1) DEFAULT 'N'::bpchar,
    nombre_titular character varying(120),
    CONSTRAINT id_alumno_tarjeta PRIMARY KEY (id_alumno_tarjeta),
    CONSTRAINT id_alumno FOREIGN KEY (id_alumno)
        REFERENCES alumno (id_alumno) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_medio_pago FOREIGN KEY (id_medio_pago)
        REFERENCES medio_pago (id_medio_pago) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_marca_tarjeta FOREIGN KEY (id_marca_tarjeta)
        REFERENCES marca_tarjeta (id_marca_tarjeta) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_entidad_bancaria FOREIGN KEY (id_entidad_bancaria)
        REFERENCES entidad_bancaria (id_entidad_bancaria) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT alumno_tarjeta_uq UNIQUE (id_alumno, id_medio_pago, id_marca_tarjeta, numero_tarjeta)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE alumno_tarjeta OWNER TO postgres;


CREATE SEQUENCE sq_id_estado_cuota
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_estado_cuota OWNER TO postgres;

CREATE TABLE estado_cuota
(
    id_estado_cuota integer DEFAULT nextval(('sq_id_estado_cuota')::regclass) NOT NULL,
    nombre character varying(80) NOT NULL,
    nombre_corto character varying(30) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_estado_cuota PRIMARY KEY (id_estado_cuota)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE estado_cuota OWNER TO postgres;


CREATE SEQUENCE sq_id_motivo_rechazo
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_motivo_rechazo OWNER TO postgres;

CREATE TABLE motivo_rechazo
(
    id_motivo_rechazo integer DEFAULT nextval(('sq_id_motivo_rechazo')::regclass) NOT NULL,
    medio_pago character(1),
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(50) NOT NULL,
    tipo_rechazo character(1) NOT NULL,
    codigo_rechazo integer NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_motivo_rechazo PRIMARY KEY (id_motivo_rechazo)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE motivo_rechazo OWNER TO postgres;


CREATE SEQUENCE sq_id_cargo_cuenta_corriente
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_cargo_cuenta_corriente OWNER TO postgres;

CREATE TABLE cargo_cuenta_corriente
(
    id_cargo_cuenta_corriente integer DEFAULT nextval(('sq_id_cargo_cuenta_corriente')::regclass) NOT NULL,
    nombre character varying(120) NOT NULL,
    nombre_corto character varying(50) NOT NULL,
    observaciones character varying(120),
    CONSTRAINT id_cargo_cuenta_corriente PRIMARY KEY (id_cargo_cuenta_corriente)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE cargo_cuenta_corriente OWNER TO postgres;


CREATE SEQUENCE sq_id_alumno_cc
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_alumno_cc OWNER TO postgres;

CREATE TABLE alumno_cuenta_corriente
(
    id_alumno_cc integer DEFAULT nextval(('sq_id_alumno_cc')::regclass) NOT NULL,
    id_alumno integer NOT NULL,
    id_cargo_cuenta_corriente integer NOT NULL,
    usuario_alta character varying(120),
    fecha_generacion_cc timestamp without time zone,
    cuota character(6) NOT NULL,
    descripcion character varying(120),
    importe numeric(15,2),
    CONSTRAINT id_alumno_cc PRIMARY KEY (id_alumno_cc),
    CONSTRAINT id_alumno FOREIGN KEY (id_alumno)
        REFERENCES alumno(id_alumno) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_cargo_cuenta_corriente FOREIGN KEY (id_cargo_cuenta_corriente)
        REFERENCES cargo_cuenta_corriente(id_cargo_cuenta_corriente) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE alumno_cuenta_corriente OWNER TO postgres;


CREATE SEQUENCE sq_id_transaccion_cc
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_transaccion_cc OWNER TO postgres;

ALTER TABLE transaccion_cuenta_corriente OWNER TO postgres;

CREATE TABLE transaccion_cuenta_corriente
(
    id_transaccion_cc integer DEFAULT nextval(('sq_id_transaccion_cc')::regclass) NOT NULL,
    id_alumno_cc integer NOT NULL,
    fecha_transaccion timestamp without time zone,
    id_estado_cuota integer NOT NULL,
    importe numeric(15,2),
    fecha_pago date,
    fecha_respuesta_prisma date,
    id_motivo_rechazo integer,
    usuario_ultima_modificacion character varying(120),
    fecha_ultima_modificacion timestamp without time zone,
    CONSTRAINT id_transaccion_cc PRIMARY KEY (id_transaccion_cc),
    CONSTRAINT id_alumno_cc FOREIGN KEY (id_alumno_cc)
        REFERENCES alumno_cuenta_corriente(id_alumno_cc) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_estado_cuota FOREIGN KEY (id_estado_cuota)
        REFERENCES estado_cuota(id_estado_cuota) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_motivo_rechazo FOREIGN KEY (id_motivo_rechazo)
        REFERENCES motivo_rechazo(id_motivo_rechazo) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );


CREATE SEQUENCE sq_id_liquidacion
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_liquidacion OWNER TO postgres;

CREATE TABLE liquidacion_encabezado
(
    id_liquidacion integer DEFAULT nextval(('sq_id_liquidacion')::regclass) NOT NULL,
    marca_tipo_tarjeta character(8) NOT NULL,
    fecha_presentacion_txt character(8) NOT NULL,
    hora_armado_txt character(4) NOT NULL,
    CONSTRAINT id_liquidacion PRIMARY KEY (id_liquidacion)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE liquidacion_encabezado OWNER TO postgres;


CREATE SEQUENCE sq_id_renglon
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_renglon OWNER TO postgres;

CREATE TABLE liquidacion_cuerpo
(
    id_liquidacion integer NOT NULL,
    id_renglon integer DEFAULT nextval(('sq_id_renglon')::regclass) NOT NULL,
    id_alumno_cc integer NOT NULL,
    numero_tarjeta character(16) NOT NULL,
    numero_factura character(8) NOT NULL,
    codigo_transaccion character(4) NOT NULL,
    importe numeric(15,2),
    codigo_alta character(1),
    CONSTRAINT id_liquidacion_cuerpo PRIMARY KEY (id_liquidacion, id_renglon),
    CONSTRAINT liquidacion_cuerpo_fk FOREIGN KEY (id_liquidacion)
        REFERENCES liquidacion_encabezado(id_liquidacion) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT id_alumno_cc FOREIGN KEY (id_alumno_cc)
        REFERENCES alumno_cuenta_corriente(id_alumno_cc) MATCH FULL
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE liquidacion_cuerpo OWNER TO postgres;


CREATE SEQUENCE sq_id_anio
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE sq_id_anio OWNER TO postgres;

CREATE TABLE anio
(
    id_anio integer DEFAULT nextval(('sq_id_anio')::regclass) NOT NULL,
    anio integer NOT NULL,
    CONSTRAINT id_anio PRIMARY KEY (id_anio)
)
    WITH (
        OIDS=FALSE
    );

ALTER TABLE anio OWNER TO postgres;

---
--- INSERT DE LAS TABLAS MAESTRAS
---
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('numero_establecimiento_debito_visa', 'Número de establecimiento para débito automático Visa', 'Num. establec. deb.', '0071227300', '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('numero_establecimiento_debito_mc', 'Número de establecimiento para débito automático MC', 'Num. establec. deb.', '0071227318', '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('importe_mensual_cuota', 'Importe mensual de la cuota', 'Importe cuota', 2500, '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('importe_inscripcion_anual', 'Importe de la inscripción anual', 'Importe inscripcion anual', 5000, '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('importe_inscripcion_inicial', 'Importe inscripción Nivel Incial', 'Criterio de cuota', 'ultima_generada', '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('importe_inscripcion_primario', 'Importe inscripción Nivel Primario', 'Criterio de cuota', 'ultima_generada', '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('criterio_generacion_cuota', 'Criterio de generación de cuota', 'Criterio de cuota', 'ultima_generada', '1.0.0');
INSERT INTO parametros_sistema (parametro, descripcion, desc_corta, valor, version_publicacion) VALUES ('importe_mensual_cuota_x_grado', 'Cobra valores de cuota mensual diferente por grado', 'Cobra valores diferentes x grado', 'NO', '1.0.0');
--Vamos a tener 3 opciones en este último parámetro, que serán: primer_cuota_adeudada, ultima_cuota_adeudada y total_adeudado

INSERT INTO nivel (id_nivel, nombre, nombre_corto, observaciones) VALUES (NEXTVAL('sq_id_nivel'), 'Nivel Inicial', 'NI', '');
INSERT INTO nivel (id_nivel, nombre, nombre_corto, observaciones) VALUES (NEXTVAL('sq_id_nivel'), 'Nivel Primario', 'NP', '');
INSERT INTO nivel (id_nivel, nombre, nombre_corto, observaciones) VALUES (NEXTVAL('sq_id_nivel'), 'Nivel Secundario', 'NS', '');


INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), 'Sala de 4', 'Sala 4', '', 1);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), 'Sala de 5', 'Sala 5', '', 1);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '1er grado', '1° grado', '', 2);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '2do grado', '2° grado', '', 2);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '3er grado', '3° grado', '', 2);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '4to grado', '4° grado', '', 2);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '5to grado', '5° grado', '', 2);
INSERT INTO grado (id_grado, nombre, nombre_corto, observaciones, id_nivel) VALUES (NEXTVAL('sq_id_grado'), '6to grado', '6° grado', '', 2);


INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Primario', 'PRI', 'Primario');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Secundario', 'SEC', 'Secundario');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Terciario', 'TER', 'Terciario');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Universitario', 'UNI', 'Universitario');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Maestría', 'MAE', 'Maestría');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Especialización', 'ESP', 'Especialización');
INSERT INTO estudio_alcanzado (id_estudio_alcanzado, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_estudio_alcanzado'), 'Doctorado', 'DOC', 'Doctorado');


INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Directores y gerentes', 'Dir. y Ger.', 'Directores y gerentes');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Profesionales científicos e intelectuales', 'Científicos', 'Profesionales científicos e intelectuales');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Técnicos y profesionales de nivel medio', 'Técnicos', 'Técnicos y profesionales de nivel medio');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Personal de apoyo administrativo', 'Personal apoyo admin.', 'Personal de apoyo administrativo');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Trabajadores de los servicios y vendedores de comercios y mercados', 'Vendedores de comercios', 'Trabajadores de los servicios y vendedores de comercios y mercados');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Agricultores y trabajadores calificados agropecuarios, forestales y pesqueros', 'Agro, forestales y pesqueros', 'Agricultores y trabajadores calificados agropecuarios, forestales y pesqueros');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Oficiales, operarios y artesanos de artes mecánicas y de otros oficios', 'Mecánicos', 'Oficiales, operarios y artesanos de artes mecánicas y de otros oficios');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Operadores de instalaciones y máquinas y ensambladores', 'Oper. máquinas', 'Operadores de instalaciones y máquinas y ensambladores');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Ocupaciones elementales', 'Elementales', 'Ocupaciones elementales');
INSERT INTO ocupacion (id_ocupacion, nombre, nombre_corto, observaciones)
VALUES(NEXTVAL('sq_id_ocupacion'), 'Ocupaciones militares', 'Militares', 'Ocupaciones militares');


INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Libreta Cívica', 'LC', 4, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Libreta de Enrolamiento', 'LE', 5, '','N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Pasaporte', 'PAS', 7, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Cédula de identidad', 'CI', 6, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Documento de dependencia', 'DEP', 8, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Clave única de identificación tributaria', 'CUIT', 3, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Clave única de identificación laboral', 'CUIL', 2, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Documento Nacional de Identidad', 'DNI', 1, '', 'N');
INSERT INTO tipo_documento (id_tipo_documento, nombre, nombre_corto, jerarquia, observaciones, extranjero)
VALUES(NEXTVAL('sq_id_tipo_documento'), 'Número de identificación fiscal', 'NIF', 9, '', 'S');


INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Padre', 'PA', 'Padre', '1');
INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Madre', 'MA', 'Madre', '2');
INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Hermano/a', 'HER', 'Hermano/a', '3');
INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Tío/a', 'T', 'Tío/a', '4');
INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Primo', 'PR', 'Primo', '5');
INSERT INTO tipo_allegado (id_tipo_allegado, nombre, nombre_corto, observaciones, jerarquia)
VALUES(NEXTVAL('sq_id_tipo_allegado'), 'Vecino', 'V', 'Vecino', '6');


INSERT INTO sexo (id_sexo, nombre, nombre_corto)
VALUES(NEXTVAL('sq_id_sexo'), 'Masculino', 'M');
INSERT INTO sexo (id_sexo, nombre, nombre_corto)
VALUES(NEXTVAL('sq_id_sexo'), 'Femenino', 'F');
INSERT INTO sexo (id_sexo, nombre, nombre_corto)
VALUES(NEXTVAL('sq_id_sexo'), 'No binario', 'NB');


INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Estados Unidos', 'us');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Canadá', 'el');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Puerto Rico', 'pr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'República Dominicana', 'rd');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Rusia', 'ru');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Egipto', 'eg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Sudáfrica', 'sa');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Grecia', 'gr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Holanda', 'nl');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bélgica', 'be');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Francia', 'fr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'España', 'es');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Hungría', 'hu');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Italia', 'it');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Rumania', 'ro');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Suiza', 'ch');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Austria', 'at');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Reino Unido', 'gb');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Dinarmarca', 'dk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Suecia', 'se');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Noruega', 'no');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Polonia', 'pl');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Alemania', 'dw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Perú', 'pe');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'México', 'mx');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Cuba', 'cu');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Argentina', 'ar');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Brasil', 'br');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Chile', 'cl');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Colombia', 'co');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Venezuela', 've');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Malasia', 'my');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Australia', 'au');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Indonesia', 'id');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Filipinas', 'ph');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nueva Zelanda', 'nz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Singapur', 'sg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tailandia', 'th');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Japón', 'jp');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Corea del Sur', 'kr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Vietnam', 'vn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'China', 'cn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Turquía', 'tr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'India', 'in');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Pakistán', 'pk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Afganistán', 'af');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Sri Lanka', 'lk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Myanmar (ex Birmania)', 'mm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Irán', 'ir');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Marruecos', 'ma');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Argelia', 'dz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Túnez', 'tn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Libia', 'ly');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Gambia', 'gm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Senegal', 'sn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Mauritania', 'mr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Malí', 'ml');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Costa de Marfil', 'ci');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Burkina Faso', 'bf');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Níger', 'ne');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Benin', 'bj');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Mauricio', 'mu');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Liberia', 'lr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Sierra Leona', 'sl');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Ghana', 'gh');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nigeria', 'ng');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Chad', 'td');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'República Centroafricana', 'cf');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Camerún', 'cm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Cabo Verde', 'cv');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Santo Tomé y Príncipe', 'st');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Gabón', 'ga');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Congo', 'cg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'República Democrática del Congo', 'zr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Angola', 'ao');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Sudán', 'sd');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Ruanda', 'rw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Somalia', 'so');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Kenia', 'ke');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tanzania', 'tz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Uganda', 'ug');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Burundi', 'bi');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Mozambique', 'mz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Zambia', 'zm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Madagascar', 'md');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Reunión', 're');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Zimbabwe', 'zw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Namibia', 'na');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Malawi', 'mw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Lesotho', 'ls');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Botswana', 'bw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Swazilandia', 'sz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Comoras', 'km');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Santa Helena', 'sh');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Eritrea', 'er');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Portugal', 'pt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Luxemburgo', 'lu');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Irlanda', 'ie');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Islandia', 'is');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Albania', 'al');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Malta', 'mt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Chipre', 'cy');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Finlandia', 'fi');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bulgaria', 'bg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Lituania', 'lt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Letonia', 'lv');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Estonia', 'ee');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Moldova', 'md');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Armenia', 'am');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bielorrusia', 'by');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Andorra', 'ad');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Mónaco', 'mc');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'San Marino', 'sm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Ciudad del Vatanico', 'va');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Ucrania', 'ua');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Serbia', 'rs');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Croacia', 'hr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Eslovenia', 'si');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bosnia y Herzegovina', 'ba');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Macedonia', 'mk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'República Checa', 'cz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Eslovaquia', 'sk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Liechtenstein', 'li');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Belice', 'bz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guatemala', 'gt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'El Salvador', 'sv');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Honduras', 'hn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nicaragua', 'ni');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Costa Rica', 'cr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Panamá', 'pa');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Haití', 'ht');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bolivia', 'bo');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guyana', 'gy');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Ecuador', 'ec');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Macao', 'mo');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guayana Francesa', 'gf');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Paraguay', 'py');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Martinica', 'mq');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Surinam', 'sr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Uruguay', 'uy');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Antillas Holandesas', 'an');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Isla Norfolk', 'nf');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Brunei', 'bn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nauru', 'nr');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Papua Nueva Guinea', 'pg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tonga', 'to');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Islas Salomón', 'sb');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Vanuatu', 'vt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Fidji', 'fj');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Palau', 'pw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Samoa Occidental', 'ws');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Kiribati', 'ki');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nueva Caledonia', 'nc');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tuvalu', 'tv');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Polinesia Francesa', 'pf');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tokelau', 'tk');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Estados Federados de Micronesia', 'fm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Islas Marshall', 'mh');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Corea del Norte', 'kp');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Laos', 'la');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bangladesh', 'bd');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Taiwan', 'tw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Maldivas', 'mv');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Líbano', 'lb');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Jordania', 'jo');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Siria', 'sy');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Irak', 'iq');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Kuwait', 'kw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Arabia Saudita', 'sa');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Yemen R.A.', 'ye');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Omán', 'om');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Emiratos Arabes Unidos', 'ae');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Israel', 'il');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bahrein', 'bh');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Qatar', 'qa');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bhután', 'bt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Mongolia', 'mn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Nepal', 'np');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Turkmenistán', 'tm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Azerbaiyán', 'az');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Georgia', 'ge');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Uzbekistán', 'uz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Jamaica', 'jm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guinea', 'gn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guinea Ecuatorial', 'gq');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Guinea-Bissau', 'gw');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Trinidad y Tobago', 'tt');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Antigua y Barbuda', 'ag');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Bahamas', 'bs');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Barbados', 'bb');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Dominica', 'dm');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Granada', 'gd');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'San Cristóbal y Nevis', 'kn');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'San Vicente y Granadinas', 'vc');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Santa Lucía', 'lc');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Tadjikistán', 'tj');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Kirguistán', 'kg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Kazajstán', 'kz');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Timor Oriental', 'tl');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Montenegro', 'me');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Djibouti', 'dj');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Etiopía', 'et');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Seychelles', 'sc');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Togo', 'tg');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Camboya', 'kh');
INSERT INTO pais (id_pais, nombre, nombre_corto) VALUES (nextval('sq_id_pais'), 'Sudán del Sur', 'ss');


INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Ciudad Autónoma de Buenos Aires', 'CABA', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Buenos Aires', 'BA', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Catamarca', 'CAT', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Córdoba', 'CORD', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Corrientes', 'CORR', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Chaco', 'CHA', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Chubut', 'CHU', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Entre Ríos', 'ER', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Formosa', 'FOR', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Jujuy', 'JU', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'La Pampa', 'LP', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'La Rioja', 'LR', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Mendoza', 'MEN', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Misiones', 'MIS', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Neuquén', 'NEU', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Río Negro', 'RN', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Salta', 'SAL', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'San Juan', 'SJ', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'San Luis', 'SL', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Santa Cruz', 'SC', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Santa Fe', 'SF', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Santiago del Estero', 'SG', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Tucumán', 'TUC', 1);
INSERT INTO provincia (id_provincia, nombre, nombre_corto, id_pais) VALUES (nextval('sq_id_provincia'), 'Tierra del Fuego, Antártida e Islas del Atlántico Sur', 'TF', 1);


INSERT INTO localidad (id_localidad, nombre, nombre_corto, id_provincia) VALUES (nextval('sq_id_localidad'), 'Concordia', 'Concordia', 1);
INSERT INTO localidad (id_localidad, nombre, nombre_corto, id_provincia) VALUES (nextval('sq_id_localidad'), 'Colonia Ayui', 'Col. Ayuí', 1);
INSERT INTO localidad (id_localidad, nombre, nombre_corto, id_provincia) VALUES (nextval('sq_id_localidad'), 'Villa Zorraquín', 'Zorraquín', 1);


INSERT INTO nacionalidad (id_nacionalidad, nombre, nombre_corto) VALUES (nextval('sq_id_nacionalidad'), 'Argentino', 'AR');
INSERT INTO nacionalidad (id_nacionalidad, nombre, nombre_corto) VALUES (nextval('sq_id_nacionalidad'), 'Uruguayo', 'UR');
INSERT INTO nacionalidad (id_nacionalidad, nombre, nombre_corto) VALUES (nextval('sq_id_nacionalidad'), 'Paraguayo', 'PA');
INSERT INTO nacionalidad (id_nacionalidad, nombre, nombre_corto) VALUES (nextval('sq_id_nacionalidad'), 'Brazileño', 'BZ');
INSERT INTO nacionalidad (id_nacionalidad, nombre, nombre_corto) VALUES (nextval('sq_id_nacionalidad'), 'Chileno', 'CH');


INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Efectivo', 'Efectivo', '', 1);
INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Transferencia', 'Transferencia', '', 2);
INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Tarjeta de Débito', 'Débito', '', 3);
INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Tarjeta de Crédito', 'Crédito', '', 4);
INSERT INTO medio_pago (id_medio_pago, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_medio_pago'), 'Postnet', 'Postnet', '', 5);


INSERT INTO marca_tarjeta (id_marca_tarjeta, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_marca_tarjeta'), 'Visa', 'Visa', '', 1);
INSERT INTO marca_tarjeta (id_marca_tarjeta, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_marca_tarjeta'), 'MasterCard', 'MasterCard', '', 2);
INSERT INTO marca_tarjeta (id_marca_tarjeta, nombre, nombre_corto, observaciones, jerarquia) VALUES (nextval('sq_id_marca_tarjeta'), 'Cabal', 'Cabal', '', 3);


INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Nación Argentina', 'BNA', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Entre Ríos', 'Bersa', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Galicia', 'Galicia', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Credicoop', 'Credicoop', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Santander', 'Santander', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Hipotecario', 'Hipotecario', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Patagonia', 'Patagonia', '');
INSERT INTO entidad_bancaria (id_entidad_bancaria, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_entidad_bancaria'), 'Banco Francés', 'BBVA', '');

INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'A', 'El socio no tiene saldo en su cuenta al momento de efectuar el débito', 'Cuenta sin disponible', 'T', 79,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'A', 'La tarjeta no es válida para operar en este comercio', 'Tarj. no es de grupo cerrado', 'P', 68,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'A', 'La tarjeta no está habilitada al momento de efectuar el débito', 'Tarjeta no operativa', 'T', 55,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'A', 'El número de tarjeta es inexistente', 'Nro. de tarjeta incorrecto', 'P', 51,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta se encuentra bloqueada por la entidad', 'Tarjeta no operativa', 'P', 55,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta se encuentra bloqueada por la entidad', 'Tarjeta no operativa por la entidad', 'T', 99,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El establecimiento debe completar el ?Código de alta de identificador? en el TXT de presentación', 'Debe informar identificador', 'P', 95,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta se puede usar solo en e- commerce', 'E-commerce no válido', 'P', 93,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El socio solicitó no abonar el débito de su resumen actual', 'Stop-debit solicitado', 'T', 92,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El socio solicitó la baja del débito automático', 'Baja de deb. aut. solicitada', 'P', 91,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La marca de la tarjeta es diferente a la del establecimiento', 'Operación m. priv. errónea', 'P', 78,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El tipo de tarjeta no puede operar en este establecimiento', 'Tarj. práctica /vcash /g.rural', 'P', 76,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El emisor rechaza el pedido de autorización sin especificar el motivo', 'Deb.aut. s/tarj. Emisor extran', 'T', 71,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El importe informado es erróneo', 'Importe de venta inválido', 'P', 64,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El emisor rechaza el pedido de autorización sin especificar el motivo', 'Autorización inválida', 'T', 63,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta no está habilitada al momento de efectuar el pedido de autorización', 'Deb. automático tarjeta inhabi', 'T', 60,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta está vencida al momento de efectuar el pedido de autorización', 'Tarjeta vencida', 'P', 56,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta se encuentra bloqueada por la entidad', 'Tarjeta con inconvenientes', 'P', 37,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'La tarjeta se dio de baja sin reposición al momento de efectuar el débito', 'Tarjeta dada de baja', 'P', 54,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El número de tarjeta informado es incorrecto', 'Tarjeta no es de Visa', 'P', 52,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El importe del resumen inválido', 'Importe de venta inválido', 'P', 64,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'El emisor rechaza el pedido de autorización sin especificar el motivo', 'Autorización inválida', 'T', 63,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'C', 'No se registra consumo asociado a la devolución informada', 'Consumo original inexistente', 'P', 88,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'El emisor rechaza el pedido de autorización sin especificar el motivo', 'Rechazada por el emisor de la tarjeta', 'T', 20,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'La tarjeta no está habilitada para operar al momento de efectuar el pedido de autorización', 'Tipo de consumo no permitido', 'T', 96,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'La tarjeta enviada en el archivo DEBLIQC es de débito. Debe enviarse en el archivo DEBLIQD', 'Tarjeta Electrón débito autom', 'P', 86,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'Tarjeta perdida', 'La tarjeta se encuentra bloqueada por la entidad', 'P', 25,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'Tarjeta c/denuncia de robo', 'La tarjeta se encuentra bloqueada por la entidad', 'P', 26,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'El número de tarjeta es inexistente', 'Tarjeta no registrada', 'P', 22,'');
INSERT INTO motivo_rechazo (id_motivo_rechazo, medio_pago, nombre, nombre_corto, tipo_rechazo, codigo_rechazo, observaciones)
VALUES (nextval('sq_id_motivo_rechazo'), 'D', 'El número de tarjeta es inexistente o está en estado CERRADA', 'Tarjeta inexistente', 'T', 40,'');


INSERT INTO estado_cuota (id_estado_cuota, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_estado_cuota'), 'Generada', 'Generada', 'La cuota del mes se encuentra generada');
INSERT INTO estado_cuota (id_estado_cuota, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_estado_cuota'), 'Liquidada', 'Liquidada', 'La couta se envió al banco para su cobro');
INSERT INTO estado_cuota (id_estado_cuota, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_estado_cuota'), 'Aprobada', 'Aprobada', 'La couta se encuentra paga');
INSERT INTO estado_cuota (id_estado_cuota, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_estado_cuota'), 'Rechazada', 'Rechazada', 'La cuota se encuentra rechazada');


INSERT INTO motivo_desercion (id_motivo_desercion, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_motivo_desercion'), 'Egresó', 'Egresó', '');
INSERT INTO motivo_desercion (id_motivo_desercion, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_motivo_desercion'), 'Abandonó', 'Abandonó', '');
INSERT INTO motivo_desercion (id_motivo_desercion, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_motivo_desercion'), 'Fallecido', 'Fallecido', '');
INSERT INTO motivo_desercion (id_motivo_desercion, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_motivo_desercion'), 'Pase', 'Pase', '');
INSERT INTO motivo_desercion (id_motivo_desercion, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_motivo_desercion'), 'Falta de pago', 'Falta de pago', '');

INSERT INTO cargo_cuenta_corriente (id_cargo_cuenta_corriente, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_cargo_cuenta_corriente'), 'Inscripción Anual', 'Insc. Anual', 'Inscripción Anual');
INSERT INTO cargo_cuenta_corriente (id_cargo_cuenta_corriente, nombre, nombre_corto, observaciones) VALUES (nextval('sq_id_cargo_cuenta_corriente'), 'Cuota Mensual', 'Cuota Mensual', 'Cuota Mensual');

INSERT INTO anio (id_anio, anio) VALUES (nextval('sq_id_anio'), '2021');
INSERT INTO anio (id_anio, anio) VALUES (nextval('sq_id_anio'), '2022');