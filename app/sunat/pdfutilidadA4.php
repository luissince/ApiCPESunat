<?php

require './lib/mpdf/vendor/autoload.php';
require './../src/autoload.php';

use SysSoftIntegra\Model\VentasADO;
use SysSoftIntegra\Model\EmpresaADO;
use SysSoftIntegra\Src\Tools;
use Mpdf\Mpdf;

$result = VentasADO::ListarUtilidad($_GET["fechaInicial"], $_GET["fechaFinal"], $_GET["idSuministro"], $_GET["idCategoria"], $_GET["idMarca"], $_GET["idPresentacion"]);
if (!is_array($result)) {
    Tools::printErrorJson($result);
    return;
}

$empresa = EmpresaADO::ReporteEmpresa();
$photo = Tools::showImageReport($empresa->Image);


$html = '
<html>
<head>
<style>
    body {
        font-family: Arial;
        font-size: 10pt;
    }
    p {	
        font-family: Arial;
    }
    table.items {
        border: 0.1mm solid #000000;
    }
    td { 
        vertical-align: middle; 
    }
    table thead th {
        border-left: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
        border-top: 0.1mm solid #000000;
        border-bottom: 0.1mm solid #000000;
    }
    table tbody td {
        border-left: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
        border-top: 0.1mm solid #000000;
        border-bottom: 0.1mm solid #000000;
    }
    table tfoot td {
        border-left: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
        border-top: 0.1mm solid #000000;
        border-bottom: 0.1mm solid #000000;
    }
    table thead td { 
        background-color: #EEEEEE;
        text-align: center;
        border: 0.1mm solid #000000;
        font-variant: small-caps;
    }
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
    <table width="100%">
        <tr>
            <td width="50%" style="color:#969696; ">
                <span style="font-weight: bold; font-size: 9pt;">
                    Generado por SySoftIntegra
                </span>
            </td>
            <td width="50%" style="color:#969696;text-align: right;">
                <span style="font-weight: bold; font-size: 9pt;">
                    www.syssoftintegra.com
                </span>
            </td>
        </tr>
    </table>
</htmlpageheader>
<htmlpagefooter name="myfooter">
    <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
        Pagin {PAGENO} de {nb}
    </div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<table width="100%" style="font-family: serif;" cellpadding="10">
    <tr>
        <td width="15%" style="border: 0mm solid #888888;text-align: center;">
            ' . $photo . '
        </td>
        <td width="85%" style="border: 0mm solid #888888;text-align: center;vertical-align: middle;">
            <span style="font-size: 13pt; color: black; font-family: sans;">
                <b>UTILIDAD GENERADA</b>
            </span>
            <br>
            <br>
            <span style="font-size: 10pt; color: black; font-family: sans;">
                PERIODO
            </span>
            <br>
            <span style="font-size: 10pt; color: black; font-family: sans;">
                DEL ' . date("d/m/Y", strtotime($_GET["fechaInicial"])) . ' AL ' . date("d/m/Y", strtotime($_GET["fechaFinal"])) . '
            </span>
        </td>
    </tr>
</table>

<table width="100%" style="font-family: arial;font-size: 9pt;" border="0" cellspacing="5">
  
        <tr>
            <th style="text-align: left;">PRODUCTO: ' . $_GET["nameProducto"] . '</th>
            <th style="text-align: left;">MARCA: ' . $_GET["nameMarca"] . '</th>
        </tr>

        <tr>
            <th style="text-align: left;">CATEGORÍA: ' . $_GET["nameCategoria"] . '</th>
            <th style="text-align: left;">PRESENTACIÓN: ' . $_GET["namePresentacion"] . '</th>
        </tr>
       
</table>

<br />

<table class="items" width="100%" style="font-size: 14pt; border-collapse: collapse; " cellpadding="8">
    <thead>
        <tr>
            <th width="5%">N°</th>
            <th width="45%">Producto</th>
            <th width="20%">Cantidad</th>
            <th width="20%">Costo</th>
            <th width="20%">Costo Total</th>
            <th width="20%">Precio</th>
            <th width="20%">Precio Total</th>
            <th width="20%">Utilidad</th>
        </tr>
    </thead>
    <tbody>';

