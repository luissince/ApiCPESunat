<?php

set_time_limit(300); //evita el error 20 segundos de peticion

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use SysSoftIntegra\Model\VentasADO;
use SysSoftIntegra\Src\Tools;

require  "./lib/phpspreadsheet/vendor/autoload.php";
require  './../src/autoload.php';

$fechaInicio = $_GET["txtFechaInicial"];
$fechaFinal = $_GET["txtFechaFinal"];

$fechaInicioFormato = date("d/m/Y", strtotime($fechaInicio));
$fechaFinalFormato =  date("d/m/Y", strtotime($fechaFinal));
$result = VentasADO::GenerarComprobantesFacturados($fechaInicio, $fechaFinal);

$ventas = $result[0];
$empresa = $result[1];

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("Creado por SysSoftIntegra")
    ->setTitle('Reporte general de comprobantes')
    ->setSubject('Reporte')
    ->setDescription('Lista de Comprobantes Facturados ' . $fechaInicioFormato . ' al ' . $fechaFinalFormato)
    ->setKeywords('Comprobantes')
    ->setCategory('Comprobantes');

$documento->getActiveSheet()->setTitle("Comprobantes");

$documento->getActiveSheet()->getStyle('A1:M1')->applyFromArray(array(
    'borders' => array(
        'outline' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => array('argb' => '000000'),
        ),
    ),
    'fill' => array(
        'fillType' => Fill::FILL_SOLID,
        'startColor' => array('argb' => '006ac1')
    ),
    'font'  => array(
        'bold'  =>  true,
        'color' => array('argb' => 'ffffff')
    ),
    'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_CENTER
    )
));

$documento->setActiveSheetIndex(0)->mergeCells('A1:M1');
$documento->setActiveSheetIndex(0)
    ->setCellValue("A1", "REPORTE GENERAL DE COMPROBANTES FACTURADOS " .  $fechaInicioFormato . ' AL ' . $fechaFinalFormato);

$documento->getActiveSheet()->getStyle('J2:L2')->applyFromArray(array(
    'borders' => array(
        'outline' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => array('argb' => '000000'),
        ),
    ),
    'fill' => array(
        'fillType' => Fill::FILL_SOLID,
        'startColor' => array('rgb' => '808080')
    ),
    'font'  => array(
        'bold'  =>  true,
        'color' => array('argb' => 'ffffff')
    ),
    'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_CENTER
    )
));
$documento->setActiveSheetIndex(0)
    ->setCellValue("J2", "FECHA")
    ->setCellValue("K2", $fechaInicioFormato)
    ->setCellValue("L2", $fechaFinalFormato);

$documento->getActiveSheet()->getStyle('A3:M3')->applyFromArray(array(
    'fill' => array(
        'type' => Fill::FILL_SOLID,
        'color' => array('rgb' => 'E5E4E2')
    ),
    'font'  => array(
        'bold'  =>  true
    ),
    'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_CENTER
    )
));

$documento->setActiveSheetIndex(0)
    ->setCellValue("A3", "N°")
    ->setCellValue("B3", "FECHA REGISTRO")
    ->setCellValue("C3", "TIPO DOCUMT.")
    ->setCellValue("D3", "NÚMERO DOCUMENTO")
    ->setCellValue("E3", "DATOS CLIENTE")
    ->setCellValue("F3", "TIPO COMPRO.")
    ->setCellValue("G3", "SERIE")
    ->setCellValue("H3", "NUMERACIÓN")
    ->setCellValue("I3", "BASE")
    ->setCellValue("J3", "IGV")
    ->setCellValue("K3", "TOTAL")
    ->setCellValue("L3", "ANULADO")
    ->setCellValue("M3", "MENSAJE SUNAT");

