<?php

require  './lib/mpdf/vendor/autoload.php';
require  './../src/autoload.php';

use SysSoftIntegra\Model\VentasADO;
use SysSoftIntegra\Src\Tools;
use Mpdf\Mpdf;

$fechaInicio = $_GET["txtFechaInicial"];
$fechaFinal = $_GET["txtFechaFinal"];

$fechaInicioFormato = date("d/m/Y", strtotime($fechaInicio));
$fechaFinalFormato =  date("d/m/Y", strtotime($fechaFinal));
$result = VentasADO::GenerarComprobantesFacturados($fechaInicio, $fechaFinal);

$ventas = $result[0];
$empresa = $result[1];

$facturas = 0;
$notaCredito = 0;

$html = '<html>
<head>
<style>
    body {
        font-family: Arial;
        font-size: 10pt;
    }

    table tr td, p{
        font-family: Arial;
    }

    table tr td{
        vertical-align: top;
    }
    
    .td-body-left{
        text-align: left;
        padding: 10px;
        width: 50%;
    }

    a {
        color:#969696;
    }

    .color-red{
        color:red;
    }

    .td-total-text{
        text-align: left;
        font-size: 12px;
        font-weight: bold;
        padding: 5px 10px;
        width: 50%;
    }

    .td-total-monto{
        text-align: left;
        font-size: 12px;
        padding: 0px 10px 10px 10px;
        width: 50%;
    }

    .td-title-left{
        padding:10px;
        width:50%;
        border-bottom:1px solid #000000;
    }

    .td-title-right{
        padding:10px;
        width:50%;
        text-align:right;
        border-bottom:1px solid #000000;
    }

    .th-table-detailt{
        background-color:black;
        color:white;
        padding:5px 3px 5px 3px;
        font-size:12px;
        border: 1px solid #000000;
    }

    .td-table-detailt{
        padding:5px 10px;
        font-size:12px;
        border-left: 1px solid #000000;
        border-bottom: 1px solid #000000;
    }
    
    .td-table-detailt-end{
        border-right: 1px solid #000000;
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
                        Generado por <a href="http://syssoftintegra.com/" target="_blank">www.syssoftintegra.com</a>
                    </span>
                </td>
                <td width="50%" style="color:#969696;text-align: right;">
                    <span style="font-weight: bold; font-size: 9pt;">
                        ' . date("d/m/Y") . '
                    </span>
                </td>
            </tr>
        </table>
    </htmlpageheader>
    <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
            Página {PAGENO} de {nb}
        </div>
    </htmlpagefooter>
    <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
    <sethtmlpagefooter name="myfooter" value="on" />
    mpdf-->

    <table width="100%" border="0" cellspacing="0">
        <tr>
            <td class="td-title-left">
                <h3>Comprobantes Facturados del ' . $fechaInicioFormato . ' al ' . $fechaFinalFormato . '</h3>
            </td>           
        </tr>
    </table>

    <table width="100%" border="0" cellspacing="0">
        <thead>
            <tr>
                <th class="th-table-detailt"> Fecha</th>
                <th class="th-table-detailt"> Tipo Documento</th>
                <th class="th-table-detailt"> Nrm Documento</th>
                <th class="th-table-detailt"> Datos Cliente</th>
                <th class="th-table-detailt"> Tipo Comprobante</th>
                <th class="th-table-detailt"> Serie</th>
                <th class="th-table-detailt"> Numeración</th>
                <th class="th-table-detailt"> Base</th>
                <th class="th-table-detailt"> Igv</th>
                <th class="th-table-detailt"> Total</th>
                <th class="th-table-detailt"> Anulado</th>
                <th class="th-table-detailt"> Mensaje Sunat</th>
            </tr>
        </thead>
        <tbody>';

foreach ($ventas as $key => $value) {
    if ($value["Xmlsunat"] == "1032" || $value["Xmlsunat"] == "") {
        $html .= '
        <tr>
            <td class="td-table-detailt color-red">' . Tools::formatDate($value["FechaRegistro"]) . '</td>
            <td class="td-table-detailt color-red">' . $value["TipoDocumento"] . '</td>
            <td class="td-table-detailt color-red">' . $value["NumeroDocumento"] . '</td>
            <td class="td-table-detailt color-red">' . $value["Informacion"] . '</td>
            <td class="td-table-detailt color-red">' . $value["TipoComprobante"] . '</td>
            <td class="td-table-detailt color-red">' . $value["Serie"] . '</td>
            <td class="td-table-detailt color-red">' . $value["Numeracion"] . '</td>
            <td class="td-table-detailt color-red">' . Tools::roundingValue($value["Base"]) . '</td>
            <td class="td-table-detailt color-red">' . Tools::roundingValue($value["Igv"]) . '</td>
            <td class="td-table-detailt color-red ">' . Tools::round(0) . '</td>
            <td class="td-table-detailt color-red">' . Tools::roundingValue($value["Base"] + $value["Igv"]) . '</td>            
            <td class="td-table-detailt td-table-detailt-end color-red">' . $value["Xmldescripcion"] . '</td>
        </tr>
    ';
    } else {
        $html .= '
        <tr>
            <td class="td-table-detailt">' . Tools::formatDate($value["FechaRegistro"]) . '</td>
            <td class="td-table-detailt">' . $value["TipoDocumento"] . '</td>
            <td class="td-table-detailt">' . $value["NumeroDocumento"] . '</td>
            <td class="td-table-detailt">' . $value["Informacion"] . '</td>
            <td class="td-table-detailt">' . $value["TipoComprobante"] . '</td>
            <td class="td-table-detailt">' . $value["Serie"] . '</td>
            <td class="td-table-detailt">' . $value["Numeracion"] . '</td>
            <td class="td-table-detailt">' . Tools::roundingValue($value["Base"]) . '</td>
            <td class="td-table-detailt">' . Tools::roundingValue($value["Igv"]) . '</td>
            <td class="td-table-detailt">' . Tools::roundingValue($value["Base"] + $value["Igv"]) . '</td>
            <td class="td-table-detailt">' . Tools::round(0) . '</td>
            <td class="td-table-detailt td-table-detailt-end">' . $value["Xmldescripcion"] . '</td>
        </tr>
    ';

        if ($value["Tipo"] == "F") {
            $facturas += $value["Base"] + $value["Igv"];
        } else {
            $notaCredito += $value["Base"] + $value["Igv"];
        }
    }
}

$html .= '</tbody>
    </table>

    <br>
    
    <table border="0" cellspacing="0">
        <tr>
            <td class="td-total-text">
                <span>FACTURAS</span>             
            </td>
            <td>
                <span>: ' . Tools::roundingValue($facturas) . '</span>
            </td>
        </tr>

        <tr>
            <td class="td-total-text">
                <span>NOTAS DE CREDITO</span>             
            </td>
            <td>
                <span>: ' . Tools::roundingValue($notaCredito) . '</span>
            </td>
        </tr>

        <tr>
            <td class="td-total-text">
                <span>SUMA TOTAL</span>             
            </td>
            <td>
                <span>: ' . Tools::roundingValue($facturas - $notaCredito) . '</span>
            </td>
        </tr>
    </table>

</body>
</html>
';

$mpdf = new Mpdf([
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 12,
    'margin_bottom' => 12,
    'margin_header' => 5,
    'margin_footer' => 5,
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'L'
]);

$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Comprobantes Facturados");
$mpdf->SetAuthor("Syssoft Integra");
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);

// Output a PDF file directly to the browser
$mpdf->Output("RUC " .  $empresa->NumeroDocumento . " - COMPROBANTES FACTURADOS DEL " . $fechaInicio . ' AL ' . $fechaFinal . ".pdf", 'I');
