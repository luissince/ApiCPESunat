<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class EmpleadoADO
{

    function construct()
    {
    }

    public static function Login($usuario, $clave)
    {
        try {
            $cmdLogin = Database::getInstance()->getDb()->prepare("{CALL Sp_Validar_Ingreso(?,?)}");
            $cmdLogin->bindParam(1, $usuario, PDO::PARAM_STR);
            $cmdLogin->bindParam(2, $clave, PDO::PARAM_STR);
            $cmdLogin->execute();
            $resultLogin = $cmdLogin->fetchObject();
            return $resultLogin;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function ListEmpleados(int $opcion, string $buscar, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $cmdEmpleado = Database::getInstance()->getDb()->prepare("{call Sp_Listar_Empleados(?,?,?,?)}");
            $cmdEmpleado->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdEmpleado->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdEmpleado->bindParam(3, $posicionPagina, PDO::PARAM_INT);
            $cmdEmpleado->bindParam(4, $filasPorPagina, PDO::PARAM_INT);
            $cmdEmpleado->execute();

            $count = 0;
            $array = array();
            while ($row = $cmdEmpleado->fetch()) {
                $count++;
                array_push($array, array(
                    "Id" => $count + $posicionPagina,
                    "IdEmpleado" => $row['IdEmpleado'],
                    "NumeroDocumento" => $row['NumeroDocumento'],
                    "Apellidos" => $row['Apellidos'],
                    "Nombres" => $row['Nombres'],
                    "Telefono" => $row['Telefono'],
                    "Celular" => $row['Celular'],
                    "Direccion" => $row['Direccion'],
                    "Rol" => $row['Rol'],
                    "Estado" => $row['Estado'],
                ));
            }

            $cmdEmpleado = Database::getInstance()->getDb()->prepare("{call Sp_Listar_Empleados_Count(?,?)}");
            $cmdEmpleado->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdEmpleado->bindParam(2, $opcion, PDO::PARAM_STR);
            $cmdEmpleado->execute();
            $resultTotal = $cmdEmpleado->fetchColumn();

            Tools::httpStatus200();
            return array("data" => $array, "total" => $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function ObtenerEmpleadoById(string $idEmpleado)
    {
        try {
            $cmdUsuario = Database::getInstance()->getDb()->prepare("SELECT * FROM EmpleadoTB WHERE IdEmpleado = ?");
            $cmdUsuario->bindParam(1, $idEmpleado, PDO::PARAM_STR);
            $cmdUsuario->execute();

            Tools::httpStatus200();
            return $cmdUsuario->fetch(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }

    public static function GetClientePredetermined()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT ci.IdCliente,ci.TipoDocumento,ci.Informacion, ci.NumeroDocumento, ci.Celular,ci.Email,ci.Direccion FROM ClienteTB AS ci WHERE Predeterminado = 1");
            $comando->execute();
            $resultCliente = $comando->fetchObject();
            if ($resultCliente) {
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 200 . ' ' . "OK");

                return $resultCliente;
            } else {
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 200 . ' ' . "OK");

                return null;
            }
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function GetListEmpleados()
    {
        try {

            $cmdEmpleados = Database::getInstance()->getDb()->prepare("SELECT IdEmpleado,Apellidos,Nombres FROM EmpleadoTB");
            $cmdEmpleados->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return $cmdEmpleados->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function FillEmpleados($search)
    {
        try {
            $cmdCliente = Database::getInstance()->getDb()->prepare("SELECT 
            IdEmpleado,
            NumeroDocumento,
            CONCAT(Apellidos,', ',Nombres) AS Informacion            
            FROM EmpleadoTB
            WHERE 
            ? <> '' AND NumeroDocumento LIKE CONCAT(?,'%') 
            OR 
            ? <> '' AND Apellidos LIKE CONCAT(?,'%')
            OR 
            ? <> '' AND Nombres LIKE CONCAT(?,'%')
            ");
            $cmdCliente->bindParam(1, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(2, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(3, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(4, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(5, $search, PDO::PARAM_STR);
            $cmdCliente->bindParam(6, $search, PDO::PARAM_STR);
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

    public static function CrudEmpleado($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM EmpleadoTB WHERE IdEmpleado = ?");
            $cmdValidate->bindParam(1, $body["IdEmpleado"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                $cmdEmpresa = Database::getInstance()->getDb()->prepare("UPDATE EmpleadoTB 
                SET 
                TipoDocumento = ?,
                NumeroDocumento = ?,
                Apellidos = ?,
                Nombres = ?,
                Sexo = ?,
                FechaNacimiento = ?,
                Puesto = ?,
                Rol = ?,
                Estado = ?,
                Telefono = ?,
                Celular = ?,
                Email = ?,
                Direccion = ?,
                Usuario = ?,
                Clave  = ? 
                WHERE IdEmpleado = ?");
                $cmdEmpresa->execute(array(
                    $body["TipoDocumento"],
                    $body["NumeroDocumento"],
                    $body["Apellidos"],
                    $body["Nombres"],
                    $body["Sexo"],
                    $body["FechaNacimiento"],
                    $body["Puesto"],
                    $body["Rol"],
                    $body["Estado"],
                    $body["Telefono"],
                    $body["Celular"],
                    $body["Email"],
                    $body["Direccion"],
                    $body["Usuario"],
                    $body["Clave"],
                    $body["IdEmpleado"]
                ));

                Database::getInstance()->getDb()->commit();
                Tools::httpStatus201();
                return "Se actualizó correctamente el usuario.";
            } else {
                $codigoEmpleado = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Empleado_Codigo_Alfanumerico();");
                $codigoEmpleado->execute();
                $idEmpleado = $codigoEmpleado->fetchColumn();

                $cmdEmpleado  = Database::getInstance()->getDb()->prepare("INSERT INTO EmpleadoTB(
                IdEmpleado,
                TipoDocumento,
                NumeroDocumento,
                Apellidos,
                Nombres,
                Sexo,
                FechaNacimiento,
                Puesto,
                Rol,
                Estado,
                Telefono,
                Celular,
                Email,
                Direccion,
                Usuario,
                Clave,
                Sistema,
                Huella)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $cmdEmpleado->execute(array(
                    $idEmpleado,
                    $body["TipoDocumento"],
                    $body["NumeroDocumento"],
                    $body["Apellidos"],
                    $body["Nombres"],
                    $body["Sexo"],
                    $body["FechaNacimiento"],
                    $body["Puesto"],
                    $body["Rol"],
                    $body["Estado"],
                    $body["Telefono"],
                    $body["Celular"],
                    $body["Email"],
                    $body["Direccion"],
                    $body["Usuario"],
                    $body["Clave"],
                    $body["Sistema"],
                    $body["Huella"],
                ));
            }
            Database::getInstance()->getDb()->commit();
            Tools::httpStatus201();
            return "Se registró correctamente el usuario.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }

    public static function DeleteEmpleado($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM EmpleadoTB WHERE IdEmpleado = ? AND Sistema = 1");
            $cmdValidate->bindParam(1, $body["IdEmpleado"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();
                return "El empleado no puede ser eliminado porque es parte del sistema.";
            }

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CajaTB WHERE IdUsuario = ?");
            $cmdValidate->bindParam(1, $body["IdEmpleado"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();
                return "No se puede eliminar el empleado, porque tiene historial de cajas.";
            }

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CompraTB WHERE Usuario = ?");
            $cmdValidate->bindParam(1, $body["IdEmpleado"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {
                Database::getInstance()->getDb()->rollback();
                Tools::httpStatus400();
                return "No se puede eliminar el empleado, porque tiene un historial de compras.";
            }

            $cmdValidate = Database::getInstance()->getDb()->prepare("DELETE FROM EmpleadoTB WHERE IdEmpleado = ?");
            $cmdValidate->bindParam(1, $body["IdEmpleado"], PDO::PARAM_STR);
            $cmdValidate->execute();

            Database::getInstance()->getDb()->commit();
            Tools::httpStatus200();
            return "El eliminó correctamente del empleado.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            Tools::httpStatus500();
            return $ex->getMessage();
        }
    }
}
