<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class ProveedorADO
{

    function construct()
    {
    }

    public static function ListProveedor($buscar, $posicionPagina, $filasPorPagina)
    {
        try {
            $comandoVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Proveedor(?,?,?)}");
            $comandoVenta->bindParam(1, $buscar, PDO::PARAM_STR);
            $comandoVenta->bindParam(2, $posicionPagina, PDO::PARAM_INT);
            $comandoVenta->bindParam(3, $filasPorPagina, PDO::PARAM_INT);
            $comandoVenta->execute();
            $arrayVenta = array();
            $count = 0;
            while ($row = $comandoVenta->fetch()) {
                $count++;
                array_push($arrayVenta, array(
                    "Id" => $count + $posicionPagina,
                    "IdProveedor" => $row["IdProveedor"],
                    "Documento" => $row["Documento"],
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "RazonSocial" => $row["RazonSocial"],
                    "NombreComercial" => $row["NombreComercial"],
                    "Telefono" => $row["Telefono"],
                    "Celular" => $row["Celular"],
                    "Representante" => $row["Representante"],
                    "Direccion" => $row["Direccion"],
                    "Estado" => $row["Estado"],
                ));
            }

            $comandoTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Proveedor_Count(?)}");
            $comandoTotal->bindParam(1, $buscar, PDO::PARAM_STR);
            $comandoTotal->execute();
            $resultTotal = $comandoTotal->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "data" => $arrayVenta,
                "total" => $resultTotal
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function GetByIdProveedor($idProveedor)
    {
        try {
            $cmdProveedor = Database::getInstance()->getDb()->prepare("{CALL Sp_Get_Proveedor_By_Id(?)}");
            $cmdProveedor->bindParam(1, $idProveedor, PDO::PARAM_STR);
            $cmdProveedor->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return  $cmdProveedor->fetchObject();
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return  $ex->getMessage();
        }
    }

    public static function CrudProveedor($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT IdProveedor FROM ProveedorTB WHERE IdProveedor = ?");
            $cmdValidate->bindParam(1, $body["IdProveedor"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT NumeroDocumento FROM ProveedorTB WHERE IdProveedor <> ? AND NumeroDocumento = ?");
                $cmdValidate->bindParam(1, $body["IdProveedor"], PDO::PARAM_STR);
                $cmdValidate->bindParam(2, $body["NumeroDocumento"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    return array("estado" => 2, "message" => "No se puede haber 2 proveedores con el mismo número de documento.");
                } else {

                    $cmdProveedor = Database::getInstance()->getDb()->prepare("UPDATE ProveedorTB 
                    SET 
                    TipoDocumento=?,
                    NumeroDocumento=?,
                    RazonSocial=UPPER(?),
                    NombreComercial=UPPER(?),
                    Ambito=?,
                    Estado=?,
                    Telefono=?,
                    Celular=?,
                    Email=?,
                    PaginaWeb=?,
                    Direccion=?,
                    Representante=? 
                    WHERE IdProveedor=?");
                    $cmdProveedor->execute(array(
                        $body["TipoDocumento"],
                        $body["NumeroDocumento"],
                        $body["RazonSocial"],
                        $body["NombreComercial"],
                        $body["Ambito"],
                        $body["Estado"],
                        $body["Telefono"],
                        $body["Celular"],
                        $body["Email"],
                        $body["PaginaWeb"],
                        $body["Direccion"],
                        $body["Representante"],
                        $body["IdProveedor"]
                    ));
                    Database::getInstance()->getDb()->commit();
                    return array("estado" => 1, "message" => "Se actualizó correctamente el proveedor.");
                }
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT NumeroDocumento FROM ProveedorTB WHERE NumeroDocumento = ?");
                $cmdValidate->bindParam(1, $body["NumeroDocumento"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    return array("estado" => 2, "message" => "No se puede haber 2 proveedores con el mismo número de documento.");
                } else {

                    $codProveedor = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Proveedor_Codigo_Alfanumerico();");
                    $codProveedor->execute();
                    $idProveedor = $codProveedor->fetchColumn();

                    $cmdProveedor = Database::getInstance()->getDb()->prepare("INSERT INTO
                    ProveedorTB(
                    IdProveedor,
                    TipoDocumento,
                    NumeroDocumento,
                    RazonSocial,
                    NombreComercial,
                    Ambito,
                    Estado,
                    Telefono,
                    Celular,
                    Email,
                    PaginaWeb,
                    Direccion,
                    UsuarioRegistro,
                    FechaRegistro,
                    Representante)
                    values(?,?,?,UPPER(?),UPPER(?),?,?,?,?,?,?,?,?,GETDATE(),?)");
                    $cmdProveedor->execute(array(
                        $idProveedor,
                        $body["TipoDocumento"],
                        $body["NumeroDocumento"],
                        $body["RazonSocial"],
                        $body["NombreComercial"],
                        $body["Ambito"],
                        $body["Estado"],
                        $body["Telefono"],
                        $body["Celular"],
                        $body["Email"],
                        $body["PaginaWeb"],
                        $body["Direccion"],
                        $body["UsuarioRegistro"],
                        $body["Representante"],
                    ));
                }
                Database::getInstance()->getDb()->commit();
                return array("estado" => 1, "message" => "Se registró correctamente el proveedor.");
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return array("estado" => 0, "message" => $ex->getMessage());
        }
    }

    public static function DeleteProveedor($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CompraTB WHERE Proveedor = ?");
            $cmdValidate->bindParam(1, $body["IdProveedor"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                return array("estado" => 2, "message" => "No se puede eliminar el proveedor ya que está ligado a una compra.");
            } else {
                $cmdProveedor = Database::getInstance()->getDb()->prepare("DELETE FROM ProveedorTB WHERE IdProveedor = ?");
                $cmdProveedor->bindParam(1, $body["IdProveedor"], PDO::PARAM_STR);
                $cmdProveedor->execute();

                Database::getInstance()->getDb()->commit();
                return array("estado" => 1, "message" => "Eliminado correctamente el proveedor.");
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return array("estado" => 0, "message" => $ex->getMessage());
        }
    }

    public static function FillProveedor($search)
    {
        try {
            $cmdCliente = Database::getInstance()->getDb()->prepare("SELECT 
            IdProveedor,
            NumeroDocumento,
            RazonSocial
            FROM ProveedorTB
            WHERE 
            ? <> '' AND NumeroDocumento LIKE CONCAT(?,'%') 
            OR 
            ? <> '' AND RazonSocial LIKE CONCAT(?,'%')");
            $cmdCliente->bindParam(1, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(2, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(3, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(4, $search, PDO::PARAM_STR);
            $cmdCliente->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return $cmdCliente->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");
            return $ex->getMessage();
        }
    }
}