if ($_GET["mostrarTodo"] == 1) {
    $count = 0;
    $costoTotal = 0;
    $precioTotal = 0;
    $utilidadGenerada = 0;

    foreach ($result as $value) {
        $count++;
        $html .= '
        <tr>
            <td>' . $count . '</td>
            <td>' . $value["Clave"] . '<br>' . $value["NombreMarca"] . '</td>
            <td>' . $value["Cantidad"] . '<br>' . $value["UnidadCompraNombre"] . '</td>
            <td>' . Tools::roundingValue($value["Costo"]) . '</td>
            <td>' . Tools::roundingValue($value["CostoTotal"]) . '</td>
            <td>' . Tools::roundingValue($value["Precio"]) . '</td>
            <td>' . Tools::roundingValue($value["PrecioTotal"]) . '</td>
            <td>' . Tools::roundingValue($value["Utilidad"]) . '</td>
        </tr>';

        if (Tools::validateDuplicateSuministro($result, $value)) {
            $costoTotal += $value["CostoTotal"];
            $precioTotal += $value["PrecioTotal"];
            $utilidadGenerada += $value["Utilidad"];
        } else {
            $costoTotal = $value["CostoTotal"];
            $precioTotal = $value["PrecioTotal"];
            $utilidadGenerada = $value["Utilidad"];
        }
    }
} else {
    $count = 0;
    $costoTotal = 0;
    $precioTotal = 0;
    $utilidadGenerada = 0;
    $newArray = array();

    foreach ($result as $value) {
        if (Tools::validateDuplicateSuministro($newArray, $value)) {
            for ($j = 0; $j < count($newArray); $j++) {
                if ($newArray[$j]["IdSuministro"] == $value["IdSuministro"]) {
                    $newArray[$j]["Cantidad"] = $newArray[$j]["Cantidad"] + $value["Cantidad"];

                    $newArray[$j]["Costo"] = $newArray[$j]["Costo"] + $value["Costo"];
                    $newArray[$j]["CostoTotal"] = $newArray[$j]["CostoTotal"] + $value["CostoTotal"];

                    $newArray[$j]["Precio"] = $newArray[$j]["Precio"] + $value["Precio"];
                    $newArray[$j]["PrecioTotal"] = $newArray[$j]["PrecioTotal"] + $value["PrecioTotal"];

                    $newArray[$j]["Utilidad"] = $newArray[$j]["Utilidad"] + $value["Utilidad"];
                }
            }
        } else {
            array_push($newArray, $value);
        }
    }

    for ($j = 0; $j < count($newArray); $j++) {
        if ($newArray[$j]["Cantidad"] > 0) {
            $newArray[$j]["Costo"] = $newArray[$j]["CostoTotal"] / $newArray[$j]["Cantidad"];
            $newArray[$j]["Precio"] = $newArray[$j]["PrecioTotal"] / $newArray[$j]["Cantidad"];
        }
    }

    foreach ($newArray as $value) {
        $count++;
        $html .= '
        <tr>
            <td>' . $count . '</td>
            <td>' . $value["Clave"] . '<br>' . $value["NombreMarca"] . '</td>
            <td>' . $value["Cantidad"] . '<br>' . $value["UnidadCompraNombre"] . '</td>
            <td>' . Tools::roundingValue($value["Costo"]) . '</td>
            <td>' . Tools::roundingValue($value["CostoTotal"]) . '</td>
            <td>' . Tools::roundingValue($value["Precio"]) . '</td>
            <td>' . Tools::roundingValue($value["PrecioTotal"]) . '</td>
            <td>' . Tools::roundingValue($value["Utilidad"]) . '</td>
        </tr>';


        $costoTotal += $value["CostoTotal"];
        $precioTotal += $value["PrecioTotal"];
        $utilidadGenerada += $value["Utilidad"];
    }
}

$html .= '
</tbody>
</table>

<br />
<br />
<table class="items" width="25%" style="font-size: 8pt; border-collapse: collapse; " cellpadding="8">
    <thead>
        <tr>
            <th width="40%">Costo Total</th>
            <th width="40%">Precio Total</th>
            <th width="40%">Utilidad Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center">' . Tools::roundingValue($costoTotal) . '</td>
            <td align="center">' . Tools::roundingValue($precioTotal)  . '</td>
            <td align="center">' . Tools::roundingValue($utilidadGenerada)  . '</td>
        </tr>
    </tbody>
</table>

</body>
</html>
';


$mpdf = new Mpdf([
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 18,
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10,
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'L'
]);

$mpdf->SetTitle("Utilidad Generada");
$mpdf->SetAuthor("Syssoft Integra");
$mpdf->SetWatermarkText((""));   // anulada
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);

// Output a PDF file directly to the browser
$mpdf->Output("Utilidad Generada.pdf", 'I');
