<?php

require './lib/mpdf/vendor/autoload.php';
require './lib/phpqrcode/vendor/autoload.php';
require './../src/autoload.php';

use SysSoftIntegra\Model\NotaCreditoADO;
use SysSoftIntegra\Src\NumberLleters;
use SysSoftIntegra\Src\Tools;
use chillerlan\QRCode\QRCode;
use Mpdf\Mpdf;

$idNotaCredito = $_GET["idNotaCredito"];
$result = NotaCreditoADO::ObtenerNotaCreditoById($idNotaCredito);
$gcl = new NumberLleters();

if (!is_array($result)) {
    Tools::printErrorJson($resultTransaccion);
    return;
}

$notacredito = $result[0];
$empresa = $result[1];
$detalle = $result[2];
$totales = $result[3];
$banco = $result[4];

$photo = Tools::showImageReport($empresa->Image);
$gcl = new NumberLleters();

$codigoFormat = Tools::formatNumber($notacredito->NumeracionNotaCredito);

$importeBrutoTotal = 0;
$descuentoTotal = 0;
$subImporteNetoTotal = 0;
$impuestoTotal = 0;
$importeNetoTotal = 0;

$textoCodBar =
    '|' . $empresa->NumeroDocumento
    . '|' . $notacredito->TipoDocumentoNotaCredito
    . '|' . $notacredito->SerieNotaCredito
    . '|' . $notacredito->NumeracionNotaCredito
    . '|' . number_format(round($totales["totalconimpuesto"], 2, PHP_ROUND_HALF_UP), 2, '.', '')
    . '|' . number_format(round($totales["totalimpuesto"], 2, PHP_ROUND_HALF_UP), 2, '.', '')
    . '|' . $notacredito->FechaNotaCredito
    . '|' . $notacredito->CodigoCliente
    . '|' . $notacredito->NumeroDocumento
    . '|';


