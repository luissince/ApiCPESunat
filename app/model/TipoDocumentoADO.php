<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';

class TipoDocumentoADO
{

    function __construct()
    {
    }

    public static function ListTipoDocumento(int $opcion, string $buscar, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("{call Sp_Listar_TipoDocumento(?,?,?,?)}");
            $cmdTipoDocumento->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdTipoDocumento->bindParam(2, $buscar, PDO::PARAM_INT);
            $cmdTipoDocumento->bindParam(3, $posicionPagina, PDO::PARAM_INT);
            $cmdTipoDocumento->bindParam(4, $filasPorPagina, PDO::PARAM_INT);
            $cmdTipoDocumento->execute();

            $count = 0;
            $array = array();
            while ($row = $cmdTipoDocumento->fetch()) {
                $count++;
                array_push($array, array(
                    "Id" => $count + $posicionPagina,
                    "IdTipoDocumento" => $row["IdTipoDocumento"],
                    "Nombre" => $row["Nombre"],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],
                    "CodigoAlterno" => $row["CodigoAlterno"],
                    "Facturacion" => $row["Facturacion"],
                    "Predeterminado" => $row["Predeterminado"],
                    "Sistema" => $row["Sistema"],
                    "Guia" => $row["Guia"],
                    "NotaCredito" => $row["NotaCredito"],
                    "Estado" => $row["Estado"],
                    "Campo" => $row["Campo"],
                    "NumeroCampo" => $row["NumeroCampo"]
                ));
            }

            $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("{call Sp_Listar_TipoDocumento_Count(?,?)}");
            $cmdTipoDocumento->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdTipoDocumento->bindParam(2, $buscar, PDO::PARAM_INT);
            $cmdTipoDocumento->execute();
            $resultTotal = $cmdTipoDocumento->fetchColumn();

            Tools::httpStatus200();

            return array("data" => $array, "total" =>   $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return  $ex->getMessage();
        }
    }

    public static function ObtenerTipoDocumentoById(int $idTipoDocumento)
    {
        try {
            $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("SELECT 
            IdTipoDocumento,
            Nombre,
            Serie,
            Numeracion,
            CodigoAlterno,
            Facturacion,
            Predeterminado,
            Sistema,
            Guia,
            NotaCredito,
            Estado,
            Campo,
            NumeroCampo
            FROM TipoDocumentoTB 
            WHERE IdTipoDocumento = ?");
            $cmdTipoDocumento->bindParam(1, $idTipoDocumento, PDO::PARAM_INT);
            $cmdTipoDocumento->execute();

            Tools::httpStatus200();
            return  $cmdTipoDocumento->fetchObject();
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function GetDocumentoCombBoxVentas()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            IdTipoDocumento,
            Nombre,
            Serie, 
            Predeterminado,
            Campo,
            NumeroCampo
            FROM TipoDocumentoTB 
            WHERE Guia <> 1 AND NotaCredito <> 1");
            $comando->execute();

            Tools::httpStatus200();

            return $comando->fetchAll(PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return  $ex->getMessage();
        }
    }

    public static function GetDocumentoCombBoxNotaCredito()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            IdTipoDocumento,
            Nombre,
            Serie, 
            Predeterminado 
            FROM TipoDocumentoTB WHERE NotaCredito = 1");
            $comando->execute();

            Tools::httpStatus200();

            return $comando->fetchAll(PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function GetDocumentoFacturados()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            IdTipoDocumento,
            Nombre,
            Serie
            FROM TipoDocumentoTB WHERE Facturacion = 1");
            $comando->execute();

            Tools::httpStatus200();

            return $comando->fetchAll(PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function CrudTipoDocumento($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM TipoDocumentoTB WHERE IdTipoDocumento = ? ");
            $cmdValidate->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {

                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM TipoDocumentoTB WHERE IdTipoDocumento <> ? AND Nombre = ? ");
                $cmdValidate->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
                $cmdValidate->bindParam(2, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "No puede haber 2 comprobante con el mismo nombre.";
                } else {
                    $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("UPDATE TipoDocumentoTB 
                    SET 
                    Nombre = ?, 
                    Serie = ?,
                    Numeracion=?,
                    CodigoAlterno=?,
                    Guia = ?,
                    Facturacion=?, 
                    NotaCredito=?, 
                    Estado = ?,
                    Campo = ?,
                    NumeroCampo = ? 
                    WHERE IdTipoDocumento = ?");
                    $cmdTipoDocumento->execute(array(
                        $body["Nombre"],
                        $body["Serie"],
                        $body["Numeracion"],
                        $body["CodigoAlterno"],
                        $body["Guia"],
                        $body["Facturacion"],
                        $body["NotaCredito"],
                        $body["Estado"],
                        $body["Campo"],
                        $body["NumeroCampo"],
                        $body["IdTipoDocumento"]
                    ));

                    Database::getInstance()->getDb()->commit();
                    Tools::httpStatus201();

                    return "Se actualizó correctamente el comprobante.";
                }
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM TipoDocumentoTB WHERE  Nombre = ?");
                $cmdValidate->bindParam(1, $body["Nombre"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "No puede haber 2 comprobante con el mismo nombre.";
                } else {
                    $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("INSERT INTO 
                    TipoDocumentoTB (
                    Nombre,
                    Serie,
                    Numeracion,
                    Predeterminado,
                    Sistema,
                    CodigoAlterno,
                    Guia,
                    Facturacion,
                    NotaCredito,
                    Estado,
                    Campo,
                    NumeroCampo) 
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                    $cmdTipoDocumento->execute(array(
                        $body["Nombre"],
                        $body["Serie"],
                        $body["Numeracion"],
                        false,
                        false,
                        $body["CodigoAlterno"],
                        $body["Guia"],
                        $body["Facturacion"],
                        $body["NotaCredito"],
                        $body["Estado"],
                        $body["Campo"],
                        $body["NumeroCampo"]
                    ));

                    Database::getInstance()->getDb()->commit();
                    Tools::httpStatus201();

                    return "Se registró correctamente el comprobante.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function DeleteTipoDocumento($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM TipoDocumentoTB 
            WHERE IdTipoDocumento = ? AND Sistema = 1");
            $cmdValidate->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();

                return "El tipo de documento no se puede eliminar porque es del sistema.";
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM VentaTB WHERE Comprobante = ?");
                $cmdValidate->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "El tipo de documento esta ligado a una venta.";
                } else {
                    $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM NotaCreditoTB WHERE Comprobante = ?");
                    $cmdValidate->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
                    $cmdValidate->execute();
                    if ($cmdValidate->fetch()) {
                        Database::getInstance()->getDb()->rollback();
                        Tools::httpStatus400();

                        return "El tipo de documento esta ligado a una nota de crédito.";
                    } else {
                        $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("DELETE FROM TipoDocumentoTB WHERE IdTipoDocumento = ?");
                        $cmdTipoDocumento->bindParam(1, $body["IdTipoDocumento"], PDO::PARAM_INT);
                        $cmdTipoDocumento->execute();

                        Database::getInstance()->getDb()->commit();
                        Tools::httpStatus201();

                        return "Se eliminó correctamente el comprobante.";
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
