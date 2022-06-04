<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use Exception;

require_once __DIR__ . './../database/DataBaseConexion.php';


class BancoADO
{

    function construct()
    {
    }

    public static function Listar_Bancos(string $buscar, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $cmdBanco = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Bancos(?,?,?)}");
            $cmdBanco->bindParam(1, $buscar, PDO::PARAM_STR);
            $cmdBanco->bindParam(2, $posicionPagina, PDO::PARAM_INT);
            $cmdBanco->bindParam(3, $filasPorPagina, PDO::PARAM_INT);
            $cmdBanco->execute();

            $arrayBanco = array();
            $count = 0;
            while ($row = $cmdBanco->fetch()) {
                $count++;
                array_push($arrayBanco, array(
                    "Id" => $count + $posicionPagina,
                    "IdBanco" => $row['IdBanco'],
                    "NombreCuenta" => $row['NombreCuenta'],
                    "NumeroCuenta" => $row['NumeroCuenta'],
                    "SaldoInicial" => $row['SaldoInicial'],
                    "Simbolo" => $row['Simbolo'],
                    "Sistema" => $row['Sistema'],
                    "FormaPago" => $row['FormaPago'],
                    "Descripcion" => $row['Descripcion'],
                    "Mostrar" => $row['Mostrar']
                ));
            }

            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Bancos_Count(?)}");
            $cmdTotal->bindParam(1, $buscar, PDO::PARAM_STR);
            $cmdTotal->execute();
            $resultTotal = $cmdTotal->fetchColumn();

            Tools::httpStatus200();

            return array("data" => $arrayBanco, "total" => $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function Obtener_Banco_Por_Id(string $idBanco)
    {
        try {
            $cmdBanco = Database::getInstance()->getDb()->prepare("SELECT 
            IdMoneda,
            NombreCuenta,
            NumeroCuenta,            
            SaldoInicial,
            Descripcion,
            FormaPago,
            Mostrar 
            FROM Banco 
            WHERE IdBanco = ?");
            $cmdBanco->bindParam(1, $idBanco, PDO::PARAM_INT);
            $cmdBanco->execute();
            return $cmdBanco->fetchObject();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function Proceso_Banco($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM Banco WHERE IdBanco = ?");
            $cmdValidate->bindParam(1, $body["IdBanco"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM Banco WHERE IdBanco <> ? and NombreCuenta = ?");
                $cmdValidate->bindParam(1, $body["IdBanco"], PDO::PARAM_STR);
                $cmdValidate->bindParam(2, $body["NombreCuenta"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "Existe una cuenta con el mismo nombre.";
                } else {

                    $cmdBanco = Database::getInstance()->getDb()->prepare("UPDATE Banco 
                    SET NombreCuenta = ?,
                    NumeroCuenta = ?,
                    IdMoneda = ?,
                    SaldoInicial = ?,
                    Descripcion = ?,
                    FormaPago = ?,
                    Mostrar = ? 
                    WHERE IdBanco = ?");
                    $cmdBanco->bindParam(1, $body["NombreCuenta"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(2, $body["NumeroCuenta"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(3, $body["IdMoneda"], PDO::PARAM_INT);
                    $cmdBanco->bindParam(4, $body["SaldoInicial"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(5, $body["Descripcion"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(6, $body["FormaPago"], PDO::PARAM_INT);
                    $cmdBanco->bindParam(7, $body["Mostrar"], PDO::PARAM_INT);
                    $cmdBanco->bindParam(8, $body["IdBanco"], PDO::PARAM_STR);
                    $cmdBanco->execute();

                    $cmdBancoHistorial = Database::getInstance()->getDb()->prepare("INSERT INTO  BancoHistorialTB(
                    IdBanco,
                    IdEmpleado,
                    IdProcedencia,
                    Descripcion,
                    Fecha,
                    Hora,
                    Entrada,
                    Salida)
                    VALUES(?,?,?,?,GETDATE(),GETDATE(),?,?)");
                    if ($body["SaldoInicial"] > 0) {
                        $cmdBancoHistorial->execute(array(
                            $body["IdBanco"],
                            $body["IdUsuario"],
                            "",
                            "APERTURA DE CUENTA",
                            $body["SaldoInicial"],
                            0
                        ));
                    }
                    Database::getInstance()->getDb()->commit();
                    Tools::httpStatus201();

                    return "Se actualizo correctamente la cuenta.";
                }
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM Banco WHERE NombreCuenta = ?");
                $cmdValidate->bindParam(1, $body["NombreCuenta"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "Existe una cuenta con el mismo nombre.";
                } else {

                    $codigoBanco = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Banco_Codigo_Alfanumerico();");
                    $codigoBanco->execute();
                    $idBanco = $codigoBanco->fetchColumn();

                    $cmdBanco = Database::getInstance()->getDb()->prepare("INSERT INTO Banco 
                    (IdBanco,
                    NombreCuenta,
                    NumeroCuenta,
                    IdMoneda,
                    SaldoInicial,
                    FechaCreacion,
                    HoraCreacion,
                    Descripcion,
                    Sistema,
                    FormaPago,
                    Mostrar)
                    VALUES(?,?,?,?,?,GETDATE(),GETDATE(),?,0,?,?)");
                    $cmdBanco->bindParam(1, $idBanco, PDO::PARAM_STR);
                    $cmdBanco->bindParam(2, $body["NombreCuenta"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(3, $body["NumeroCuenta"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(4, $body["IdMoneda"], PDO::PARAM_INT);
                    $cmdBanco->bindParam(5, $body["SaldoInicial"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(6, $body["Descripcion"], PDO::PARAM_STR);
                    $cmdBanco->bindParam(7, $body["FormaPago"], PDO::PARAM_INT);
                    $cmdBanco->bindParam(8, $body["Mostrar"], PDO::PARAM_INT);
                    $cmdBanco->execute();

                    $cmdBancoHistorial = Database::getInstance()->getDb()->prepare("INSERT INTO  BancoHistorialTB(
                    IdBanco,
                    IdEmpleado,
                    IdProcedencia,
                    Descripcion,
                    Fecha,
                    Hora,
                    Entrada,
                    Salida)
                    VALUES(?,?,?,?,GETDATE(),GETDATE(),?,?)");
                    if ($body["SaldoInicial"] > 0) {
                        $cmdBancoHistorial->execute(array(
                            $body["IdBanco"],
                            $body["IdUsuario"],
                            "",
                            "APERTURA DE CUENTA",
                            $body["SaldoInicial"],
                            0
                        ));
                    }
                    Database::getInstance()->getDb()->commit();
                    Tools::httpStatus201();

                    return "Se registro correctamente la cuenta.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function Deleted_Banco($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM Banco WHERE IdBanco = ? AND Sistema = 1");
            $cmdValidate->bindParam(1, $body["IdBanco"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();

                return "La cuenta no se puede eliminar es propio del sistema.";
            } else {
                $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM BancoHistorialTB WHERE IdBanco = ?");
                $cmdValidate->bindParam(1, $body["IdBanco"], PDO::PARAM_STR);
                $cmdValidate->execute();
                if ($cmdValidate->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    Tools::httpStatus400();

                    return "La cuenta no se puede eliminar porque tiene asociado un historial.";
                } else {
                    $cmdBanco = Database::getInstance()->getDb()->prepare("DELETE FROM Banco WHERE IdBanco = ?");
                    $cmdBanco->bindParam(1, $body["IdBanco"], PDO::PARAM_STR);
                    $cmdBanco->execute();

                    Database::getInstance()->getDb()->commit();
                    Tools::httpStatus201();

                    return "Se eliminó correctamente la cuenta.";
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }
}
