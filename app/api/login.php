<?php
//dominio/app/api/logi.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\EmpleadoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $body = json_decode(file_get_contents("php://input"), true);
    $usuario = $body["usuario"];
    $clave = $body["clave"];

    $result = EmpleadoADO::Login($usuario, $clave);

    if (is_object($result)) {
        echo json_encode(array(
            "state" => 1,
            "empleado" => $result
        ));
    } else if ($result == false) {
        echo json_encode(array(
            "state" => 2,
            "message" => "El usuario o contraseña son incorrectas."
        ));
    } else {
        echo json_encode(array(
            "state" => 0,
            "message" => "Error de conexión del servidor, intente nuevamente en un par de minutos."
        ));
    }
    exit;
}
