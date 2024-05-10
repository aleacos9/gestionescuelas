<?php

class fecha
{
    protected $timestamp;
    
    public function __construct()
    {
        if (!func_num_args()) {
            $this->timestamp = strtotime(date("Y-m-d H:i:s"));
        } else {
            list($arg) = func_get_args();
            $this->set_fecha($arg);
        }
    }
    
    //Metodos para setear la variable interna.
    public function set_fecha($fecha)
    {
        if (isset($fecha)) {
            $this->timestamp = strtotime($fecha);
        }
    }
    
    public function set_timestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    
    //Metodos para obtener una fecha desplazada en dias, meses o años. Se debe incluir el signo en el parametro.
    public function get_fecha_desplazada($dias)
    {
        $aux = strtotime("$dias day", $this->timestamp);
        return $aux;
    }
    
    public function get_fecha_desplazada_meses($meses)
    {
        $aux = strtotime("$meses month", $this->timestamp);
        return $aux;
    }
    
    public function get_fecha_desplazada_años($años)
    {
        $aux = strtotime("$años year", $this->timestamp);
        return $aux;
    }
    
    //Metodos de comparacion de fechas, siempre se compara contra la fecha cargada en la variable interna.
    public function es_menor_que($fecha2)
    {
        if ($this->get_diferencia($fecha2) > 0) {
            return true;
        }
            
        return false;
    }
    
    public function es_mayor_que($fecha2)
    {
        if ($this->get_diferencia($fecha2) < 0) {
            return true;
        }
            
        return false;
    }
    
    public function es_igual_que($fecha2)
    {
        if ($this->get_diferencia($fecha2) == 0) {
            return true;
        }
            
        return false;
    }
    
    //Metodos para obtener la fecha en distintos formatos, se utiliza para recuperar la fecha interna.
    public function get_timestamp_db()
    {
        $aux = date("Y-m-d H:i:s", $this->timestamp);
        return $aux;
    }
    
    public function get_fecha_db()
    {
        $aux = date("Y-m-d", $this->timestamp);
        return $aux;
    }
    
    public function get_timestamp_pantalla()
    {
        $aux = date("d/m/Y H:i:s", $this->timestamp);
        return $aux;
    }
        
    public function get_fecha_pantalla()
    {
        $aux = date("d/m/Y", $this->timestamp);
        return $aux;
    }

    //Metodos estaticos para convertir fechas
    public function convertir_fecha_a_timestamp($fecha)
    {
        $timestamp =  strtotime($fecha);
        $aux = date("Y-m-d H:i:s", $timestamp);
        return $aux;
    }
    
    public function convertir_timestamp_a_fecha($timestamp)
    {
        $aux = null;
        if (!is_null($timestamp)) {
            $aux = date("Y-m-d", strtotime($timestamp));
        }
        return $aux;
    }
    
    //Metodos para obtener la hora apartir de un timestamp
    public function convertir_timestamp_a_hora($timestamp)
    {
        return date("H:i", strtotime($timestamp));
    }
    
    //Metodo que devuelve si el dia es sabado o domingo.
    public function es_dia_habil()
    {
        $aux = $this->get_parte('dia_semana');		//0 es para Domingo y 6 es para Sabado
        if (($aux > '0') and ($aux < '6')) {
            return true;
        }
            
        return false;
    }
    
    //Metodo que devuelve una parte especifica de la fecha.
    public function get_parte($parte)
    {
        switch ($parte) {
            case 'dia':
                $parte_fecha = 'mday';
                break;
                
            case 'mes':
                $parte_fecha = 'mon';
                break;
                
            case 'año':
                $parte_fecha = 'year';
                break;

            case 'dia_semana':
                $parte_fecha = 'wday';
                break;
                                
            default:
                $parte_fecha = 'mday';

        } // switch
                
        $aux = $this->separar_fecha_en_partes();
        return ($aux[$parte_fecha]);
    }

    public function separar_fecha_en_partes()
    {
        return getdate($this->timestamp);
    }
    
