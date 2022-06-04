<?php

include __DIR__ . '/../controller/EmpresaController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

print_r($uri);

if (!isset($uri[2]) & !isset($uri[4])) {
    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    header($protocol . " 404 Not Found");
    exit();
}
// $empresa = new EmpresaController();
// $empresa->getEmpresa();
