<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class IngresoADO
{

    function construct()
    {
    }

    public static function ListarIngresos(int $opcion, string $buscar, string $fechaInico, string $fechaFinal, int $posicionPagina, int $filasPorPagina)
    {
        try {

            $cmdIngreso = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ingresos(?,?,?,?,?,?)}");
            $cmdIngreso->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdIngreso->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdIngreso->bindParam(3, $fechaInico, PDO::PARAM_STR);
            $cmdIngreso->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdIngreso->bindParam(5, $posicionPagina, PDO::PARAM_STR);
            $cmdIngreso->bindParam(6, $filasPorPagina, PDO::PARAM_STR);
            $cmdIngreso->execute();

            $count = 0;
            $resultIngreso = array();
            while ($row = $cmdIngreso->fetch()) {
                $count++;
                array_push($resultIngreso, array(
                    "Id" => $count + $posicionPagina,
                    "IdIngreso" => $row['IdIngreso'],
                    "Fecha" => $row['Fecha'],
                    "Hora" => $row['Hora'],
                    "Detalle" => $row['Detalle'],
                    "NumeroDocumento" => $row['NumeroDocumento'],
                    "Informacion" => $row['Informacion'],
                    "Procedencia" => $row['Procedencia'],
                    "Forma" => $row['Forma'],
                    "Monto" => floatval($row['Monto']),
                ));
            }


            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ingresos_Count(?,?,?,?)}");
            $cmdTotal->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdTotal->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdTotal->bindParam(3, $fechaInico, PDO::PARAM_STR);
            $cmdTotal->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdTotal->execute();
            $totalIngreso = $cmdTotal->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array("estado" => 1, "data" => $resultIngreso, "total" => $totalIngreso);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ListarSalidas(int $opcion, string $buscar, string $fechaInico, string $fechaFinal, int $posicionPagina, int $filasPorPagina)
    {
        try {

            $cmdIngreso = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Salidas(?,?,?,?,?,?)}");
            $cmdIngreso->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdIngreso->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdIngreso->bindParam(3, $fechaInico, PDO::PARAM_STR);
            $cmdIngreso->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdIngreso->bindParam(5, $posicionPagina, PDO::PARAM_STR);
            $cmdIngreso->bindParam(6, $filasPorPagina, PDO::PARAM_STR);
            $cmdIngreso->execute();

            $count = 0;
            $resultIngreso = array();
            while ($row = $cmdIngreso->fetch()) {
                $count++;
                array_push($resultIngreso, array(
                    "Id" => $count + $posicionPagina,
                    "IdIngreso" => $row['IdIngreso'],
                    "Fecha" => $row['Fecha'],
                    "Hora" => $row['Hora'],
                    "Detalle" => $row['Detalle'],
                    "NumeroDocumento" => $row['NumeroDocumento'],
                    "Informacion" => $row['Informacion'],
                    "Procedencia" => $row['Procedencia'],
                    "Forma" => $row['Forma'],
                    "Monto" => floatval($row['Monto']),
                ));
            }


            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Salidas_Count(?,?,?,?)}");
            $cmdTotal->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdTotal->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdTotal->bindParam(3, $fechaInico, PDO::PARAM_STR);
            $cmdTotal->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdTotal->execute();
            $totalIngreso = $cmdTotal->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array("estado" => 1, "data" => $resultIngreso, "total" => $totalIngreso);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function InsertIngreso($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("INSERT INTO 
            IngresoTB(
            IdProcedencia,
            IdUsuario,
            IdCliente,
            Detalle,
            Procedencia,
            Fecha,
            Hora,
            Forma,
            Monto)
            VALUES(?,?,?,?,?,?,?,?,?)");
            $cmdValidate->execute(array(
                $body["idProcedencia"],
                $body["idUsuario"],
                $body["idCliente"],
                $body["detalle"],
                $body["procedencia"],
                $body["fecha"],
                $body["hora"],
                $body["forma"],
                $body["monto"],

            ));

            Database::getInstance()->getDb()->commit();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "OK");

            return  "Se registró correctamente el Ingreso.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ReporteGeneralIngresosEgresos(string $fechaInico, string $fechaFinal, int $empleado, string $idUsuario)
    {
        try {
            $cmdIngreso = Database::getInstance()->getDb()->prepare("{call Sp_Reporte_General_Ingresos_Egresos(?,?,?,?)}");
            $cmdIngreso->bindParam(1, $fechaInico, PDO::PARAM_STR);
            $cmdIngreso->bindParam(2, $fechaFinal, PDO::PARAM_INT);
            $cmdIngreso->bindParam(3, $empleado, PDO::PARAM_STR);
            $cmdIngreso->bindParam(4, $idUsuario, PDO::PARAM_STR);
            $cmdIngreso->execute();

            return   $cmdIngreso->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {

            return $ex->getMessage();
        }
    }
}
