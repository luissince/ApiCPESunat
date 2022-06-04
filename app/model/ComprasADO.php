<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class ComprasADO{

    function construct()
    {
    }

    public static function Listar_Compras(int $opcion,string $buscar,string $fechaInicio,string $fechaFin,int $comprobante,int $estado,int $posicionPagina,int $filasPorPagina){
        try{
            $cmdCompra = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Compras(?,?,?,?,?,?,?,?)}");
            $cmdCompra->bindParam(1,$opcion,PDO::PARAM_INT);
            $cmdCompra->bindParam(2,$buscar,PDO::PARAM_STR);
            $cmdCompra->bindParam(3,$fechaInicio,PDO::PARAM_STR);
            $cmdCompra->bindParam(4,$fechaFin,PDO::PARAM_STR);
            $cmdCompra->bindParam(5,$comprobante,PDO::PARAM_INT);
            $cmdCompra->bindParam(6,$estado,PDO::PARAM_INT);
            $cmdCompra->bindParam(7,$posicionPagina,PDO::PARAM_INT);
            $cmdCompra->bindParam(8,$filasPorPagina,PDO::PARAM_INT);
            $cmdCompra->execute();

            $count = 0;
            $arrayCompra = array();
            while($row = $cmdCompra->fetch()){
                $count++;
                array_push($arrayCompra, array(
                    "Id" =>$count + $posicionPagina,
                    "IdCompra" => $row["IdCompra"],
                    "FechaCompra" => $row["FechaCompra"],
                    "HoraCompra" => $row["HoraCompra"],
                    "Comprobante" => $row["Comprobante"],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],

                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "RazonSocial" => $row["RazonSocial"],

                    "TipoCompra" => $row["TipoCompra"],
                    "Tipo" => $row["Tipo"],

                    "EstadoCompra" => $row["EstadoCompra"],
                    "Estado" => $row["Estado"],

                    "Moneda" => $row["Moneda"],
                    "Simbolo" => $row["Simbolo"],
                    
                    "Total" =>floatval($row["Total"]),
                ));
            }

            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Compras_Count(?,?,?,?,?,?)}");
            $cmdTotal->bindParam(1,$opcion,PDO::PARAM_INT);
            $cmdTotal->bindParam(2,$buscar,PDO::PARAM_STR);
            $cmdTotal->bindParam(3,$fechaInicio,PDO::PARAM_STR);
            $cmdTotal->bindParam(4,$fechaFin,PDO::PARAM_STR);
            $cmdTotal->bindParam(5,$comprobante,PDO::PARAM_INT);
            $cmdTotal->bindParam(6,$estado,PDO::PARAM_INT);
            $cmdTotal->execute();
            $resultTotal = $cmdTotal->fetchColumn();

            Tools::httpStatus200();

            return array("data"=>$arrayCompra, "total" => $resultTotal);
        }catch(Exception $ex){
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function Obtener_Compra_ById(string $idCompra){
        try{

        }catch(Exception $ex){
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

}