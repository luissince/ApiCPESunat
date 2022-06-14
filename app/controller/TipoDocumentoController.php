<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\TipoDocumentoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "all") {
        print json_encode(TipoDocumentoADO::ListTipoDocumento($_GET["opcion"], $_GET["search"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
    } else if ($_GET["type"] == "getdocumentocomboboxventas") {
        print json_encode(TipoDocumentoADO::GetDocumentoCombBoxVentas());
    } else if ($_GET["type"] == "GetDocumentoCombBoxNotaCredito") {
        print json_encode(TipoDocumentoADO::GetDocumentoCombBoxNotaCredito());
    } else if ($_GET["type"] == "getdocumentofacturados") {
        print json_encode(TipoDocumentoADO::GetDocumentoFacturados());
    } else if ($_GET["type"] == "getbyid") {
        print json_encode(TipoDocumentoADO::ObtenerTipoDocumentoById($_GET["idTipoDocumento"]));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "crud") {
        print json_encode(TipoDocumentoADO::CrudTipoDocumento($body));
    } else if ($body["type"] == "remove") {
        print json_encode(TipoDocumentoADO::DeleteTipoDocumento($body));
    }
}
