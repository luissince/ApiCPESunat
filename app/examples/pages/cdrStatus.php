<?php
set_time_limit(300);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Src\SoapResult;
use SysSoftIntegra\Src\Sunat;

require __DIR__ . './../../src/autoload.php';

$get['rucSol'] = $_GET["rucSol"];
$get['userSol'] = $_GET["userSol"];
$get['passSol'] = $_GET["passSol"];
$get['ruc'] = $_GET["ruc"];
$get['tipo'] = $_GET["tipo"];
$get['serie'] = $_GET["serie"];
$get['numero'] = $_GET["numero"];
$get['cdr'] = $_GET["cdr"];

$fileDir = __DIR__ . '/../../files';

if (!file_exists($fileDir)) {
    mkdir($fileDir, 0777, true);
}

$arguments = [
    $get["ruc"],
    $get["tipo"],
    $get["serie"],
    intval($get["numero"])
];

$soapResult = new SoapResult('../../resources/wsdl/billConsultService.wsdl', implode('-', $arguments));


if ($get['cdr'] !== "") {
    $soapResult->sendGetStatusCdr(Sunat::xmlGetStatusCdr($get));
    if ($soapResult->isSuccess()) {
        if ($soapResult->isAccepted()) {
            echo json_encode(array(
                "state" => $soapResult->isSuccess(),
                "accepted" => $soapResult->isAccepted(),
                "code" => $soapResult->getCode(),
                "message" => $soapResult->getMessage(),
                "description" => $soapResult->getDescription(),
                "file" => $soapResult->getFile(),
            ));
        } else {
            echo json_encode(array(
                "state" => $soapResult->isSuccess(),
                "accepted" => $soapResult->isAccepted(),
                "code" => $soapResult->getCode(),
                "message" => $soapResult->getMessage(),
            ));
        }
    } else {
        echo json_encode(array(
            "state" => $soapResult->isSuccess(),
            "code" => $soapResult->getCode(),
            "accepted" => $soapResult->isAccepted(),
            "message" => $soapResult->getMessage()
        ));
    }
} else {
    $soapResult->sendGetStatusValid(Sunat::xmlGetValidService($get));
    if ($soapResult->isSuccess()) {
        if ($soapResult->isAccepted()) {
            echo json_encode(array(
                "state" => $soapResult->isSuccess(),
                "accepted" => $soapResult->isAccepted(),
                "code" => $soapResult->getCode(),
                "message" => $soapResult->getMessage()
            ));
        } else {
            echo json_encode(array(
                "state" => $soapResult->isSuccess(),
                "accepted" => $soapResult->isAccepted(),
                "code" => $soapResult->getCode(),
                "message" => $soapResult->getMessage(),
            ));
        }
    } else {
        echo json_encode(array(
            "state" => $soapResult->isSuccess(),
            "code" => $soapResult->getCode(),
            "accepted" => $soapResult->isAccepted(),
            "message" => $soapResult->getMessage()
        ));
    }
}
