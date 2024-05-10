<?php

ini_set('display_error', '1');
error_reporting(E_ALL);

use Dompdf\Dompdf;

    $parametros = toba::memoria()->get_parametros();

    if (isset($parametros['id_persona']) &&
        isset($parametros['comprobante_numero']) &&
        isset($parametros['id_alumno_cc'])
       ) {
        $persona = new persona($parametros['id_persona']);
        $datos_comprobante = $persona->obtener_datos_comprobante_afip($parametros['comprobante_numero'], $parametros['id_alumno_cc']);

        if ($datos_comprobante) {
            //Defino los datos fijos de la factura
            $cuit_institucion = dao_consultas::catalogo_de_parametros('cuit_institucion');
            $iibb_institucion = dao_consultas::catalogo_de_parametros('iibb_institucion');
            $fecha_inicio_actividades_institucion = dao_consultas::catalogo_de_parametros('fecha_inicio_actividades_institucion');
            $razon_social_institucion = utf8_encode(dao_consultas::catalogo_de_parametros('razon_social_institucion'));
            $domicilio_comercial_institucion = utf8_encode(dao_consultas::catalogo_de_parametros('domicilio_comercial_institucion'));
            $condicion_frente_iva_institucion = dao_consultas::catalogo_de_parametros('condicion_frente_iva_institucion');

            // Obtener los datos del comprobante
            $punto_venta = str_pad($datos_comprobante['PtoVta'], 5, '0', STR_PAD_LEFT);
            $numero = str_pad($datos_comprobante['numero_comprobante'], 8, '0', STR_PAD_LEFT);
            $fecha = fecha::formatear_para_pantalla($datos_comprobante['CbteFch']);

            //estas 3 fechas hay q obtenerlas
            $periodo_factura_desde = fecha::formatear_para_pantalla($datos_comprobante['FchServDesde']);
            $periodo_factura_hasta = fecha::formatear_para_pantalla($datos_comprobante['FchServHasta']);
            $fecha_vto_pago = fecha::formatear_para_pantalla($datos_comprobante['FchVtoPago']);

            $subtotal = '$' . $datos_comprobante['ImpNeto'];
            $otros_tributos = '$' . $datos_comprobante['ImpTrib'];
            $importe_total = '$' . $datos_comprobante['ImpTotal'];
            $cae = $datos_comprobante['CodAutorizacion'];
            $fecha_vto_cae = fecha::formatear_para_pantalla($datos_comprobante['FchVto']);
            $cuit_cliente = $datos_comprobante['DocNro'];
            $datos_cargo = dao_consultas::get_datos_alumno_cuenta_corriente($datos_comprobante);
            if (isset($datos_cargo)) {
                if (isset($datos_cargo[0]['descripcion'])) {
                    $descripcion = utf8_encode($datos_cargo[0]['descripcion']);
                }
                //antes ponia el dato del alumno, aca debe ir el dato del tutor
                $nombre_tutor = dao_consultas::get_nombres_persona(array('nro_documento' => $cuit_cliente, 'con_dni'=> false));
                $apellido_nombre_cliente = utf8_encode($nombre_tutor[0]['nombre_completo']);
                /*if (isset($datos_cargo[0]['persona'])) {
                    $apellido_nombre_cliente = utf8_encode($datos_cargo[0]['persona']);
                }*/
                if (isset($datos_cargo[0]['direccion_calle'])) {
                    $dir_calle = isset($datos_cargo[0]['direccion_calle']) ? utf8_encode($datos_cargo[0]['direccion_calle']) : '';
                    $dir_numero = isset($datos_cargo[0]['direccion_numero']) ? utf8_encode($datos_cargo[0]['direccion_numero']) : '';
                    $domicilio_cliente = $dir_calle . ' ' . $dir_numero;
                } else {
                    $domicilio_cliente = '';
                }
            }
            $condicion_frente_iva_cliente = dao_consultas::catalogo_de_parametros('condicion_frente_iva_cliente');
            $condicion_venta = 'Contado';

            /*if (isset($datos_comprobante['CodAutorizacion'])) {
                $val = str_replace(",", ".", $datos_comprobante['ImpTotal']);
                $importe = preg_replace("/[\,\.](\d{3})/", "$1", $val);
                $datos_comprobante['fecha_comprobante'] = date("Y-m-d", strtotime($datos_comprobante['CbteFch']));

                $datos_comprobante['fecha_comprobante'] = '2024-01-17';
                $datos_comprobante['CodAutorizacion'] = '74038369730179';
                $datos_comprobante['numero_comprobante'] = '03893';
                $datos_comprobante['DocTipo'] = 86;
                $datos_comprobante['DocNro'] = '20366950126';
                $importe = '36000';

                $url = 'https://www.afip.gob.ar/fe/qr/';
                $json_qr = '{
                       "ver":1,
                       "fecha":"' . $datos_comprobante['fecha_comprobante'] . '",
                       "cuit":' . $cuit_institucion . ',
                       "ptoVta":' . intval($punto_venta) . ',
                       "tipoCmp":' . $datos_comprobante['CbteTipo'] . ',
                       "nroCmp":' . intval($datos_comprobante['numero_comprobante']) . ',
                       "importe":' . $importe . ',
                       "moneda":"PES",
                       "ctz":1,
                       "tipoDocRec":' . $datos_comprobante['DocTipo'] . ',
                       "nroDocRec":' . $datos_comprobante['DocNro'] . ',
                       "tipoCodAut":"E",
                       "codAut":' . $datos_comprobante['CodAutorizacion'] . '
                    }';

                toba::logger()->error($datos_comprobante['fecha_comprobante']);
                toba::logger()->error($cuit_institucion);
                toba::logger()->error($punto_venta);
                toba::logger()->error($datos_comprobante['CbteTipo']);
                toba::logger()->error($datos_comprobante['numero_comprobante']);
                toba::logger()->error($importe);
                toba::logger()->error($datos_comprobante['DocTipo']);
                toba::logger()->error($datos_comprobante['DocNro']);
                toba::logger()->error($datos_comprobante['CodAutorizacion']);

                $datos_qr = $url . '?p=' . base64_encode($json_qr);
                toba::logger()->error($datos_qr);
            }*/

            // URL generada para el código QR
            $url_codigo_qr = "https://www.afip.gob.ar/fe/qr/?p=ewogICAgICAgICAgICAgICAgICAgICAgICJ2ZXIiOjEsCiAgICAgICAgICAgICAgICAgICAgICAgImZlY2hhIjoiMjAyNC0wMS0xNyIsCiAgICAgICAgICAgICAgICAgICAgICAgImN1aXQiOjMwNjcwOTE3Njg4LAogICAgICAgICAgICAgICAgICAgICAgICJwdG9WdGEiOjEsCiAgICAgICAgICAgICAgICAgICAgICAgInRpcG9DbXAiOjExLAogICAgICAgICAgICAgICAgICAgICAgICJucm9DbXAiOjM4OTMsCiAgICAgICAgICAgICAgICAgICAgICAgImltcG9ydGUiOjM2MDAwLAogICAgICAgICAgICAgICAgICAgICAgICJtb25lZGEiOiJQRVMiLAogICAgICAgICAgICAgICAgICAgICAgICJjdHoiOjEsCiAgICAgICAgICAgICAgICAgICAgICAgInRpcG9Eb2NSZWMiOjg2LAogICAgICAgICAgICAgICAgICAgICAgICJucm9Eb2NSZWMiOjIwMzY2OTUwMTI2LAogICAgICAgICAgICAgICAgICAgICAgICJ0aXBvQ29kQXV0IjoiRSIsCiAgICAgICAgICAgICAgICAgICAgICAgImNvZEF1dCI6NzQwMzgzNjk3MzAxNzkKICAgICAgICAgICAgICAgICAgICB9";

            // Decodificar la cadena Base64 para obtener los datos del código QR
            $datos_qr = base64_decode(parse_url($url_codigo_qr, PHP_URL_QUERY));

            // Generar la etiqueta <img> con el código QR codificado en Base64
            $qr_image_src = 'data:image/png;base64,' . base64_encode($datos_qr);
            //toba::logger()->error($qr_image_src);
            // Obtener el tamaño de la imagen
            $size_info = getimagesize($qr_image_src);
            //toba::logger()->error($size_info);

            $img_path = realpath('/home/ale/proyectos_propios/toba3_escuela/toba/proyectos/gestionescuelas/php/comprobantes/factura_c/logo.png');
            //toba::logger()->error($img_path);

            // Generar el HTML del comprobante
            $html_comprobante = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="iso-8859-1">
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <title>Factura C</title>
                <style>
                    @page {
                        size: A4;
                        margin: 2cm;
                    }
        
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        color: #000;
                        margin: 0;
                        padding: 0;
                        border: 1px solid black;
                    }
        
                    h1 {
                        font-size: 16px;
                        text-align: center;
                        margin-bottom: 20px;
                    }
        
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
        
                    table th, table td {
                        padding: 5px;
                        border: 1px solid #000;
                    }
        
                    table th {
                        background-color: #ccc;
                        font-weight: bold;
                        text-align: center;
                    }
        
                    table td {
                        text-align: center;
                    }
        
                    hr {
                        border: none;
                        border-top: 1px solid black;
                        margin: 0;
                    }
        
                    .logo {
                        position: absolute;
                        top: 10px;
                        left: 10px;
                        width: 150px;
                        height: 150px;
                    }
        
                    .header {
                        position: relative;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        height: 80px;
                        margin: 1cm;
                    }
        
                    .header-left {
                        position: absolute;
                        left: 0;
                        right: 50%;
                        top: -30px;
                        width: auto;
                        height: 100%;
                        font-size: 12px;
                        color: #555;
                    }
        
                    .header-right {
                        position: absolute;
                        right: 0;
                        left: 50%;
                        top: -30px;
                        width: auto;
                        height: 100%;
                        font-size: 12px;
                        color: #555;
                    }
        
                    .header-info {
                        margin-top: 50px;
                    }
        
                    .comprobante,
                    .fecha {
                        margin: 0 20px;
                    }
        
                    .comprobante {
                        margin-left: 0;
                    }
                    
                    .comprobante strong {
                        font-weight: bold;
                    }
                    
                    .comprobante.razon-social {
                        font-size: 22px;
                        text-align: center;
                    }
                    
                    .comprobante.factura {
                        font-size: 22px;
                        text-align: left;
                    }
                              
                    .datos-factura {
                        font-size: 12px;
                        border-top: 1px solid black; /* Agregamos un borde superior */
                        margin-top: 10px;
                    }
                    
                    .datos-factura p {
                        display: inline-block;
                        margin: 5px 0;
                    }
                    
                    .datos-cliente {
                        font-size: 12px;
                        border-top: 1px solid black; /* Agregamos un borde superior */
                        margin-top: 10px;
                    }
        
                    .datos-cliente p {
                        margin: 5px 0;
                        margin-left: 5px;
                    }
        
                    .detalle-factura {
                        border-collapse: collapse;
                        width: 100%;
                        margin-top: 20px;
                        font-size: 12px;
                        color: #555;
                    }
        
                    .detalle-factura th {
                        padding: 10px;
                        border: 1px solid black;
                        text-align: center;
                    }
        
                    .detalle-factura td {
                        font-size: 12px;
                        color: #000;
                        text-align: left;
                    }
        
                    .detalle-factura td:nth-child(1) {
                        text-align: center;
                    }
        
                    .detalle-factura td:nth-child(3),
                    .detalle-factura td:nth-child(4) {
                        text-align: right;
                    }
        
                    .detalle-factura th:nth-child(1),
                    .detalle-factura th:nth-child(4),
                    .detalle-factura td:nth-child(1),
                    .detalle-factura td:nth-child(4) {
                        border-left: none;
                        border-right: none;
                    }
        
                    .footer {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        height: 4cm;
                        /*background-color: #f7f7f7;*/
                        font-size: 12px;
                        color: #555;
                        padding: 30px;
                    }
        
                    .footer table {
                        width: 100%;
                    }
        
                    .footer th {
                        text-align: left;
                        width: 50%;
                    }
        
                    .footer td {
                        text-align: right;
                        width: 50%;
                    }
        
                    /* Estilo para los datos adicionales */
                    .datos-adicionales-footer {
                        font-size: 10px;
                        text-align: right;
                        margin-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="header-left">
                        <p class="comprobante razon-social">$razon_social_institucion</p>
                        <p class="comprobante"><strong>Domicilio Comercial:</strong> $domicilio_comercial_institucion</p>
                        <p class="comprobante"><strong>Condicion frente al IVA:</strong> $condicion_frente_iva_institucion</p>
                    </div>
                    <div class="header-right">
                        <p class="comprobante factura">FACTURA C</p>
                        <p class="comprobante"><strong>Punto de Venta: $punto_venta</strong></p>
                        <p class="comprobante"><strong>Comp. Nro: $numero</strong></p>
                        <p class="comprobante"><strong>Fecha de Emision: $fecha</strong></p>
                        <p class="comprobante"><strong>CUIT:</strong> $cuit_institucion</p>
                        <p class="comprobante"><strong>Ingresos Brutos:</strong> $iibb_institucion</p>
                        <p class="comprobante"><strong>Fecha de inicio de actividades:</strong> $fecha_inicio_actividades_institucion</p>
                    </div>
                </div>
                <div class="datos-factura">
                    <p><strong>Periodo facturado desde:</strong> $periodo_factura_desde</p>
                    <p><strong>Hasta:</strong> $periodo_factura_hasta</p>
                    <p><strong>Fecha de vto. para el pago:</strong> $fecha_vto_pago</p>
                </div>
                <div class="datos-cliente">
                    <p><strong>CUIL:</strong> $cuit_cliente</p>
                    <p><strong>Apellido y Nombres:</strong> $apellido_nombre_cliente</p>
                    <p><strong>Condicion frente al IVA:</strong> $condicion_frente_iva_cliente</p>
                    <p><strong>Domicilio:</strong> $domicilio_cliente</p>
                    <p><strong>Condicion de venta:</strong> $condicion_venta</p>
                </div>
                <hr>
                <table class="detalle-factura">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Descripcion</th>
                        <th>Precio unitario</th>
                        <th>Importe</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>$descripcion</td>
                        <td>$subtotal</td>
                        <td>$subtotal</td>
                    </tr>
                    </tbody>
                </table>
                <div class="footer">
                    <table>
                        <tr>
                            <th>Subtotal:</th>
                            <td>$subtotal</td>
                        </tr>
                        <tr>
                            <th>Importe Otros Tributos:</th>
                            <td>$otros_tributos</td>
                        </tr>
                        <tr>
                            <th>Importe Total:</th>
                            <td>$importe_total</td>
                        </tr>
                    </table>
                    <div class="datos-adicionales-footer">
                        <p class="comprobante">CAE N: $cae</p>
                        <p class="comprobante">Fecha de Vto. de CAE: $fecha_vto_cae</p>
                    </div>
                    <div class="page-number"></div>
                </div>
                <!-- Incrustar el código QR en el PDF -->
                <!--img src="' . $qr_image_src . '" alt=""-->
                <!--img src="proyectos/gestionescuelas/php/comprobantes/factura_c/logo.png"-->
                <!--<img src="' . $img_path . '" alt="Logo">-->
            </body>
        </html>
        HTML;

            // Crear una instancia de Dompdf
            $dompdf = new Dompdf();

            // Cargar el HTML en Dompdf
            $dompdf->loadHtml(mb_convert_encoding($html_comprobante, 'HTML-ENTITIES', 'ISO-8859-1'));
            $dompdf->loadHtml($html_comprobante);

            $dompdf->setPaper('A4', 'portrait');

            // Renderizar el PDF
            $dompdf->render();

            // Generar el PDF con dompdf
            $dompdf->stream("comprobante_".$numero.".pdf");

            // Obtener el contenido del PDF generado
            $pdf_content = $dompdf->output();

            // Limpiar el búfer de salida
            ob_end_clean();

            // Configurar los encabezados para la descarga del archivo
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="comprobante_'.$numero.'.pdf"');
            header('Content-Length: ' . strlen($pdf_content));
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Enviar el contenido del PDF como un archivo descargable
            echo $pdf_content;
        }
    }
?>