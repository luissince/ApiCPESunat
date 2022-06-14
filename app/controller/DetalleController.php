<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\DetalleADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "detailname") {
        print json_encode(DetalleADO::GetDetailIdName(array($_GET["value1"], $_GET["value2"], $_GET["value3"])));
        exit();
    } else if ($_GET["type"] == "detailid") {
        print json_encode(DetalleADO::GetDetailId($_GET["value"]));
        exit();
    } else if ($_GET["type"] == "categoriaproducto") {
        print json_encode(DetalleADO::CategoriasParaProductos());
        exit();
    } else if ($_GET["type"] == "marcaproducto") {
        print json_encode(DetalleADO::MarcasParaProductos());
        exit();
    } else if ($_GET["type"] == "listmantenimiento") {
        print json_encode(DetalleADO::Listar_Mantenimiento($_GET["value"]));
        exit();
    } else if ($_GET["type"] == "listdetalle") {
        print json_encode(DetalleADO::Listar_Detalle_ById($_GET["idMantenimiento"], $_GET["search"]));
        exit();
    } else if ($_GET["type"] == "fillunidad") {
        print json_encode(DetalleADO::FillDetalleUnidadMedida($_GET["search"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == "crud") {
        print json_encode(DetalleADO::CrudDetalle($body));
        exit();
    } else if ($body["type"] == "delete") {
        print json_encode(DetalleADO::DeleteDetalle($body));
        exit();
    }
}
