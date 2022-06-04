<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\EmpresaADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = EmpresaADO::ObtenerEmpresa();

    if (!is_object($data)) {
        print json_encode(array(
            "state" => 0,
            "message" => $data
        ));
    } else {
        print json_encode(array(
            "state" => 1,
            "company" => $data,
        ));
    }
    exit();
}
