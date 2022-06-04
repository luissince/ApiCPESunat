<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';

class MonedaADO
{

    function __construct()
    {
    }

    public static function Listar_Monedas(int $opcion, string $buscar, int $posicionPagina, int $filasPorPagina)
    {
        try {

            $cmdMoneda = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Monedas(?,?,?,?)}");
            $cmdMoneda->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdMoneda->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdMoneda->bindParam(3, $posicionPagina, PDO::PARAM_INT);
            $cmdMoneda->bindParam(4, $filasPorPagina, PDO::PARAM_INT);
            $cmdMoneda->execute();

            $arrayMoneda = array();
            $count = 0;
            while ($row = $cmdMoneda->fetch()) {
                $count++;
                array_push($arrayMoneda, array(
                    "Id" => $count,
                    "IdMoneda" => $row['IdMoneda'],
                    "Nombre" => $row['Nombre'],
                    "Abreviado" => $row['Abreviado'],
                    "Simbolo" => $row['Simbolo'],
                    "TipoCambio" => $row['TipoCambio'],
                    "Predeterminado" => $row['Predeterminado']
                ));
            }

            $cmdMoneda = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Monedas_Count(?,?)}");
            $cmdMoneda->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdMoneda->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdMoneda->execute();
            $resultTotal = $cmdMoneda->fetchColumn();

            Tools::httpStatus200();

            return array("data" => $arrayMoneda, "total" => $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function GetMonedasComboBox()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            IdMoneda,
            Nombre,
            Simbolo,
            Predeterminado 
            FROM MonedaTB");
            $comando->execute();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return $comando->fetchAll(PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ObtenerMonedaById($idMoneda)
    {
        try {
            $cmdMoneda = Database::getInstance()->getDb()->prepare("SELECT 
            IdMoneda,
            Nombre,
            Abreviado,
            Simbolo,
            TipoCambio
            FROM 
            MonedaTB 
            WHERE IdMoneda = ?");
            $cmdMoneda->bindParam(1, $idMoneda, PDO::PARAM_STR);
            $cmdMoneda->execute();

            return $cmdMoneda->fetchObject();
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function CrudMoneda($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM MonedaTB WHERE IdMoneda = ?");
            $cmdValidate->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT Nombre FROM MonedaTB WHERE IdMoneda <> ? AND Nombre = ?");
                $cmdValidate->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
                $cmdValidate->bindParam(2, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "Hay una moneda con el mismo nombre.";
                } else {
                    $cmdMoneda = Database::getInstance()->getDb()->prepare("UPDATE MonedaTB SET 
                    Nombre=?,
                    Abreviado=?,
                    Simbolo=?,
                    TipoCambio=?
                    WHERE IdMoneda = ?");
                    $cmdMoneda->execute(array(
                        $body["Nombre"],
                        $body["Abreviado"],
                        $body["Simbolo"],
                        $body["TipoCambio"],
                        $body["IdMoneda"]
                    ));

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 201 . ' ' . "Created");
                    Database::getInstance()->getDb()->commit();

                    return "Se actualizó correctamente la moneda.";
                }
            } else {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT Nombre FROM MonedaTB WHERE Nombre = ?");
                $cmdValidate->bindParam(1, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "Hay una moneda con el mismo nombre.";
                } else {
                    $cmdMoneda = Database::getInstance()->getDb()->prepare("INSERT INTO MonedaTB(
                        Nombre,
                        Abreviado,
                        Simbolo,
                        TipoCambio,
                        Predeterminado,
                        Sistema) 
                        VALUES(?,?,?,?,?,?)");
                    $cmdMoneda->execute(array(
                        $body["Nombre"],
                        $body["Abreviado"],
                        $body["Simbolo"],
                        $body["TipoCambio"],
                        false,
                        false
                    ));

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 201 . ' ' . "Created");
                    Database::getInstance()->getDb()->commit();

                    return "Se registró correctamente la moneda.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");
            return $ex->getMessage();
        }
    }

    public static function RemoverMoneda($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM MonedaTB WHERE IdMoneda = ? AND Predeterminado = 1");
            $cmdValidate->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 400 . ' ' . "Bad Request");

                return "No se puedo eliminar la moneda ya que está predeterminado.";
            } else {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM VentaTB WHERE Moneda = ?");
                $cmdValidate->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "No se puedo eliminar ya que está ligado a una venta.";
                } else {
                    $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CompraTB WHERE TipoMoneda = ?");
                    $cmdValidate->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
                    $cmdValidate->execute();
                    if ($cmdValidate->fetch()) {
                        Database::getInstance()->getDb()->rollback();
                        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                        header($protocol . ' ' . 400 . ' ' . "Bad Request");

                        return "No se puedo eliminar ya que está ligado a un compra.";
                    } else {
                        $cmdMoneda = Database::getInstance()->getDb()->prepare("DELETE FROM MonedaTB WHERE IdMoneda = ?");
                        $cmdMoneda->bindParam(1, $body["IdMoneda"], PDO::PARAM_INT);
                        $cmdMoneda->execute();

                        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                        header($protocol . ' ' . 201 . ' ' . "Created");
                        Database::getInstance()->getDb()->commit();

                        return "Se eliminó correctamente la moneda.";
                    }
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }
}
