<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class RolADO
{

    function construct()
    {
    }

    public static function ListarRoles()
    {
        try {
            $cmdRol = Database::getInstance()->getDb()->prepare("SELECT * FROM RolTB");
            $cmdRol->execute();

            Tools::httpStatus200();
            return $cmdRol->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }
}