$html .= '<html>
    <head>
    <style>
        body {
            font-family: Arial;
            font-size: 10pt;
            color: black;
        }

        a{
            color:#848484;
        }

        p{
            font-weight: normal;
            margin:0;
        }

        h2{
            font-size: 13pt;
            margin:0;
        }

        h3{
            font-size: 11pt;
            margin:0;
        }

        .header-left{
            width:40mm;
            background-color:white;
            text-align: left;
        }

        .header-center{
            width:90mm;
            background-color:white;
            padding:0mm 5mm;
        }

        .header-right{
            width:60mm;
            background-color:white;
        }

        .border-comprobante{
            height:100%;
            border:1px solid #000000;
            padding:3mm 2mm;
        }

        .border-bottom{
            width:100%;
            height:2mm;
            border-bottom:1px solid #00000;
        }

        .mb-1{
            margin-bottom:1mm;
        }

        .mb-2{
            margin-bottom:2mm;
        }

        .mb-3{
            margin-bottom:3mm;
        }

        .mb-5{
            margin-bottom:5mm;
        }

        .th-text-head{
            text-align:left;
            padding:1mm 1mm 1mm 0mm; 
            font-size: 12px;
        }

        .font-normal{
            font-weight: normal;       
        }

        .color-primary{
            color:#b3b1b1;
        }

        .color-white{
            color:white;
        }

        .color-black{
            color:black;
        }

        .background-primary{
            background-color:#b3b1b1;
        }

        .background-seconday{
            background-color:#cccccc;
        }

        .text-left{
            text-align: left;
        }

        .text-center{
            text-align: center;
        }

        .text-right{
            text-align: right;
        }

        .p-1{
            padding:1mm;
        }

        .p-2{
            padding:2mm;
        }

        .ptb-1{
            padding-top:1mm;
            padding-bottom:1mm;
        }

        .ptb-2{
            padding-top: 2mm;
            padding-bottom: 2mm;
        }

        .plr-1{
        padding-left: 1mm;
        padding-right: 1mm;
        }

        .plr-2{
        padding-left: 2mm;
        padding-right: 2mm;
        }

        .font-size-8{
            font-size: 8pt;
        }

        .font-size-9{
            font-size: 9pt;
        }

        .font-size-10{
            font-size: 10pt;
        }

        .th-total-title{
            padding:0mm 2mm 0mm 0mm;
            font-weight:normal;
            text-align:right;
        }

        .th-total-valor{
            padding:1mm 0mm;
            text-align:right;
        }    

        .div-footer{
            width:92mm;
            background-color:white;
            float:left;
            padding-left:2mm;
        }

    </style>
    </head>
    <body>
        <!--mpdf
        <htmlpagefooter name="myfooter">        
            <div style="width:50%;float:left;text-align:center; color:#333;">
                Generado por SySoftIntegra
            </div>
            <div style="width:50%;float:left;text-align:center;color:#333;">
                www.syssoftintegra.com
            </div>  
        </htmlpagefooter>
        <sethtmlpagefooter name="myfooter" value="on" />
        mpdf-->

        <table width="100%" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="header-left">
                    ' . $photo . '
                    </th>
                    <th class="header-center">
                        <h2>' . $empresa->RazonSocial . '</h2>
                        <p>' . $empresa->Domicilio . '</p>
                        <p>TELÉFONO: ' . $empresa->Telefono . ' CELULAR: ' . $empresa->Celular . '</p>
                        <p class="font-size-9">' . $empresa->Email . '</p>
                        <p class="font-size-9">' . $empresa->PaginaWeb . '</p>
                    </th>
                    <th class="header-right">                  
                        <table width="100%" height="100%" border="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="border-comprobante">
                                    <h4>R.U.C.: ' . $empresa->NumeroDocumento . '</h4>       
                                    <br>                             
                                    <h3>' . $notacredito->Comprobante . '</h3>       
                                    <br>
                                    <h4>' . $notacredito->SerieNotaCredito . ' - ' . $codigoFormat . '</h4>                                   
                                </tr>                          
                            </thead>
                        </table>                                           
                    </th>
                </tr>
            </thead>      
        </table>

        <div class="border-bottom mb-2"></div>
        
        <table border="0" cellspacing="0" cellpadding="1" class="mb-2">
            <thead>
                <tr>
                    <th class="th-text-head">D.N.I./R.U.C.</th>    
                    <th class="th-text-head font-normal">: ' . $notacredito->NumeroDocumento . '</th>     
                </tr>
                <tr>
                    <th class="th-text-head">NOMBRES</th>      
                    <th class="th-text-head font-normal">: ' . $notacredito->Informacion . '</th>        
                </tr>
                <tr>
                    <th class="th-text-head">DIRECCIÓN</th>            
                    <th class="th-text-head font-normal">: ' . $notacredito->Direccion . '</th>  
                </tr>
                <tr>
                    <th class="th-text-head">FECHA EMISIÓN</th>            
                    <th class="th-text-head font-normal">: ' . date("d/m/Y", strtotime($notacredito->FechaNotaCredito)) . '</th>  
                </tr>
                <tr>
                    <th class="th-text-head">MONEDA</th>            
                    <th class="th-text-head font-normal">: ' . $notacredito->NombreMoneda . ' - ' . $notacredito->TipoMoneda . '</th>  
                </tr>
            </thead>
        </table>

        <table width="100%" border="0" cellspacing="0" class="mb-2">
            <thead>
                <tr>
                    <th class="background-primary p-1">Ítem</th>    
                    <th class="background-seconday p-1">Detalle</th>     
                    <th class="background-seconday p-1">Cantidad</th>  
                    <th class="background-seconday p-1">Precio</th>  
                    <th class="background-seconday p-1">Unidad</th>  
                    <th class="background-primary p-1">Importe</th>  
                </tr>
            </thead>
            <tbody>';
