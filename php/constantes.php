<?php

class constantes
{
    /*
        Para definir una constante se agregara una entrada al arreglo, con el siguiente formato:
                'nombre' => array('valor' => numero/caracter , 'tabla' => nombretabla)
    */

    protected static $lista_constantes = array(
        'TIPO_DATO_INT' => array('valor' => 'INT'),
        'TIPO_DATO_STR' => array('valor' => 'STR'),
        'TIPO_DATO_FLOAT' => array('valor' => 'FLT'),
        'INSCRIPCION_ANUAL' => array('valor' => 1, 'tabla' => 'cargo_cuenta_corriente'),
        'CUOTA_MENSUAL' => array('valor' => 2, 'tabla' => 'cargo_cuenta_corriente'),
        'MATERIALES' => array('valor' => 3, 'tabla' => 'cargo_cuenta_corriente'),
        'NIVEL_INICIAL' => array('valor' => 1, 'tabla' => 'nivel'),
        'NIVEL_PRIMARIO' => array('valor' => 2, 'tabla' => 'nivel'),
        'NIVEL_SECUNDARIO' => array('valor' => 3, 'tabla' => 'nivel'),
        'SALA4' => array('valor' => 1, 'tabla' => 'grado'),
        'SALA5' => array('valor' => 2, 'tabla' => 'grado'),
        'PRIMER_GRADO' => array('valor' => 3, 'tabla' => 'grado'),
        'SEGUNDO_GRADO' => array('valor' => 4, 'tabla' => 'grado'),
        'TERCER_GRADO' => array('valor' => 5, 'tabla' => 'grado'),
        'CUARTO_GRADO' => array('valor' => 6, 'tabla' => 'grado'),
        'QUINTO_GRADO' => array('valor' => 7, 'tabla' => 'grado'),
        'SEXTO_GRADO' => array('valor' => 8, 'tabla' => 'grado'),
        'NO_INSCRIPCION' => array('valor' => 1, 'tabla' => 'notificacion'),
    );

    //Devuelve el valor de la constante indicada como parametro, si no se encuentra devuelve null
    public static function get_valor_constante($nombre_constante)
    {
        $valor = null;
        if (isset(self::$lista_constantes[$nombre_constante]['valor'])) {
            $valor = self::$lista_constantes[$nombre_constante]['valor'];
        } else {
            toba::logger()->info(" OJO!!! La constante $nombre_constante no se encuentra declarada !!!!");
        }

        return $valor;
    }

    //Devuelve el nombre de la tabla que referencia la constante indicada, si no se encuentra devuelve null
    public static function get_tabla_origen_de_constante($nombre_constante)
    {
        $tabla = null;
        if (isset(self::$lista_constantes[$nombre_constante]['tabla'])) {
            $tabla = self::$lista_constantes[$nombre_constante]['tabla'];
        }

        return $tabla;
    }

    //Devuelve un booleano que indica si la constante referencia un valor en una tabla.
    public static function es_dato_de_tabla($nombre_constante)
    {
        return (isset(self::$lista_constantes[$nombre_constante]['tabla']));
    }
}
