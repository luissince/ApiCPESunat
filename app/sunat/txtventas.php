<?php

set_time_limit(300);

use SysSoftIntegra\Model\VentasADO;

require  "/lib/phpspreadsheet/vendor/autoload.php";
require  './../src/autoload.php';

$fechaInicio = $_GET["txtFechaInicial"];
$fechaFinal = $_GET["txtFechaFinal"];

$result = VentasADO::GetReporteGeneralVentas($fechaInicio, $fechaFinal, intval($_GET["facturado"]));
$ventas = $result[0];
$empresa = $result[1];

$montotatal = 0;
$textsunat = "";

$countRows = 0;

foreach ($ventas as $key => $value) {
    $date = date("d/m/Y", strtotime($value["FechaVenta"]));
    $textsunat .= $empresa->NumeroDocumento . "|" .
        trim($value["TipoComprobante"]) . "|" .
        trim($value["Serie"]) . "|" .
        trim($value["Numeracion"]) . "|" .
        $date . "|" .
        number_format(floatval(($value["Base"] + $value["Igv"])), 2, '.', '') . "\n";
    $countRows++;
}

$filename = "FACTURAS " . $empresa->NumeroDocumento . " - " . $fechaInicio . " AL " . $fechaFinal . ".txt";

$fp = fopen($filename, "wb");
fwrite($fp, $textsunat);
fclose($fp);

$file_to_download = $filename;
$client_file = $filename;

$download_rate = 200; // 200Kb/s

$f = null;

try {
    if (!file_exists($file_to_download)) {
        throw new Exception('El archivo ' . $file_to_download . ' no existe');
    }

    if (!is_file($file_to_download)) {
        throw new Exception('El archivo ' . $file_to_download . ' no es valido');
    }

    header('Cache-control: private');
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($file_to_download));
    header('Content-Disposition: filename=' . $client_file);

    flush();
    $f = fopen($file_to_download, 'r');
    while (!feof($f)) {
        print fread($f, round($download_rate * 1024));
        flush();
        sleep(1);
    }
} catch (\Throwable $e) {
    echo $e->getMessage();
} finally {
    if ($f) {
        fclose($f);
    }
    if (file_exists($file_to_download)) {
        unlink($file_to_download);
    }
}
