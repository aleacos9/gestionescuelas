<?php

class conversion_tipo_datos
{
    //conversion_tipo_datos::convertir_null_a_cadena($variable, $tipo);
    public static function convertir_null_a_cadena($variable, $tipo)
    {
        if (($variable === null) or (trim($variable) == '')) {
            $variable = 'null';
        } else {
            if ($tipo == constantes::get_valor_constante('TIPO_DATO_INT')) {
                $variable = $variable;
            } elseif ($tipo == constantes::get_valor_constante('TIPO_DATO_STR')) {
                $variable = "'$variable'";
            } elseif ($tipo == constantes::get_valor_constante('TIPO_DATO_FLOAT')) {
                $variable = "'$variable'";
            }
        }
        return $variable;
    }

    /*
    *	Funcion que concatena un 0 decimal cuando es necesario, esto es cuando el nro tiene decimales
    *	multiplos de 10, a excepcion del 00.
    */
    public static function agregar_decimal_significativo($total)
    {
        $nro = ($total * 100) - 1;
        $nro = fmod($nro, 100);
        $cond = (trim($nro) == '9');								//Chequeo si es un nro de un solo digito
        for ($i = 1; (($i < 9) and (!$cond)); $i++) {				//Testeo si tiene la forma x9 para x = [0..8]
            if (trim($nro) == ($i.'9')) {							//Uso trim para que tengan el mismo tipo.
                    $cond = true;
            }//if
        }//fe

        if ($cond) {									//El ultimo decimal era un cero que se trunco de la mantisa.
                $total .= '0';						//Hay que agregarlo a la derecha ya que es significativo.
        }
        return $total;
    }
}
