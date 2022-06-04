<?php
set_time_limit(300);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Src\SoapResult;
use SysSoftIntegra\Src\Sunat;
use SysSoftIntegra\Model\VentasADO;

require __DIR__ . './../../src/autoload.php';

$fechaInicial = $_GET['fechaInicial'];
$fechaFinal = $_GET['fechaFinal'];
$ventas = VentasADO::ListComprobantes($fechaInicial, $fechaFinal);

if (is_array($ventas)) {
    $arraysunat = array();
    $count = 0;
    foreach ($ventas as $value) {
        $arguments = [
            $value["Empresa"]->NumeroDocumento,
            $value["TipoComprobante"],
            $value["Serie"],
            intval($value["Numeracion"])
        ];
        $get = [
            'rucSol' => $value["Empresa"]->NumeroDocumento,
            'userSol' =>  $value["Empresa"]->UsuarioSol,
            'passSol' =>  $value["Empresa"]->ClaveSol,
            'ruc' =>  $value["Empresa"]->NumeroDocumento,
            'tipo' => $value["TipoComprobante"],
            'serie' => $value["Serie"],
            'numero' => intval($value["Numeracion"])
        ];

        $soapResult = new SoapResult('../../resources/wsdl/billConsultService.wsdl', implode('-', $arguments));
        $soapResult->sendGetStatusValid(Sunat::xmlGetValidService($get));

        $code = "-";
        $message = "-";
        $count++;
        if ($soapResult->isSuccess()) {
            if ($soapResult->isAccepted()) {
                $code = $soapResult->getCode();
                $message = $soapResult->getMessage();
            } else {
                $code = $soapResult->getCode();
                $message = $soapResult->getMessage();
            }
        } else {
            $code = $soapResult->getCode();
            $message = $soapResult->getMessage();
        }

        array_push($arraysunat, array(
            "id" => $count,
            "NumeroDocumento" => $value["Empresa"]->NumeroDocumento,
            "Fecha" => $value["Fecha"],
            "Hora" => $value["Hora"],
            "Serie" => $value["Serie"],
            "Numeracion" => $value["Numeracion"],
            "Nombre" => $value["Nombre"],
            "TipoComprobante" => $value["TipoComprobante"],
            "Total" => floatval($value["Total"]),
            "Estado" =>  $code,
            "Mensaje" => $message,
        ));
    }
    print json_encode(array(
        "estado" => 1,
        "data" =>  $arraysunat
    ));
} else {
    print json_encode(array(
        "estado" => 2,
        "message" => $ventas
    ));
}


// $get['rucSol'] = $_GET["rucSol"];
// $get['userSol'] = $_GET["userSol"];
// $get['passSol'] = $_GET["passSol"];
// $get['ruc'] = $_GET["ruc"];
// $get['tipo'] = $_GET["tipo"];
// $get['serie'] = $_GET["serie"];
// $get['numero'] = $_GET["numero"];
// $get['cdr'] = $_GET["cdr"];

// $fileDir = __DIR__ . '/../../files';

// if (!file_exists($fileDir)) {
//     mkdir($fileDir, 0777, true);
// }

// $soapResult = new SoapResult('../../resources/wsdl/billConsultService.wsdl', implode('-', $arguments));
// $soapResult->sendGetStatusValid(Sunat::xmlGetValidService($get));

// if ($soapResult->isSuccess()) {
//     if ($soapResult->isAccepted()) {
//         echo json_encode(array(
//             "state" => $soapResult->isSuccess(),
//             "accepted" => $soapResult->isAccepted(),
//             "code" => $soapResult->getCode(),
//             "message" => $soapResult->getMessage()
//         ));
//     } else {
//         echo json_encode(array(
//             "state" => $soapResult->isSuccess(),
//             "accepted" => $soapResult->isAccepted(),
//             "code" => $soapResult->getCode(),
//             "message" => $soapResult->getMessage(),
//         ));
//     }
// } else {
//     echo json_encode(array(
//         "state" => $soapResult->isSuccess(),
//         "code" => $soapResult->getCode(),
//         "accepted" => $soapResult->isAccepted(),
//         "message" => $soapResult->getMessage()
//     ));
// }
