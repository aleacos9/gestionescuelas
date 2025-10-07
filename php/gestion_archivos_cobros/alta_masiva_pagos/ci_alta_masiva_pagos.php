<?php
class ci_alta_masiva_pagos extends ci_alta_manual_pagos
{
	protected $s__datos_alta_masiva_pagos;

    //---- formulario -------------------------------------------------------------------

	function conf__formulario($form)
	{
	}

    function evt__formulario__modificacion($datos)
    {
        $fecha = new fecha();
        $hoy = $fecha->get_timestamp_db();
        $usuario = toba::usuario()->get_id();

        $mes_original = $datos['cuota'];
        $datos['cuota'] = str_pad($mes_original, 2, '0', STR_PAD_LEFT);
        $datos['usuario_ultima_modificacion'] = $usuario;
        $datos['fecha_ultima_modificacion'] = $hoy;
        $datos['fecha_transaccion'] = $hoy;
        $datos['mostrar_mensaje_individual'] = false;
        $datos['modo'] = "alta_masiva";

        if (!empty($datos['archivo'])) {
            $this->procesar_archivo($datos['archivo'], $datos['anio'], $mes_original);
            // después de validar, guardo cuota con año
            $datos['cuota'] = $datos['cuota'] . $datos['anio'];
        }

        $this->s__datos_alta_masiva_pagos = $datos;
    }

    private function procesar_archivo($archivo, $anio_formulario, $mes_original)
    {
        $nombre_archivo = basename($archivo['name']);
        $ruta_tmp = $archivo['tmp_name'];

        $this->validar_nombre_archivo($nombre_archivo, $anio_formulario, $mes_original);
        $this->validar_extension_y_tamano($archivo, $nombre_archivo);
        $this->verificar_archivo_existente($nombre_archivo);

        $ruta_destino = $this->mover_archivo($archivo, $nombre_archivo, $ruta_tmp);
        $this->ejecutar_procedure($ruta_destino);

        $this->mostrar_resumen($nombre_archivo);
    }

    /**
     * Valida que el nombre del archivo contenga el año y mes seleccionados en el formulario.
     *
     * @param string $nombre_archivo Nombre original del archivo subido.
     * @param int    $anio           Año ingresado en el formulario.
     * @param int    $mes_original   Mes ingresado en el formulario (sin formatear).
     *
     * @throws toba_error Si el formato del nombre no es válido o si no coincide con el año/mes esperado.
     */
    private function validar_nombre_archivo($nombre_archivo, $anio_formulario, $mes_original)
    {
        if (preg_match('/_(\d{6})\d*/', $nombre_archivo, $matches)) {
            $archivo_anio_mes = $matches[1]; // ej: 202509
            $mes_formulario = str_pad($mes_original, 2, '0', STR_PAD_LEFT);
            if ($archivo_anio_mes !== $anio_formulario . $mes_formulario) {
                throw new toba_error(
                    "El archivo {$nombre_archivo} no coincide con el mes y año seleccionados ({$anio_formulario}{$mes_formulario})."
                );
            }
        } else {
            throw new toba_error("El nombre del archivo {$nombre_archivo} no tiene el formato esperado con año y mes (YYYYMM...).");
        }
    }

