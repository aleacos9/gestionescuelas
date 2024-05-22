--Alejandro feature/alta-manual-pagos 10/03/2022
DROP FUNCTION IF EXISTS alta_manual_pagos();

--Alejandro feature/alta-manual-pagos 25/03/2022
DROP FUNCTION IF EXISTS cambios_tablas_archivo_respuesta();

--Alejandro feature/alta-manual-pagos 04/04/2022
DROP FUNCTION IF EXISTS cambios_tablas_parametros_sistema();

--Alejandro feature/alta-manual-pagos 05/04/2022
DROP FUNCTION IF EXISTS alta_nuevo_parametro_sistema();

--Alejandro feature/alta-manual-pagos 06/04/2022
DROP FUNCTION IF EXISTS spAfectacionArchivoDebito();

--Alejandro feature/alta-manual-pagos 22/04/2022
DROP FUNCTION IF EXISTS alta_nuevos_parametros_sistema();

--Alejandro feature/alta-manual-pagos 25/04/2022
DROP FUNCTION IF EXISTS cambios_tabla_transaccion_cuenta_corriente();

--Alejandro feature/alta-manual-pagos 25/04/2022
DROP FUNCTION IF EXISTS alta_parametro_sistema_ingresa_importe_en_generacion_cargos();

--Alejandro feature/alta-manual-pagos 18/10/2022
DROP FUNCTION IF EXISTS altas_parametros_sistema();

--Alejandro feature/alta-manual-pagos 05/05/2022
DROP FUNCTION IF EXISTS cambio_campos_archivo_respuesta();

--Alejandro feature/alta-manual-pagos 13/05/2022
DROP FUNCTION IF EXISTS agregar_campos_transaccion_cuenta_corriente();

--Alejandro feature/alta-manual-pagos 14/07/2022
DROP FUNCTION IF EXISTS actualizar_usuario_a_tutores();

--Alejandro feature/alta-manual-pagos 13/10/2022
DROP FUNCTION IF EXISTS insertar_jerarquia_grado();

--Alejandro feature/alta-manual-pagos 18/10/2022
DROP FUNCTION IF EXISTS insertar_campos_alumno();

--Alejandro feature/alta-manual-pagos 18/10/2022
DROP FUNCTION IF EXISTS altas_parametros_sistema();

--Alejandro feature/alta-manual-pagos 31/10/2022
DROP FUNCTION IF EXISTS cambios_tabla_alumnos_dato_cursada();

--Alejandro feature/alta-manual-pagos 01/11/2022
DROP FUNCTION IF EXISTS actualizar_anio_cursada_alumnos();

--Alejandro feature/alta-manual-pagos 13/03/2023
DROP FUNCTION IF EXISTS altas_parametros_sistema_generar_comprobante_AFIP();

--Alejandro feature/alta-manual-pagos 14/03/2023
DROP FUNCTION IF EXISTS alta_columna_estado_tabla_anio();

--Alejandro feature/alta-manual-pagos 20/03/2023
DROP FUNCTION IF EXISTS alta_parametros_fijos_para_comprobante_electronico();

--Alejandro feature/actualizar-en-perfil-tutor-valor-cuota-adeudada 27/07/2023
DROP FUNCTION IF EXISTS actualizar_en_perfil_tutor_valor_cuota_adeudada();

--Alejandro feature/agregar-concepto-para-generar-cargos 22/08/2023
DROP FUNCTION IF EXISTS agregar_concepto_para_generar_cargos();

--Alejandro feature/no-permitir-cargar-pago-reinscripcion-si-adeuda-cuotas 20/09/2023
DROP FUNCTION IF EXISTS impedir_pago_reinscripcion_si_adeuda_cuotas();

--Alejandro feature/listar-solo-allegados-sin-usuarios-desde-alta-de-usuarios 26/09/2023
DROP FUNCTION IF EXISTS listar_solo_allegados_sin_usuarios();

--Alejandro feature/agregar-mensaje-popup-usuario-perfil-tutor 03/10/2023
DROP FUNCTION IF EXISTS agregar_mensaje_popup_usuario_perfil_tutor();

--Alejandro feature/listar-solo-allegados-sin-usuarios-desde-alta-de-usuarios 04/10/2023
DROP FUNCTION IF EXISTS alta_parametro_definir_fecha_desde_listado_cuenta_corriente();

--Alejandro feature/permitir-pago-inscripcion-en-cuotas 02/11/2023
DROP FUNCTION IF EXISTS permitir_pago_inscripcion_en_cuotas();

--Alejandro feature/generacion-de-comprobantes-afip-desde-el-alta-de-pagos 23/02/2024
DROP FUNCTION IF EXISTS generacion_de_comprobantes_afip_desde_alta_pagos();

--Alejandro feature/generacion-de-comprobantes-afip-desde-el-alta-de-pagos 23/02/2024
DROP FUNCTION IF EXISTS correcciones_varias_perfil_administrativo();