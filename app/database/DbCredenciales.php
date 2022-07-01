<?php

$json =  file_get_contents(__DIR__ . "./config.json");
$object = (object) json_decode($json, true);
/**
 * Provee las constantes para conectarse a la base de datos
 * Mysql.
 */
// define("HOSTNAME", "localhost"); // Nombre del host
// define("PORT", "1433"); // Puerto de servidor
// define("DATABASE", "SysSoftIntegra"); // Nombre de la base de datos
// define("USERNAME", "sa"); // Nombre del usuario
// define("PASSWORD", "123456"); // Nombre de la constraseña

define("HOSTNAME", $object->HOSTNAME); // Nombre del host
define("PORT", $object->PORT); // Puerto de servidor
define("DATABASE", $object->DATABASE); // Nombre de la base de datos
define("USERNAME", $object->USERNAME); // Nombre del usuario
define("PASSWORD", $object->PASSWORD); // Nombre de la constraseña