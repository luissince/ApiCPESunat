<?php
require  './lib/mpdf/vendor/autoload.php';
require  './../src/autoload.php';

use SysSoftIntegra\Model\IngresoADO;
use SysSoftIntegra\Model\CajaADO;
use SysSoftIntegra\Model\EmpresaADO;
use SysSoftIntegra\Src\Tools;
use Mpdf\Mpdf;

$title = "RESUMEN DE INGRESOS";
$resultTransaccion = IngresoADO::ReporteGeneralIngresosEgresos($_GET["txtFechaInicial"], $_GET["txtFechaFinal"],  $_GET["usuario"],  $_GET["idUsuario"]);
$resultMovimiento = CajaADO::ReporteGeneralMovimientoCaja($_GET["txtFechaInicial"], $_GET["txtFechaFinal"],  $_GET["usuario"],  $_GET["idUsuario"]);

if (!is_array($$resultTransaccion) && !is_array($resultMovimiento)) {
    Tools::printErrorJson($resultTransaccion);
    Tools::printErrorJson($resultMovimiento);
    return;
}

date_default_timezone_set('America/Lima');

$result = array();
if ($_GET["transaccion"] == 1) {
    foreach ($resultTransaccion as $value) {
        array_push($result, $value);
    }
}

if ($_GET["movimientos"] == 1) {
    foreach ($resultMovimiento as $value) {
        array_push($result, $value);
    }
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

            .text-center{
                text-align: center;
            }

            .border-left{
                border-left:1px solid #000000;
            }

            .border-right{
                border-right:1px solid #000000;
            }

            .border-up{
                border-up:1px solid #000000;
            }

            .border-bottom{
                border-bottom:1px solid #000000;
            }

            .td-total-text{
                text-align: left;
                font-size: 12px;
                font-weight: bold;
                padding: 5px 10px;
            }

            .td-total-monto{
                text-align: left;
                font-size: 12px;
                padding:5px 10px;
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
        <htmlpagefooter name="myfooter">        
            <div style="width:33%;float:left;text-align:center; color:#333;font-size: 9pt;">
                Página {PAGENO} de {nb}
            </div>
            <div style="width:33%;float:left;text-align:center; color:#333;font-size: 9pt;">
                Generado por SySoftIntegra
            </div>
            <div style="width:33%;float:left;text-align:center;color:#333;font-size: 9pt;">
                ' . date("d/m/Y") . ' '.date("h:m a") .'
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
                    <span style="font-size: 12pt; color: black; font-family: sans;">
                        <b>' . $empresa->RazonSocial . '</b>
                    </span>
                    <br>
                    <span style="font-size: 12pt; color: black; font-family: sans;">
                        <b>R.U.C: ' . $empresa->NumeroDocumento . '</b>
                    </span>
                    <br><br>
                    <span style="font-size: 11pt; color: black; font-family: sans;">
                        RESUMEN DE INGRESOS/EGRESOS
                    </span>
                    <br>
                    <br>
                    <span style="font-size: 10pt; color: black; font-family: sans;">
                        PERIODO
                    </span>
                    <br>
                    <span style="font-size: 10pt; color: black; font-family: sans;">
                        DEL  ' . date("d/m/Y", strtotime($_GET["txtFechaInicial"])) . " AL " . date("d/m/Y", strtotime($_GET["txtFechaFinal"])) . '
                    </span>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%" border="0" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <th class="th-table-detailt">N°</th>
                    <th class="th-table-detailt">TRANSACCIÓN</th>
                    <th class="th-table-detailt">CANT.</th>
                    <th class="th-table-detailt">EFECTIVO</th>
                    <th class="th-table-detailt">TARJETA</th>
                    <th class="th-table-detailt">DEPOSITO</th>
                </tr>
            </thead>
            <tbody>';
$count = 0;

$totalEfectivoIngreso = 0;
$totalEfectivoSalida = 0;
$totalTarjetaIngreso = 0;
$totalTarjetaSalida = 0;
$totalDepositoIngreso = 0;
$totalDepositoSalida = 0;
foreach ($result as $value) {
    $count++;
    $html .= '
                            <tr>
                                <td class="td-table-detailt text-center">' . $count . '</td>
                                <td class="td-table-detailt">' . $value->Transaccion . '</td>
                                <td class="td-table-detailt text-center">' . Tools::roundingValue($value->Cantidad) . '</td>
                                <td class="td-table-detailt text-center">' . Tools::roundingValue($value->Efectivo) . '</td>
                                <td class="td-table-detailt text-center">' . Tools::roundingValue($value->Tarjeta) . '</td>
                                <td class="td-table-detailt text-center td-table-detailt-end">' . Tools::roundingValue($value->Deposito) . '</td>
                            </tr>
                            ';

    if ($value->FormaIngreso == "EFECTIVO") {
        if ($value->Transaccion == "COMPRAS" || $value->Transaccion == "EGRESOS") {
            $totalEfectivoIngreso += 0;
            $totalEfectivoSalida += $value->Efectivo;
        } else {
            $totalEfectivoIngreso += $value->Efectivo;
            $totalEfectivoSalida += 0;
        }
    } else if ($value->FormaIngreso == "TARJETA") {
        if ($value->Transaccion == "COMPRAS" || $value->Transaccion == "EGRESOS") {
            $totalTarjetaIngreso += 0;
            $totalTarjetaSalida += $value->Tarjeta;
        } else {
            $totalTarjetaIngreso += $value->Tarjeta;
            $totalTarjetaSalida += 0;
        }
    } else {
        if ($value->Transaccion == "COMPRAS" || $value->Transaccion == "EGRESOS") {
            $totalDepositoIngreso += 0;
            $totalDepositoSalida += $value->Deposito;
        } else {
            $totalDepositoIngreso += $value->Deposito;
            $totalDepositoSalida += 0;
        }
    }
}

$html .= '</tbody>
        </table>

        <br>
        
        <div>
            <span style="font-size:10pt;font-weight:bold;">RESUMEN GENERAL</span>
        </div>

        <div style="width: 100%;">
            <div align="left" style="width: 22%;float: left;">
                <table style="display:block;float:left;" border="0" cellspacing="0" cellpadding="0" border="0">
                    <thead>        
                        <tr>
                            <th class="th-table-detailt" colspan="2">EFECTIVO</th>          
                        </tr>
                        <tr>
                            <th class="td-total-text text-center border-left border-bottom">INGRESO</th>
                            <th class="td-total-text text-center border-left border-right border-bottom">SALIDA</th>
                        </tr>
                        <tr>
                            <th class="td-total-monto text-center border-left border-bottom">' . Tools::roundingValue($totalEfectivoIngreso) . '</th>
                            <th class="td-total-monto text-center border-left border-right border-bottom">' . Tools::roundingValue($totalEfectivoSalida) . '</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div align="left" style="width: 22%;float: left;">
                <table style="display:block;float:left;" border="0" cellspacing="0" cellpadding="0" border="0">
                    <thead>        
                        <tr>
                            <th class="th-table-detailt" colspan="2">TARJETA</th>          
                        </tr>
                        <tr>
                            <th class="td-total-text text-center border-left border-bottom">INGRESO</th>
                            <th class="td-total-text text-center border-left border-right border-bottom">SALIDA</th>
                        </tr>
                        <tr>
                            <th class="td-total-monto text-center border-left border-bottom">' . Tools::roundingValue($totalTarjetaIngreso) . '</th>
                            <th class="td-total-monto text-center border-left border-right border-bottom">' . Tools::roundingValue($totalTarjetaSalida) . '</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div align="left" style="width: 22%;float: left;">
                <table style="float:left;" border="0" cellspacing="0" cellpadding="0" border="0">
                    <thead>        
                        <tr>
                            <th class="th-table-detailt" colspan="2">DEPOSITO</th>          
                        </tr>
                        <tr>
                            <th class="td-total-text text-center border-left border-bottom">INGRESO</th>
                            <th class="td-total-text text-center border-left border-right border-bottom">SALIDA</th>
                        </tr>
                        <tr>
                            <th class="td-total-monto text-center border-left border-bottom">' . Tools::roundingValue($totalDepositoIngreso) . '</th>
                            <th class="td-total-monto text-center border-left border-right border-bottom">' . Tools::roundingValue($totalDepositoSalida) . '</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        
    </body>
</html>';


$mpdf = new Mpdf([
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10,
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'P'
]);

$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("RESUMEN DE INGRESO DEL " . date("d-m-Y", strtotime($_GET["txtFechaInicial"])) . " AL " . date("d-m-Y", strtotime($_GET["txtFechaFinal"])));
$mpdf->SetAuthor("Syssoft Integra");
$mpdf->SetWatermarkText((""));   // anulada
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);

// Output a PDF file directly to the browser
$mpdf->Output("RESUMEN DE INGRESO DEL " . date("d-m-Y", strtotime($_GET["txtFechaInicial"])) . " AL " . date("d-m-Y", strtotime($_GET["txtFechaFinal"])) . ".pdf", 'I');
