<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\MovimientoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "listipomovimiento") {
        print json_encode(MovimientoADO::ListarTipoMovimiento($_GET["ajuste"], $_GET["all"]));
        exit();
    } else if ($_GET["type"] == "listforidmovimiento") {
        print json_encode(MovimientoADO::ObtenerMovimientoInventarioById($_GET["idMovimiento"]));
        exit();
    } else if ($_GET["type"] == "listmovimiento") {
        $opcion = $_GET["opcion"];
        $movimiento = $_GET["movimiento"];
        $fechaInicial = $_GET["fechaInicial"];
        $fechaFinal = $_GET["fechaFinal"];
        $posicionPagina = $_GET['posicionPagina'];
        $filasPorPagina = $_GET['filasPorPagina'];
        print json_encode(MovimientoADO::ListarMoviminentos($opcion, $movimiento, $fechaInicial, $fechaFinal, $posicionPagina, $filasPorPagina));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);

    if ($body["type"] == "insertmovimiento") {
        print json_encode(MovimientoADO::RegistrarMovimientoInventario($body));
        exit();
    } else if ($body["type"] == "restarkardex") {
        print json_encode(MovimientoADO::RestablecerInventario($body));
        exit();
    } else if ($body["type"] == "anularmovimiento") {
        print json_encode(MovimientoADO::CancelarMovimiento($body));
        exit();
    }
}
