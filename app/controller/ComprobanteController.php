<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\ComprobanteADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "getserienumeracion") {
        $result = ComprobanteADO::GetSerieNumeracionEspecifico($_GET["idTipoDocumento"]);
        if (is_array($result)) {
            print json_encode(array(
                "estado" => 1,
                "data" => $result,
            ));
        } else {
            print json_encode(array(
                "estado" => 0,
                "message" => $result,
            ));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
}
