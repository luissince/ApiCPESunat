<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\ClienteADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "allcliente") {
        print json_encode(ClienteADO::ListCliente($_GET["nombre"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    } else if ($_GET["type"] == "GetByIdCliente") {
        print json_encode(ClienteADO::GetByIdCliente($_GET["idCliente"]));
        exit();
    } else if ($_GET["type"] == "GetSearchClienteNumeroDocumento") {
        print json_encode(ClienteADO::GetSearchClienteNumeroDocumento($_GET["opcion"], $_GET["search"]));
        exit();
    } else if ($_GET["type"] == "GetListCliente") {
        print json_encode(ClienteADO::GetListCliente());
        exit();
    } else if ($_GET["type"] == "fillcliente") {
        print json_encode(ClienteADO::FillCliente($_GET["search"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "crudCliente") {
        print json_encode(ClienteADO::CrudCliente($body));
        exit();
    } else if ($body["type"] == "deleteCliente") {
        print json_encode(ClienteADO::DeleteCliente($body));
        exit();
    } else if ($body["type"] == "predeterminateCliente") {
        print json_encode(ClienteADO::PredeterminateCliente($body));
        exit();
    }
}
