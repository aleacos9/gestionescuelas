<?php

class Afip
{
    public $afip_config;
    protected $afip;
    protected $padron_cuatro;
    protected $factura_electronica;

    public function __construct()
    {
        $afip_ini = new toba_ini(toba::instalacion()->get_path_carpeta_instalacion()."/afip.ini");
        $this->afip_config = $afip_ini->get_datos_entrada('afip_config');
        $this->afip_config['cuit'] = getenv('AFIP_WS_CUIT') ?: $this->afip_config['cuit'];
        $this->afip_config['produccion'] = getenv('AFIP_WS_PRODUCCION') ?: $this->afip_config['produccion'];
        $this->afip_config['cert'] = getenv('AFIP_WS_CERT') ?: $this->afip_config['cert'];
        $this->afip_config['key'] = getenv('AFIP_WS_KEY') ?: $this->afip_config['key'];
        $this->afip_config['token_dir'] = getenv('AFIP_WS_TOKEN_DIR') ?: $this->afip_config['token_dir'];

        if (isset($this->afip_config['cuit']) && isset($this->afip_config['cert']) && isset($this->afip_config['key'])) {
            $config = [
                'CUIT' => $this->afip_config['cuit'],
                'production' => $this->afip_config['produccion'],
                'cert' => $this->afip_config['cert'],
                'key' => $this->afip_config['key'],
                'token_dir' => $this->afip_config['token_dir']
            ];

            try {
                $this->afip = new \SIU\Afip\Afip($config);
                $this->padron_cuatro = new \SIU\Afip\WebService\PadronAlcanceCuatro($this->afip);

                /*$this->factura_electronica = new \SIU\Afip\WebService\FacturaElectronica($this->afip);
                $punto_venta = 1;
                $tipo_comprobante = 11;
                $result = $this->factura_electronica->getUltimoComprobante($punto_venta, $tipo_comprobante);
                //$result = $factura_electronica->getTiposCbte();
                //$result = $factura_electronica->getTiposConcepto();
                //$result = $factura_electronica->getEstadoServicio();
                //var_dump($result);*/
            } catch (\Exception $e) {
                toba::notificacion()->warning($e->getMessage());
                toba::logger()->info($e->getMessage());
            }
        }
    }

    /**
     * Valida si esta configurado el WS de AFIP y si las credenciales configuradas
     * se conectan correctamente.
     *
     * @return     \|boolean  ( description_of_the_return_value )
     */
    public function conectado()
    {
        $res = false;

        if (isset($this->afip_config['cuit']) && isset($this->afip_config['cert']) && isset($this->afip_config['key'])) {
            try {
                $res = $this->padron_cuatro->getEstadoServicio();

                $datos = get_object_vars($res);

                if ($datos['appserver'] == 'OK' && $datos['authserver'] == 'OK' && $datos['dbserver'] == 'OK') {
                    $res = true;
                }
            } catch (\Exception $e) {
                toba::notificacion()->warning($e->getMessage());
                toba::logger()->info($e->getMessage());
            }
        }

        return $res;
    }

    public function get_contribuyente_detalle($cuit)
    {
        $datos = [];

        if (isset($this->datos[$cuit])) {
            return $this->datos[$cuit];
        }

        $cuit_sin_guiones = str_replace('-', '', $cuit);
        $res = $this->padron_cuatro->getContribuyenteDetalle($cuit_sin_guiones);

        if (is_object($res)) {
            $datos = $this->datos[$cuit] = get_object_vars($res);
        }

        return $datos;
    }

    public function get_opciones_domicilios($cuit)
    {
        $datos = $this->get_contribuyente_detalle($cuit);

        $prov_domicilios = [];

        if (!isset($datos['domicilio'])) {
            return $prov_domicilios;
        }

        if (is_array($datos['domicilio'])) {
            foreach ($datos['domicilio'] as $domicilio) {
                $domicilios[] = get_object_vars($domicilio);
            }
        } elseif (is_object($datos['domicilio'])) {
            $domicilios[] = get_object_vars($datos['domicilio']);
        }

        if (isset($domicilios)) {
            $domi = [];

            foreach ($domicilios as $domicilio) {
                $domi["direccion"] = null;
                $domi["direccion"] .= (isset($domicilio["direccion"])) ? $domicilio["direccion"] : "";
                $domi["direccion"] .= (isset($domicilio["descripcionProvincia"])) ? " ".$domicilio["descripcionProvincia"] : "";
                $domi["direccion"] .= (isset($domicilio["localidad"])) ? " ".$domicilio["localidad"] : "";
                $domi["direccion"] = utf8_decode($domi["direccion"]);
                $prov_domicilios[] = $domi;
            }
        }

        return $prov_domicilios;
    }

    public function getAfip()
    {
        return $this->afip;
    }
}
