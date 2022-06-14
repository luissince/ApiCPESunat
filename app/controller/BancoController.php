<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\BancoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "all") {
        print json_encode(BancoADO::Listar_Bancos($_GET["buscar"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    } else if ($_GET["type"] == "getid") {
        print json_encode(BancoADO::Obtener_Banco_Por_Id($_GET["idBanco"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "crud") {
        print json_encode(BancoADO::Proceso_Banco($body));
        exit();
    } else if ($body["type"] == "delete") {
        print json_encode(BancoADO::Deleted_Banco($body));
        exit();
    }
}
