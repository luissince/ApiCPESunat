<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\ProveedorADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "allproveedores") {
        print json_encode(ProveedorADO::ListProveedor($_GET["nombre"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
    } else if ($_GET["type"] == "GetByIdProveedor") {
        print json_encode(ProveedorADO::GetByIdProveedor($_GET["idProveedor"]));
    } else if ($_GET["type"] == "fillproveedor") {
        print json_encode(ProveedorADO::FillProveedor($_GET["search"]));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "crudProveedor") {
        print json_encode(ProveedorADO::CrudProveedor($body));
    } else if ($body["type"] == "deleteProveedor") {
        print json_encode(ProveedorADO::DeleteProveedor($body));
    }
}
