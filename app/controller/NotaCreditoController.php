<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\NotaCreditoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "all") {
        $opcion = $_GET["opcion"];
        $buscar = $_GET["search"];
        $fechaInicio = $_GET["fechaInicial"];
        $fechaFinal = $_GET["fechaFinal"];
        $posicionPagina = $_GET["posicionPagina"];
        $filasPorPagina = $_GET["filasPorPagina"];
        print json_encode(NotaCreditoADO::ListaNotaCredito($opcion, $buscar, $fechaInicio, $fechaFinal, $posicionPagina, $filasPorPagina));
        exit();
    } else if ($_GET["type"] == "notacreditotalle") {
        print json_encode(NotaCreditoADO::ListarDetalleNotaCredito($_GET["idNotaCredito"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
}
