<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\EmpresaADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "getempresa") {
        print json_encode(EmpresaADO::ObtenerEmpresa());
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body["idEmpresa"] = $_POST["idEmpresa"];
    $body["txtNumDocumento"] = $_POST["txtNumDocumento"];
    $body["certificadoUrl"] = $_POST["certificadoUrl"];
    $body["certificadoType"] = $_POST["certificadoType"];
    $body["certificadoName"] = $_POST["certificadoType"] == 1 ? $_FILES['certificado']['name'] : '';
    $body["certificadoNameTmp"] = $_POST["certificadoType"] == 1 ? $_FILES['certificado']['tmp_name'] : '';

    $body["txtClaveCertificado"] = $_POST["txtClaveCertificado"];
    echo json_encode(EmpresaADO::CrudEmpresa($body));
    exit();
}