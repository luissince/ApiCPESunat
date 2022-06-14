<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\SuministrosADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($_GET["type"] == "listainventario") {
        $producto = $_GET['producto'];
        $existencia = $_GET['existencia'];
        $nombre = $_GET['nombre'];
        $opcion = $_GET['opcion'];
        $categoria = $_GET['categoria'];
        $marca = $_GET['marca'];
        $idAlmacen = $_GET['idAlmacen'];
        $posicionPagina = $_GET['posicionPagina'];
        $filasPorPagina = $_GET['filasPorPagina'];
        //$search = $_GET['search'];     
        print json_encode(SuministrosADO::ListarInventario($producto, $existencia, $nombre, $opcion, $categoria, $marca, $idAlmacen, $posicionPagina, $filasPorPagina));
        exit();
    } else if ($_GET["type"] == "destacados") {
        print json_encode(SuministrosADO::ListarProductosDestacos($_GET['posicionPagina'], $_GET['filasPorPagina']));
        exit();
    } else if ($_GET["type"] == "catalogo") {
        print json_encode(SuministrosADO::ListarSuministroCatalogo($_GET['opcion'], $_GET['buscar'], $_GET['categoria'], $_GET['marca'], $_GET['posicionPagina'], $_GET['filasPorPagina']));
        exit();
    } else if ($_GET["type"] == "modalproductos") {
        print json_encode(SuministrosADO::ListarSuministroView($_GET["tipo"], $_GET["value"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    } else if ($_GET["type"] == "kardexlista") {
        print json_encode(SuministrosADO::KardexSuministroById(0, $_GET["idSuministro"], $_GET["idAlmacen"]));
        exit();
    } else if ($_GET["type"] == "getproducto") {
        print json_encode(SuministrosADO::ObtenerSuministroById($_GET["idSuministro"]));
        exit();
    } else if ($_GET["type"] === "listaproductos") {
        $opcion = $_GET["opcion"];
        $clave = $_GET["clave"];
        $nombre = $_GET["nombre"];
        $categoria = $_GET["categoria"];
        $marca = $_GET["marca"];
        $posicionPagina = $_GET["posicionPagina"];
        $filasPorPagina = $_GET["filasPorPagina"];
        print json_encode(SuministrosADO::ListarSuministros($opcion, $clave, $nombre, $categoria, $marca, $posicionPagina, $filasPorPagina));
        exit();
    } else if ($_GET["type"] === "impuestos") {
        print json_encode(SuministrosADO::ListarImpuesto());
        exit();
    } else if ($_GET["type"] === "detalles") {
        print json_encode(SuministrosADO::ObtenerDetalleId("4", $_GET["mantenimiento"], $_GET["nombre"]));
        exit();
    } else if ($_GET["type"] == "getsuministroformovimiento") {
        print json_encode(SuministrosADO::ObtenerSuministroForMovimiento($_GET["idSuministro"]));
        exit();
    } else if ($_GET["type"] == "listallnegativo") {
        print json_encode(SuministrosADO::ListarSuministroNegativos());
        exit();
    } else if ($_GET["type"] == "fillSuministro") {
        print json_encode(SuministrosADO::Get_Suministro_By_Search($_GET["search"]));
        exit();
    }else if($_GET["type"] == "fillsuministrosearch"){
        print json_encode(SuministrosADO::FillSuministro($_GET["search"]));
        exit();
    }
     else if ($_GET["type"] == "valueSuministro") {
        print json_encode(SuministrosADO::Get_Suministro_By_Value($_GET["value"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);

    if ($body["type"] == "updatesuministro") {
        print json_encode(SuministrosADO::ActualizarSuministro($body));
        exit();
    } else if ($body["type"] == "insertsuministro") {
        print json_encode(SuministrosADO::RegistrarSuministro($body));
        exit();
    } else if ($body["type"] == "removesuministro") {
        print json_encode(SuministrosADO::EliminarProductoById($body["IdSuministro"]));
        exit();
    }
}
