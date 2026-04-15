<?php
class ci_abm_anios extends gestionescuelas_ext_ci
{
    protected $s__id_anio;

    //---- cuadro ----------------------------------------------------------------------

    function conf__cuadro($cuadro)
    {
        $datos = dao_consultas::get_anios();
        $cuadro->set_datos($datos);
    }

    function evt__cuadro__seleccion($seleccion)
    {
        $this->s__id_anio = $seleccion['id_anio'];
    }

    //---- formulario -------------------------------------------------------------------

    function conf__formulario($form)
    {
        if (isset($this->s__id_anio)) {
            $sql = "SELECT id_anio, anio, estado 
                    FROM anio 
                    WHERE id_anio = " . toba::db()->quote($this->s__id_anio);

            $datos = toba::db()->consultar_fila($sql);

            $form->set_datos($datos);
        }
    }

    function evt__formulario__alta($datos)
    {
        // Validación: no duplicar año
        $existe = toba::db()->consultar_fila("
                    SELECT 1 
                    FROM anio 
                    WHERE anio = " . toba::db()->quote($datos['anio'])
                    );

        if ($existe) {
            throw new toba_error('El año ya existe');
        }

        /// Validación: solo un año activo
        if ($datos['estado'] == 'A') {
            $activo = toba::db()->consultar_fila("
                    SELECT 1 
                    FROM anio 
                    WHERE estado = 'A'
                    ");

            if ($activo) {
                throw new toba_error('Ya existe un año activo');
            }
        }

        $sql = "INSERT INTO anio (anio, estado)
        VALUES (" . toba::db()->quote($datos['anio']) . ",
                " . toba::db()->quote($datos['estado']) . ")";

        toba::db()->ejecutar($sql);

        toba::notificacion()->agregar('Año creado correctamente', 'info');

    }

    function evt__formulario__baja()
    {
        if (isset($this->s__id_anio)) {

            // Validación: no permitir borrar el año activo
            $existe = toba::db()->consultar_fila("
            SELECT 1 
            FROM anio 
            WHERE id_anio = " . toba::db()->quote($this->s__id_anio) . "
            AND estado = 'A'
        ");

            if ($existe) {
                throw new toba_error('No se puede eliminar el año activo');
            }

            $sql = "DELETE FROM anio 
                WHERE id_anio = " . toba::db()->quote($this->s__id_anio);

            toba::db()->ejecutar($sql);

            unset($this->s__id_anio);

            toba::notificacion()->agregar('Año eliminado correctamente', 'info');
        }
    }

    function evt__formulario__modificacion($datos)
    {
        if (isset($this->s__id_anio)) {

            // Validación: no duplicar año (excluyendo el actual)
            $existe = toba::db()->consultar_fila("
                        SELECT 1 
                        FROM anio 
                        WHERE anio = " . toba::db()->quote($datos['anio']) . "
                        AND id_anio <> " . toba::db()->quote($this->s__id_anio)
                    );

            if ($existe) {
                throw new toba_error('El año ya existe');
            }

            // Validación: solo un año activo (excluyendo el actual)
            if ($datos['estado'] == 'A') {
                $activo = toba::db()->consultar_fila("
                            SELECT 1 
                            FROM anio 
                            WHERE estado = 'A'
                            AND id_anio <> " . toba::db()->quote($this->s__id_anio)
                        );

                if ($activo) {
                    throw new toba_error('Ya existe un año activo');
                }
            }

            $sql = "UPDATE anio 
            SET anio = " . toba::db()->quote($datos['anio']) . ",
                estado = " . toba::db()->quote($datos['estado']) . "
            WHERE id_anio = " . toba::db()->quote($this->s__id_anio);

            toba::db()->ejecutar($sql);

            toba::notificacion()->agregar('Año modificado correctamente', 'info');
        }
        unset($this->s__id_anio);
    }

    function evt__formulario__cancelar()
    {
        unset($this->s__id_anio);
    }
}