<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\VentasADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);

    $opcion = $body['opcion'];
    $busqueda = $body['busqueda'];
    $fechaInicial = $body['fechaInicial'];
    $fechaFinal = $body['fechaFinal'];
    $estado = $body['estado'];
    $posicionPagina = $body['posicionPagina'];
    $filasPorPagina = $body['filasPorPagina'];
    $ventas = VentasADO::ListVentas($opcion, $busqueda, $fechaInicial, $fechaFinal, intval($estado), $posicionPagina, $filasPorPagina);
    if (is_array($ventas)) {
        print json_encode(array(
            "state" => 1,
            "data" => $ventas[0],
            "total" => $ventas[1],
            "suma" => $ventas[2]
        ));
    } else {
        print json_encode(array(
            "state" => 0,
            "message" => "Error de conexión del servidor, intente nuevamente en un par de minutos."
        ));
    }
    exit();
}