    //Metodo que calcula la diferencia de dias entre dos fechas.
    public function cantidadDiasEntreDosFechas($fecha1, $fecha2, $hora_limite)
    {
        // Obtener dias
        $f_inicio = strtotime(substr($fecha1, 0, 10));
        $f_fin = strtotime(substr($fecha2, 0, 10));
        $diff = $f_fin - $f_inicio;
        $dias = round($diff / 86400) + 1;
        // Obtener datos del timestamp
        $fecha_desde = getdate(strtotime($fecha1));
        $fecha_hasta = getdate(strtotime($fecha2));
        // Descontar segun hora limite.
        if (strtotime($fecha_desde['hours'].':'.$fecha_desde['minutes']) > strtotime($hora_limite)) {
            $dias -= 0.5;
        }
        if (strtotime($fecha_hasta['hours'].':'.$fecha_hasta['minutes']) < strtotime($hora_limite)) {
            $dias -= 0.5;
        }
        return $dias;
    }
    
    private function get_diferencia($fecha2)
    {
        if (! is_null($fecha2)) {
            $timestamp2 = strtotime($fecha2);
            $diff_segs = $timestamp2 - $this->timestamp;
            if ($diff_segs < 0) {
                $resultado = ceil($diff_segs / 86400);
            } else {
                $resultado = floor($diff_segs / 86400);
            }
            
            return $resultado;
        }
    }
    
    public function get_nombre_mes($id_mes)
    {
        $meses = self::get_meses_anio();
        foreach (array_keys($meses) as $id) {
            if ($meses[$id]['id'] == $id_mes) {
                return $meses[$id]['mes'];
            }
        }
    }
    
    public function get_meses_anio()
    {
        //El dia que windows cumpla con el RFC 1766 esto va a funcar correctamente.
        /*$i = 0;
        $meses = array();
        setlocale(LC_TIME, "es-ES");
        $next_fecha = strtotime(date("Y-m-d H:i:s"));
        while ($i < 12){
                $mes_loco = strftime('%B-%m', $next_fecha);
                list($mes_letra, $mes_nro) = explode('-', $mes_loco);
                $meses[$mes_nro - 1] = array('id'=> $mes_nro, 'mes' => ucfirst($mes_letra));
                $next_fecha = strtotime("+1 month", $next_fecha);
                $i++;
        }//while		*/

        //Por ahora lo hacemos asi mas croto.
        $meses[0]['id'] = 1;
        $meses[0]['mes'] = "Enero";
        $meses[1]['id'] = 2;
        $meses[1]['mes'] = "Febrero";
        $meses[2]['id'] = 3;
        $meses[2]['mes'] = "Marzo";
        $meses[3]['id'] = 4;
        $meses[3]['mes'] = "Abril";
        $meses[4]['id'] = 5;
        $meses[4]['mes'] = "Mayo";
        $meses[5]['id'] = 6;
        $meses[5]['mes'] = "Junio";
        $meses[6]['id'] = 7;
        $meses[6]['mes'] = "Julio";
        $meses[7]['id'] = 8;
        $meses[7]['mes'] = "Agosto";
        $meses[8]['id'] = 9;
        $meses[8]['mes'] = "Septiembre";
        $meses[9]['id'] = 10;
        $meses[9]['mes'] = "Octubre";
        $meses[10]['id'] = 11;
        $meses[10]['mes'] = "Noviembre";
        $meses[11]['id'] = 12;
        $meses[11]['mes'] = "Diciembre";

        return $meses;
    }
    /**
     *
     * @param <type> $mes entra el mes como numero ej:2 para febrero
     * @param <type> $anio entra el anio ej:2005
     * @return <type> retorna el ultimo dia del mes en formato fecha, de la forma Y-M-D. ej: 2009-09-30.
     */
    public static function ultimo_dia_mes($mes, $anio)
    {
        $mes_nuevo = mktime(0, 0, 0, $mes, 1, $anio);
        $cant = date("t", $mes_nuevo);
        return date("Ymd", mktime(0, 0, 0, $mes, $cant, $anio));
    }

    public static function primer_dia_mes($mes, $anio)
    {
        return date("Ym01", mktime(0, 0, 0, $mes, 1, $anio));
    }

    // Metodo para convertir de esto 06/10/2010 a 06-10-2010
    public function convertir_fecha_pantalla_a_fecha_db($fecha)
    {
        $fecha_conv = str_replace("/", "-", $fecha);
        $aux = substr($fecha_conv, 6, 4)."-".substr($fecha_conv, 3, 2)."-".substr($fecha_conv, 0, 2);
        return $aux;
    }
    
    public static function formatear_para_pantalla($fecha)
    {
        $objeto = new fecha($fecha);
        return $objeto->get_fecha_pantalla();
    }
}
