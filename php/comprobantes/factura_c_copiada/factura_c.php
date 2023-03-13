<?php

    echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
            echo "<head>";
                echo "<meta charset='UTF-8'>";
                echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
                echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
                echo "<link rel='stylesheet' type='text/css' media='screen' href='css/styles.css'>";
            echo "</head>";
        echo "<body>";
        echo "</body>";
    echo "</html>";


    echo "</div><div id='app' class='col-11'>";
        echo "<div class='logo row my-3'>";
            echo "<hr/>";
                echo "<div class='col-2'>";
                    echo "<img src='logo.png' width='150px' height='150px'/>";
                echo "</div>";
                echo "<div class='nombre'>";
                    echo "<h2>Factura N° </h2>";
                    echo "<h1>Escuela N° 58 Sagrada Familia</h1>";
                    echo "<p>Av. Monseñor Ricardo Rösch 4699</p>";
                echo "</div>";
            echo "<hr/>";
        echo "</div>";

        echo "<div class='ladoderecho'>";
            echo "<strong>Punto de Venta: </strong> 1  <strong>      Comprobante N°: </strong>  0000001";
            echo "<br>";
            echo "<strong>Fecha Emisión: </strong> 05/10/2022";
            echo "<br> <br>";
            echo "<strong>CUIT: </strong> 27127112784";
            echo "<br>";
            echo "<strong>Ingresos Brutos: </strong> 27127112784";
            echo "<br>";
            echo "<strong>Fecha de inicio de actividades: </strong> 05/10/2022";
        echo "</div>";

        echo "<div class='medio row fact-info mt-3'>";
            echo "<div class='col-3'>";
                echo "<hr />";
                echo "<strong>CUIT:</strong> 20-25619134-5";
                echo "<br><br>";
                echo "<strong>Apellido y Nombres: </strong> Cueva, Martín";
                echo "<br><br>";
                echo "<strong>Condición ante el IVA: </strong> Consumidor Final";
                echo "<hr />";
            echo "</div>";
        echo "</div>";

        echo "<div class='abajo1 row my-5'>";
            echo "<table class='table table-borderless factura'>";
                echo "<tr>";
                    echo "<thead>";
                        echo "<th>Cant.</th>";
                        echo "<th>Descripcion</th>";
                        echo "<th>Precio Unitario</th>";
                        echo "<th>Importe</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<hr />";
                echo "<tbody>";
                    echo "<tr>";
                        echo "<td>1</td>";
                        echo "<td>Cuota Mes de Octubre 2022</td>";
                        echo "<td>5500.00</td>";
                        echo "<td>5500.00</td>";
                    echo "</tr>";
                    echo "</tfoot>";
            echo "</table>";
        echo "</div>";

        echo "<div class='cond row'>";
            echo "<div class='col-12 mt-3'>";
                echo "</p>";
            echo "</div>";
        echo "</div>";
    echo "</div>";


    echo "<footer>";
        echo "<hr />";
            echo "<div class='textofooter'>";
                echo "<strong>Subtotal: </strong> 5500,00";
                echo "<br><br>";
                echo "<strong>Importe Otros Tributos: </strong> 0,00";
                echo "<br><br>";
                echo "<strong>Importe Total: </strong> 5500,00";
            echo "</div>";
            echo "<hr />";
                echo "<div class='textofooter2'><h3>Escuela N° 58 Sagrada Familia</h3></div>";
            echo "<hr />";
    echo "</footer>";

