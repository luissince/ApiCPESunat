<?php

require_once('./app/sunat/lib/phpdotenv/vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

var_dump($_ENV);


echo "Api Sunat";