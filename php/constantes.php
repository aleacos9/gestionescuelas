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
