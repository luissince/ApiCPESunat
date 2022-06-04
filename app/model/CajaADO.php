<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use Exception;

require_once __DIR__ . './../database/DataBaseConexion.php';


class CajaADO
{

    function construct()
    {
    }

    public static function ReporteGeneralMovimientoCaja(string $fechaInicio,string $fechaFinal,int $usuario,string $idUsuario){
        try{
            $cmdMovimiento = Database::getInstance()->getDb()->prepare("{call Sp_Reporte_General_Movimiento_Caja(?,?,?,?)}");
            $cmdMovimiento->bindParam(1, $fechaInicio, PDO::PARAM_STR);
            $cmdMovimiento->bindParam(2, $fechaFinal, PDO::PARAM_INT);
            $cmdMovimiento->bindParam(3, $usuario, PDO::PARAM_STR);
            $cmdMovimiento->bindParam(4, $idUsuario, PDO::PARAM_STR);
            $cmdMovimiento->execute();
            return $cmdMovimiento->fetchAll(PDO::FETCH_OBJ);
        }catch(Exception $ex){
            return $ex->getMessage();
        }
    }

}
