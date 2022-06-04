<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class AlmacenADO
{

    function construct()
    {
    }

    public static function ListarAlmacen(string $buscar, int $posicionPagina, int $filasPorPagina)
    {
        try {

            $cmdAlmacen = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Almacen(?,?,?)}");
            $cmdAlmacen->bindParam(1, $buscar, PDO::PARAM_STR);
            $cmdAlmacen->bindParam(2, $posicionPagina, PDO::PARAM_INT);
            $cmdAlmacen->bindParam(3, $filasPorPagina, PDO::PARAM_INT);
            $cmdAlmacen->execute();

            $count = 0;
            $arrayAlmacen = array();
            while ($row = $cmdAlmacen->fetch()) {
                $count++;
                array_push($arrayAlmacen, array(
                    "Id" => $count + $posicionPagina,
                    "IdAlmacen" => $row['IdAlmacen'],
                    "Nombre" => $row['Nombre'],

                    "IdUbigeo" => $row['IdUbigeo'],
                    "Ubigeo" => $row['Ubigeo'],
                    "Departamento" => $row['Departamento'],
                    "Provincia" => $row['Provincia'],
                    "Distrito" => $row['Distrito'],

                    "Direccion" => $row['Direccion'],
                    "Fecha" => $row['Fecha'],
                    "Hora" => $row['Hora'],
                ));
            }

            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Almacen_Count(?)}");
            $cmdTotal->bindParam(1, $buscar, PDO::PARAM_STR);
            $cmdTotal->execute();
            $resultTotal = $cmdTotal->fetchColumn();

            Tools::httpStatus200();

            return array("data" => $arrayAlmacen, "total" => $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }

    public static function GetSearchComboBoxAlmacen()
    {
        try {
            $cmdDetalle = Database::getInstance()->getDb()->prepare("SELECT IdAlmacen,Nombre FROM AlmacenTB");
            $cmdDetalle->execute();

            Tools::httpStatus200();

            return $cmdDetalle->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function Obtener_Almancen_Por_Id(int  $idAlmacen)
    {
        try {
            $cmdAlmacen = Database::getInstance()->getDb()->prepare("SELECT 
            a.IdAlmacen,
            a.Nombre, 
            a.IdUbigeo,
            a.Direccion,
            u.Ubigeo,
            u.Departamento,
            u.Provincia,
            u.Distrito
            FROM AlmacenTB AS a
            INNER JOIN UbigeoTB AS u ON u.IdUbigeo = a.IdUbigeo
            WHERE a.IdAlmacen = ?");
            $cmdAlmacen->bindParam(1, $idAlmacen, PDO::PARAM_INT);
            $cmdAlmacen->execute();

            Tools::httpStatus200();
            return $cmdAlmacen->fetch(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function CrudAlmacen($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM AlmacenTB WHERE IdAlmacen = ?");
            $cmdValidate->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM AlmacenTB WHERE IdAlmacen <> ? AND Nombre = ?");
                $cmdValidate->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
                $cmdValidate->bindParam(2, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();

                    Tools::httpStatus400();
                    return "No se puede registrar o actualizar el mismo nombre.";
                } else {
                    $cmdAlmacen = Database::getInstance()->getDb()->prepare("UPDATE AlmacenTB 
                    SET 
                    Nombre = ?,
                    IdUbigeo = ?,
                    Direccion = ?,
                    Fecha = GETDATE(),
                    Hora = GETDATE(),
                    IdUsuario = ? 
                    WHERE IdAlmacen = ?");
                    $cmdAlmacen->execute(array(
                        $body["Nombre"],
                        $body["IdUbigeo"],
                        $body["Direccion"],
                        $body["IdUsuario"],
                        $body["IdAlmacen"],
                    ));
                    Database::getInstance()->getDb()->commit();

                    Tools::httpStatus201();
                    return "Se actualizó correctamente el almacen.";
                }
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM AlmacenTB WHERE Nombre = ?");
                $cmdValidate->bindParam(1, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();

                    Tools::httpStatus400();
                    return "No se puede registrar o actualizar el mismo nombre.";
                } else {
                    $codigoAlmacen = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Almacen_Codigo_Numerico()");
                    $codigoAlmacen->execute();
                    $idAlmacen = $codigoAlmacen->fetchColumn();

                    $cmdAlmacen = Database::getInstance()->getDb()->prepare("INSERT INTO AlmacenTB(
                    IdAlmacen,
                    Nombre,
                    IdUbigeo,
                    Direccion,
                    Fecha,
                    Hora,
                    IdUsuario) 
                    VALUES(?,?,?,?,GETDATE(),GETDATE(),?)");
                    $cmdAlmacen->execute(array(
                        $idAlmacen,
                        $body["Nombre"],
                        $body["IdUbigeo"],
                        $body["Direccion"],
                        $body["IdUsuario"],
                    ));

                    $cmdProducto = Database::getInstance()->getDb()->prepare("SELECT IdSuministro FROM SuministroTB");
                    $cmdProducto->execute();
                    while ($row = $cmdProducto->fetch()) {
                        $cmdCantidad = Database::getInstance()->getDb()->prepare("INSERT INTO CantidadTB(
                        IdAlmacen,
                        IdSuministro,
                        StockMinimo,
                        StockMaximo,
                        Cantidad) 
                        VALUES(?,?,?,?,?)");
                        $cmdCantidad->execute(array(
                            $idAlmacen,
                            $row["IdSuministro"],
                            0,
                            0,
                            0
                        ));
                    }

                    Database::getInstance()->getDb()->commit();

                    Tools::httpStatus201();
                    return "Se registró correctamente el almacen.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function DeleteAlmacen($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CompraTB WHERE IdAlmacen = ?");
            $cmdValidate->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();

                return "El almacen esta ligado a un historial de compras.";
            } else {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM KardexSuministroTB WHERE IdAlmacen = ?");
                $cmdValidate->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "El almacen esta ligado a un kardex de productos.";
                } else {
                    if ($body["IdAlmacen"] == 0) {
                        Database::getInstance()->getDb()->rollback();
                        Tools::httpStatus400();

                        return "No se puede eliminar el almacen generado por el sistema.";
                    } else {
                        $cmdAlmacen = Database::getInstance()->getDb()->prepare("DELETE FROM AlmacenTB WHERE IdAlmacen = ?");
                        $cmdAlmacen->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
                        $cmdAlmacen->execute();

                        $cmdCantidad = Database::getInstance()->getDb()->prepare("DELETE FROM CantidadTB WHERE IdAlmacen = ?");
                        $cmdCantidad->bindParam(1, $body["IdAlmacen"], PDO::PARAM_INT);
                        $cmdCantidad->execute();

                        Database::getInstance()->getDb()->commit();
                        Tools::httpStatus201();

                        return "Se eliminó correctamente el almacen";
                    }
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }
}