?>
                                        <?php
                                        foreach ($detalle as $value) {
                                            $html .= '<tr>
                                            <td class="background-primary plr-2 ptb-1 font-size-8 text-center">' . $value["Id"] . '</td>
                                            <td class="background-seconday plr-2 ptb-1 font-size-8">' . $value["Clave"] . '<br>' . $value["NombreMarca"] . '</td>
                                            <td class="background-seconday plr-2 ptb-1 font-size-8">' . Tools::roundingValue($value["Cantidad"]) . '</td>
                                            <td class="background-seconday plr-2 ptb-1 font-size-8">' . Tools::roundingValue($value["Precio"]) . '</td>
                                            <td class="background-seconday plr-2 ptb-1 font-size-8">' . $value["UnidadCompra"] . '</td>
                                            <td class="background-primary plr-2 ptb-1 font-size-8 text-right">' . Tools::roundingValue($value["Precio"] * $value["Cantidad"]) . '</td>
                                            </tr>';

                                            $importeBruto = $value["Precio"] * $value["Cantidad"];
                                            $descuento = $value["Descuento"];
                                            $subImporteBruto = $importeBruto - $descuento;
                                            $subImporteNeto = Tools::calculateTaxBruto($value["ValorImpuesto"], $subImporteBruto);
                                            $impuesto = Tools::calculateTax($value["ValorImpuesto"], $subImporteNeto);
                                            $importeNeto = $subImporteNeto + $impuesto;

                                            $importeBrutoTotal += $importeBruto;
                                            $descuentoTotal += $descuento;
                                            $subImporteNetoTotal += $subImporteNeto;
                                            $impuestoTotal += $impuesto;
                                            $importeNetoTotal += $importeNeto;
                                        }
                                        ?>
                                        <?php
                                        $html  .= '</tbody>
        </table>

        <table width="" style="margin: 0 0 0 auto;" border="0" cellspacing="1">
            <tbody>
            <tr>
            <th class="th-total-title font-size-9">IMPORTE BRUTO:</th>
            <th class="th-total-valor font-size-9"> ' . $notacredito->Simbolo . ' ' . Tools::roundingValue($importeBrutoTotal) . '</th>
        </tr>
        <tr>
            <th class="th-total-title font-size-9">DESCUENTO:</th>
            <th class="th-total-valor font-size-9"> ' . $notacredito->Simbolo . ' ' . Tools::roundingValue($descuentoTotal) . '</th>
        </tr>
        <tr>
            <th class="th-total-title font-size-9">SUB IMPORTE:</th>
            <th class="th-total-valor font-size-9"> ' . $notacredito->Simbolo . ' ' . Tools::roundingValue($subImporteNetoTotal) . '</th>
        </tr>
        <tr>
            <th class="th-total-title font-size-9">IGV(18%):</th>
            <th class="th-total-valor font-size-9"> ' . $notacredito->Simbolo . ' ' . Tools::roundingValue($impuestoTotal) . '</th>
        </tr>
        <tr>
            <th class="th-total-title font-size-9">IMPORTE NETO :</th>
            <th class="th-total-valor font-size-9"> ' . $notacredito->Simbolo . ' ' . Tools::roundingValue($importeNetoTotal) . '</th>
        </tr>
            <tbody>
        </table> 

        <div class="border-bottom"></div>

        <p class="mb-2 font-size-9">
            <b>SON: ' . $gcl->getResult(round($importeNetoTotal, 2, PHP_ROUND_HALF_UP), $notacredito->NombreMoneda) . '</b>
        <p>

        <div style="border-left: 2mm solid #b3b1b1; width:100%;">
            <div class="div-footer text-center">
                <h3 class="mb-2">Terminos y Condiciones</h3>                       
                <p class="font-normal font-size-9 mb-1">
                ' . $empresa->Terminos . '
                </p> 
                <p style="font-size:9pt;">
                ' . $empresa->Condiciones . '
                </p> 
                <br>
                <h3 class="mb-2">Numero de Cuentas</h3>';
                                        foreach ($banco as $value) {
                                            $html .= '
                        <p class="font-normal font-size-9 mb-1">
                        <b>' . $value->NombreCuenta . '</b>
                        ' . $value->Moneda . '
                        <b>N°</b>
                        ' . $value->NumeroCuenta . '
                        </p>';
                                        }
                                        $html .= '
            </div>

            <div class="div-footer text-center">
                <img width="120" src="' . (new QRCode)->render($textoCodBar) . '" alt="QR Code" />
                <div>Hashcode: ' . $notacredito->CodigoHash . '</div>
                <p style="font-size:11px; margin:0px;">
                    Representación Impresa de la Factura Electrónica Autorizado para ser Emisor electrónico. Para consultar el comprobante ingrese a: https://sunat.god.pe
                </p> 
            </div>   
        </div>

    </body>
    </html>';

                                        $mpdf = new Mpdf([
                                            'margin_left' => 10,
                                            'margin_right' => 10,
                                            'margin_top' => 10,
                                            'margin_bottom' => 10,
                                            'margin_header' => 0,
                                            'margin_footer' => 5,
                                            'mode' => 'utf-8',
                                            'format' => 'A4',
                                            'orientation' => 'P'
                                        ]);

                                        $mpdf->SetProtection(array('print'));
                                        $mpdf->SetTitle("Syssoft Integra");
                                        $mpdf->SetAuthor("Syssoft Integra");
                                        $mpdf->SetWatermarkText(("PAGADO"));   // anulada
                                        $mpdf->showWatermarkText = true;
                                        $mpdf->watermark_font = 'DejaVuSansCondensed';
                                        $mpdf->watermarkTextAlpha = 0.1;
                                        $mpdf->SetDisplayMode('fullpage');
                                        $mpdf->WriteHTML($html);
                                        // Output a PDF file directly to the browser
                                        $mpdf->Output("SysSoft Integra.pdf", 'I');
