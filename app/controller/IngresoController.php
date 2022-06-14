<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\IngresoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "listaIngreso") {
        echo json_encode(IngresoADO::ListarIngresos($_GET["opcion"], $_GET["buscar"], $_GET["fechaInicio"], $_GET["fechaFin"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    } else if ($_GET["type"] == "listaSalidas") {
        echo json_encode(IngresoADO::ListarSalidas($_GET["opcion"], $_GET["buscar"], $_GET["fechaInicio"], $_GET["fechaFin"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "insert") {
        echo json_encode(IngresoADO::InsertIngreso($body));
        exit();
    }
}
