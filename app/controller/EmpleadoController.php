<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

use SysSoftIntegra\Model\EmpleadoADO;

require __DIR__ . './../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["type"] == "all") {
        print json_encode(EmpleadoADO::ListEmpleados($_GET["opcion"], $_GET["search"], $_GET["posicionPagina"], $_GET["filasPorPagina"]));
        exit();
    } else if ($_GET["type"] == "getid") {
        print json_encode(EmpleadoADO::ObtenerEmpleadoById($_GET["idEmpleado"]));
        exit();
    } else if ($_GET["type"] == "login") {
        $usuario = $_GET['usuario'];
        $clave = $_GET['clave'];
        $result = EmpleadoADO::Login($usuario, $clave);
        if (is_object($result)) {
            session_start();
            $_SESSION["IdEmpleado"] = $result->IdEmpleado;
            $_SESSION["Nombres"] = $result->Nombres;
            $_SESSION["Apellidos"] = $result->Apellidos;
            $_SESSION["Estado"] = $result->Estado;
            $_SESSION["Rol"] = $result->Rol;
            $_SESSION["RolName"] = $result->RolName;
            echo json_encode(array(
                "estado" => 1,
                "empleado" => $result
            ));
        } else if ($result == false) {
            echo json_encode(array(
                "estado" => 2,
                "message" => "Usuario o contraseña incorrecta."
            ));
        } else {
            echo json_encode(array(
                "estado" => 0,
                "message" => $result
            ));
        }
        exit();
    } else if ($_GET["type"] == "predeterminate") {
        print json_encode(EmpleadoADO::GetClientePredetermined());
        exit();
    } else if ($_GET["type"] == "GetListEmpleados") {
        print json_encode(EmpleadoADO::GetListEmpleados());
        exit();
    } else if ($_GET["type"] == "fillempleado") {
        print json_encode(EmpleadoADO::FillEmpleados($_GET["search"]));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);
    if ($body["type"] == 'crud') {
        print json_encode(EmpleadoADO::CrudEmpleado($body));
        exit();
    } else if ($body["type"] == "delete") {
        print json_encode(EmpleadoADO::DeleteEmpleado($body));
        exit();
    }
}