    /**
     * Valida la extensión y el tamaño del archivo subido.
     *
     * @param string $nombre_archivo Nombre del archivo.
     * @param int    $size           Tamaño en bytes del archivo.
     *
     * @throws toba_error Si la extensión no es .txt o si supera los 5 MB.
     */
    private function validar_extension_y_tamano($archivo, $nombre_archivo)
    {
        if (strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION)) !== 'txt') {
            throw new toba_error("Solo se permiten archivos con extensión .txt");
        }
        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new toba_error("El archivo es demasiado grande (máximo 5MB).");
        }
    }

    /**
     * Verifica si el archivo ya fue importado previamente, buscando en la tabla archivo_respuesta.
     *
     * @param string $nombre_archivo Nombre del archivo subido.
     */
    private function verificar_archivo_existente($nombre_archivo)
    {
        $nombre_sin_ext = pathinfo($nombre_archivo, PATHINFO_FILENAME);
        $nombre_archivo_sql = str_replace("'", "''", $nombre_sin_ext);
        $sql_check = "SELECT 1 FROM archivo_respuesta WHERE nombre_archivo = '{$nombre_archivo_sql}'";
        $existe = toba::db()->consultar($sql_check);
        if (!empty($existe)) {
            toba::notificacion()->agregar("El archivo {$nombre_archivo} ya fue importado previamente.", 'info');
        }
    }

    /**
     * Mueve el archivo subido desde la carpeta temporal a la carpeta compartida para la importación.
     *
     * @param string $ruta_tmp       Ruta temporal del archivo subido.
     * @param string $nombre_archivo Nombre del archivo.
     *
     * @return string Ruta final del archivo movido.
     *
     * @throws toba_error Si no se puede mover el archivo o si la carpeta no existe/no es escribible.
     */
    private function mover_archivo($archivo, $nombre_archivo, $ruta_tmp)
    {
        $dir = '/var/lib/postgresql/importaciones';
        if (!is_dir($dir)) {
            throw new toba_error("Directorio de importación inexistente: $dir");
        }
        if (!is_writable($dir)) {
            throw new toba_error("Directorio no escribible por PHP: $dir");
        }
        $ruta_destino = $dir . DIRECTORY_SEPARATOR . $nombre_archivo;
        if (!move_uploaded_file($ruta_tmp, $ruta_destino)) {
            toba::logger()->error("move_uploaded_file falló. tmp: {$ruta_tmp} destino: {$ruta_destino}");
            throw new toba_error("No se pudo guardar el archivo en $ruta_destino. Revisar permisos.");
        }
        toba::logger()->debug("Archivo movido a: " . $ruta_destino);
        return $ruta_destino;
    }

    /**
     * Ejecuta el procedimiento almacenado que procesa el archivo en la base de datos.
     *
     * @param string $ruta_destino Ruta completa del archivo en el servidor.
     *
     * @throws toba_error Si ocurre un error al ejecutar el procedimiento.
     */
    private function ejecutar_procedure($ruta_destino)
    {
        try {
            $escape_path = str_replace("'", "''", $ruta_destino);
            $sql = "SELECT spafectacionarchivodebitorutaarchivo('{$escape_path}')";
            toba::db()->ejecutar($sql);
        } catch (Exception $e) {
            toba::logger()->error("Error al ejecutar procedure: " . $e->getMessage());
            throw new toba_error("No se pudo procesar el archivo en la base. Verifique logs para más detalle.");
        }
    }

    /**
     * Obtiene un resumen del procesamiento del archivo (cantidad total y detalle por medio de pago).
     *
     * @param string $nombre_archivo Nombre del archivo subido.
     */
    private function mostrar_resumen($nombre_archivo)
    {
        $nombre_sin_ext = pathinfo($nombre_archivo, PATHINFO_FILENAME);
        $nombre_archivo_sql = str_replace("'", "''", $nombre_sin_ext);

        $sql_id = "SELECT id_archivo_respuesta
                   FROM archivo_respuesta
                   WHERE nombre_archivo = '{$nombre_archivo_sql}'
                   ORDER BY id_archivo_respuesta DESC
                   LIMIT 1
                  ";

        $ultimo_reg = toba::db()->consultar($sql_id)[0] ?? null;

        if ($ultimo_reg) {
            $ultimo = $ultimo_reg['id_archivo_respuesta'];

            $res_total = toba::db()->consultar(
                "SELECT COUNT(*) as cant
                 FROM archivo_respuesta_detalle 
                 WHERE id_archivo_respuesta = {$ultimo}
                "
            );
            $cant = (int) ($res_total[0]['cant'] ?? 0);

            $sql_resumen = "SELECT ar.id_medio_pago
                              ,COUNT(*) as cant
                              ,mp.nombre as medio_pago
                            FROM archivo_respuesta_detalle as ard
                                INNER JOIN archivo_respuesta ar ON ar.id_archivo_respuesta = ard.id_archivo_respuesta
                                INNER JOIN medio_pago mp on ar.id_medio_pago = mp.id_medio_pago
                            WHERE ard.id_archivo_respuesta = {$ultimo}
                            GROUP BY ar.id_medio_pago, mp.nombre
                           ";

            $resumen = toba::db()->consultar($sql_resumen);

            $detalle = "";
            foreach ($resumen as $fila) {
                $detalle .= "Medio {$fila['medio_pago']}: {$fila['cant']} registros<br/>";
            }

            toba::notificacion()->agregar(
                "Archivo {$nombre_archivo} procesado.<br/> Se importaron {$cant} registros.<br/> " . $detalle,
                'info'
            );
        } else {
            toba::notificacion()->agregar("Archivo {$nombre_archivo} procesado, sin registros para importar.", 'info');
        }
    }

    public function set_datos_cn()
    {
        $this->cn()->set_datos_pago($this->s__datos_alta_masiva_pagos);
    }
}