$cel = 4;
foreach ($ventas as $key => $value) {
    $documento->getActiveSheet()->getStyle('A' . $cel . ':M' . $cel . '')->applyFromArray(array(
        'fill' => array(
            'type' => Fill::FILL_SOLID,
            'color' => array('rgb' => 'E5E4E2')
        ),
        'font'  => array(
            'bold'  =>  false
        ),
        'alignment' => array(
            'horizontal' => Alignment::HORIZONTAL_LEFT
        )
    ));

    if ($value["Xmlsunat"] !== "1032" || $value["Xmlsunat"] == "") {
        $documento->getActiveSheet()->getStyle('A' . $cel . ':M' . $cel . '')->applyFromArray(array(
            'font'  => array(
                'bold'  =>  false,
                'color' => array('rgb' => '000000')
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_LEFT
            )
        ));
        $documento->getActiveSheet()->getStyle("I" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("J" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("K" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("L" . $cel)->getNumberFormat()->setFormatCode('0.00');

        $documento->setActiveSheetIndex(0)
            ->setCellValue("A" . $cel,  strval($value["Id"]))
            ->setCellValue("B" . $cel, strval(Tools::formatDate($value["FechaRegistro"])))
            ->setCellValue("C" . $cel, strval($value["TipoDocumento"]))
            ->setCellValue("D" . $cel, strval($value["NumeroDocumento"]))
            ->setCellValue("E" . $cel, strval($value["Informacion"]))
            ->setCellValue("F" . $cel, strval($value["TipoComprobante"]))
            ->setCellValue("G" . $cel, strval($value["Serie"]))
            ->setCellValue("H" . $cel, strval($value["Numeracion"]))
            ->setCellValue("I" . $cel, strval(Tools::round($value["Base"])))
            ->setCellValue("J" . $cel, strval(Tools::round($value["Igv"])))
            ->setCellValue("K" . $cel, strval(Tools::round($value["Base"] + $value["Igv"])))
            ->setCellValue("L" . $cel, strval(Tools::round(0)))
            ->setCellValue("M" . $cel, strval($value["Xmldescripcion"]));
    } else {
        $documento->getActiveSheet()->getStyle('A' . $cel . ':M' . $cel . '')->applyFromArray(array(
            'font'  => array(
                'bold'  =>  false,
                'color' => array('rgb' => 'd10505')
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_LEFT
            )
        ));

        $documento->getActiveSheet()->getStyle("I" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("J" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("K" . $cel)->getNumberFormat()->setFormatCode('0.00');
        $documento->getActiveSheet()->getStyle("L" . $cel)->getNumberFormat()->setFormatCode('0.00');

        $documento->setActiveSheetIndex(0)
            ->setCellValue("A" . $cel,  strval($value["Id"]))
            ->setCellValue("B" . $cel, strval(Tools::formatDate($value["FechaRegistro"])))
            ->setCellValue("C" . $cel, strval($value["TipoDocumento"]))
            ->setCellValue("D" . $cel, strval($value["NumeroDocumento"]))
            ->setCellValue("E" . $cel, strval($value["Informacion"]))
            ->setCellValue("F" . $cel, strval($value["TipoComprobante"]))
            ->setCellValue("G" . $cel, strval($value["Serie"]))
            ->setCellValue("H" . $cel, strval($value["Numeracion"]))
            ->setCellValue("I" . $cel, strval(Tools::round($value["Base"])))
            ->setCellValue("J" . $cel, strval(Tools::round($value["Igv"])))
            ->setCellValue("K" . $cel, strval(Tools::round(0, 2)))
            ->setCellValue("L" . $cel, strval(Tools::round($value["Base"] + $value["Igv"])))
            ->setCellValue("M" . $cel, strval($value["Xmldescripcion"]));
    }
    $cel++;
}

//Ancho de las columnas
$documento->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$documento->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$documento->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$documento->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$documento->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$documento->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$documento->getActiveSheet()->getColumnDimension('M')->setWidth(40);

$nombreDelDocumento = "RUC " .  $empresa->NumeroDocumento . " - COMPROBANTES FACTURADOS DEL " . $fechaInicio . ' AL ' . $fechaFinal . ".xlsx";
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $nombreDelDocumento);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

ob_end_clean();
$writer = IOFactory::createWriter($documento, 'Xlsx');
$writer->save('php://output');
exit;
