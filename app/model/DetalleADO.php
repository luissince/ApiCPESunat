<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use Exception;


require_once __DIR__ . './../database/DataBaseConexion.php';


class DetalleADO
{

    function construct()
    {
    }

    public static function Listar_Mantenimiento(string $value)
    {
        try {
            $cmdMantenimiento  = Database::getInstance()->getDb()->prepare("{call Sp_List_Table_Matenimiento(?)}");
            $cmdMantenimiento->bindParam(1, $value, PDO::PARAM_STR);
            $cmdMantenimiento->execute();
            return $cmdMantenimiento->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public static function Listar_Detalle_ById(string $idMantenimiento, string $search)
    {
        try {
            $cmdDetalle = Database::getInstance()->getDb()->prepare("{call Sp_List_Table_Detalle(?,?)}");
            $cmdDetalle->bindParam(1, $idMantenimiento, PDO::PARAM_STR);
            $cmdDetalle->bindParam(2, $search, PDO::PARAM_STR);
            $cmdDetalle->execute();
            return $cmdDetalle->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function GetDetailIdName($value)
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("{CALL Sp_Get_Detalle_IdNombre(?,?,?)}");
            $comando->bindParam(1, $value[0], PDO::PARAM_STR);
            $comando->bindParam(2, $value[1], PDO::PARAM_STR);
            $comando->bindParam(3, $value[2], PDO::PARAM_STR);
            $comando->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return  $comando->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function GetDetailId($value)
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("{CALL Sp_Get_Detalle_Id(?)}");
            $comando->bindParam(1, $value, PDO::PARAM_STR);
            $comando->execute();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return  $comando->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function FillDetalleUnidadMedida($search)
    {
        try {
            $cmdUnidad = Database::getInstance()->getDb()->prepare("SELECT IdDetalle,Nombre FROM DetalleTB 
            WHERE Nombre LIKE CONCAT('%',?,'%') AND IdMantenimiento = '0013'");
            $cmdUnidad->bindParam(1, $search, PDO::PARAM_STR);
            $cmdUnidad->execute();

            Tools::httpStatus200();
            return $cmdUnidad->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }

    public static function CategoriasParaProductos()
    {
        try {
            $cmdDatalle = Database::getInstance()->getDb()->prepare("SELECT 
            d.IdDetalle,
            d.Nombre,
            count(d.IdDetalle) AS Cantidad 
            FROM DetalleTB AS d 
            INNER JOIN SuministroTB AS s ON s.Categoria = d.IdDetalle
            WHERE d.IdMantenimiento = '0006'
            GROUP BY d.IdDetalle,d.Nombre");
            $cmdDatalle->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return  $cmdDatalle->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function MarcasParaProductos()
    {
        try {
            $cmdDatalle = Database::getInstance()->getDb()->prepare("SELECT 
            d.IdDetalle,
            d.Nombre,
            count(d.IdDetalle) AS Cantidad 
            FROM DetalleTB AS d 
            INNER JOIN SuministroTB AS s ON s.Marca = d.IdDetalle
            WHERE d.IdMantenimiento = '0007'
            GROUP BY d.IdDetalle,d.Nombre");
            $cmdDatalle->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return  $cmdDatalle->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function CrudDetalle($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT 
            IdDetalle,
            IdMantenimiento 
            FROM DetalleTB 
            WHERE IdDetalle=? AND IdMantenimiento=?");
            $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
            $cmdValidate->bindParam(2, $body["IdMantenimiento"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT IdDetalle,IdMantenimiento 
                FROM DetalleTB 
                WHERE IdDetalle<>? AND IdMantenimiento=? AND Nombre = ?");
                $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
                $cmdValidate->bindParam(2, $body["IdMantenimiento"], PDO::PARAM_STR);
                $cmdValidate->bindParam(3, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "No puede haber 2 detalles con el mismo nombre.";
                } else {
                    $cmdDetalle = Database::getInstance()->getDb()->prepare("UPDATE DetalleTB SET IdAuxiliar=?,Nombre=?,Descripcion=?,Estado=? 
                    WHERE IdDetalle =? AND IdMantenimiento = ?");
                    $cmdDetalle->execute(array(
                        $body["IdAuxiliar"],
                        $body["Nombre"],
                        $body["Descripcion"],
                        $body["Estado"],
                        $body["IdDetalle"],
                        $body["IdMantenimiento"],
                    ));

                    Database::getInstance()->getDb()->commit();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 201 . ' ' . "Created");

                    return "Se actualizó correctamente el detalle.";
                }
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT Nombre FROM DetalleTB 
                WHERE IdMantenimiento = ? AND Nombre = ?");
                $cmdValidate->bindParam(1, $body["IdMantenimiento"], PDO::PARAM_STR);
                $cmdValidate->bindParam(2, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "No puede haber 2 detalles con el mismo nombre.";
                } else {
                    $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO 
                    DetalleTB(
                    IdMantenimiento,
                    IdAuxiliar,
                    Nombre,
                    Descripcion,
                    Estado,
                    UsuarioRegistro) 
                    values(?,?,?,?,?,?)");
                    $cmdDetalle->execute(array(
                        $body["IdMantenimiento"],
                        $body["IdAuxiliar"],
                        $body["Nombre"],
                        $body["Descripcion"],
                        $body["Estado"],
                        $body["UsuarioRegistro"],
                    ));

                    Database::getInstance()->getDb()->commit();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 201 . ' ' . "Created");

                    return "El registró correctamente el detalle.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function DeleteDetalle($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM SuministroTB WHERE Categoria = ?");
            $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 400 . ' ' . "Bad Request");

                return "No se puede eliminar el detalle porque esta ligado a un producto.";
            } else {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM SuministroTB WHERE Marca = ?");
                $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "No se puede eliminar el detalle porque esta ligado a un producto.";
                } else {
                    $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM SuministroTB WHERE Presentacion = ?");
                    $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
                    $cmdValidate->execute();
                    if ($cmdValidate->fetch()) {
                        Database::getInstance()->getDb()->rollback();
                        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                        header($protocol . ' ' . 400 . ' ' . "Bad Request");

                        return "No se puede eliminar el detalle porque esta ligado a un producto.";
                    } else {
                        $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM SuministroTB WHERE UnidadCompra = ?");
                        $cmdValidate->bindParam(1, $body["IdDetalle"], PDO::PARAM_INT);
                        $cmdValidate->execute();
                        if ($cmdValidate->fetch()) {
                            Database::getInstance()->getDb()->rollback();
                            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                            header($protocol . ' ' . 400 . ' ' . "Bad Request");

                            return "No se puede eliminar el detalle porque esta ligado a un producto.";
                        } else {
                            $cmdDetalle = Database::getInstance()->getDb()->prepare("DELETE FROM DetalleTB WHERE IdDetalle = ? AND IdMantenimiento = ?");
                            $cmdDetalle->execute(array(
                                $body["IdDetalle"],
                                $body["IdMantenimiento"]
                            ));

                            Database::getInstance()->getDb()->commit();
                            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                            header($protocol . ' ' . 201 . ' ' . "Created");

                            return "Se eliminó correctamente del detalle.";
                        }
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
