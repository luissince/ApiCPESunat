<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'./../../');
$dotenv->load();

/**
 * Provee las constantes para conectarse a la base de datos
 * Mysql.
 */
// define("HOSTNAME", "localhost"); // Nombre del host
// define("PORT", "1433"); // Puerto de servidor
// define("DATABASE", "SysSoftIntegra"); // Nombre de la base de datos
// define("USERNAME", "sa"); // Nombre del usuario
// define("PASSWORD", "123456"); // Nombre de la constraseña

define("HOSTNAME", $_ENV["HOSTNAME"]); // Nombre del host
define("PORT",  $_ENV["PORT"]); // Puerto de servidor
define("DATABASE", $_ENV["DATABASE"]); // Nombre de la base de datos
define("USERNAME",  $_ENV["USERNAME"]); // Nombre del usuario
define("PASSWORD",  $_ENV["PASSWORD"]); // Nombre de la constraseña