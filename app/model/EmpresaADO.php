<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use Exception;

require_once __DIR__ . './../database/DataBaseConexion.php';

class EmpresaADO
{

    function construct()
    {
    }

    public static function Index()
    {
        try {
            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT 
            Telefono,
            Celular,
            Domicilio,
            Email,
            Telefono,
            NombreComercial
            FROM EmpresaTB");
            $cmdEmpresa->execute();

            return Tools::httpStatus200($cmdEmpresa->fetch(PDO::FETCH_OBJ));
        } catch (Exception $ex) {
            return  Tools::httpStatus500($ex->getMessage());
        }
    }

    public static function ObtenerEmpresa()
    {
        try {
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            idEmpresa,
            documento,
            razonSocial
             FROM empresa LIMIT 1");
            $comando->execute();

            return Tools::httpStatus200($comando->fetchObject());
        } catch (Exception $ex) {
            return Tools::httpStatus500($ex->getMessage());
        }
    }

    public static function ReporteEmpresa()
    {
        try {
            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,
            e.NumeroDocumento,
            e.RazonSocial,
            e.NombreComercial,
            e.Domicilio,
            e.Telefono,
            e.Celular,
            e.Email,
            e.Terminos,
            e.Condiciones,
            e.PaginaWeb,
            e.Image
            FROM EmpresaTB AS e 
            INNER JOIN DetalleTB AS d ON e.TipoDocumento = d.IdDetalle AND d.IdMantenimiento = '0003'");
            $cmdEmpresa->execute();
            $rowEmpresa = $cmdEmpresa->fetch();
            $empresa  = (object)array(
                "IdAuxiliar" => $rowEmpresa['IdAuxiliar'],
                "NumeroDocumento" => $rowEmpresa['NumeroDocumento'],
                "RazonSocial" => $rowEmpresa['RazonSocial'],
                "NombreComercial" => $rowEmpresa['NombreComercial'],
                "Domicilio" => $rowEmpresa['Domicilio'],
                "Telefono" => $rowEmpresa['Telefono'],
                "PaginaWeb" => $rowEmpresa['PaginaWeb'],
                "Email" => $rowEmpresa['Email'],
                "Terminos" => $rowEmpresa['Terminos'],
                "Celular" => $rowEmpresa['Celular'],
                "Condiciones" => $rowEmpresa['Condiciones'],
                "Image" => $rowEmpresa['Image'] == null ? "" : base64_encode($rowEmpresa['Image'])
            );

            return  $empresa;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function FiltrarUbigeo(string $search)
    {
        try {
            $cmdUbigeo = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Ubigeo_BySearch(?)}");
            $cmdUbigeo->bindParam(1, $search, PDO::PARAM_STR);
            $cmdUbigeo->execute();
            return Tools::httpStatus200($cmdUbigeo->fetchAll(PDO::FETCH_OBJ));
        } catch (Exception $ex) {
            return Tools::httpStatus500($ex->getMessage());
        }
    }

    public static function CrudEmpresa($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $validate = Database::getInstance()->getDb()->prepare("SELECT * FROM empresa WHERE idEmpresa =?");
            $validate->bindParam(1, $body['idEmpresa'], PDO::PARAM_STR);
            $validate->execute();
            if(!$validate->fetch()){
                Database::getInstance()->getDb()->rollback();
                return Tools::httpStatus400("No se encontro los datos de la empresa.");
            }

            $path = "";
            if ($body["certificadoType"] == 0) {
                Database::getInstance()->getDb()->rollback();
                return Tools::httpStatus400("No se pudo procesar por problemas del cliente.");
            }

            $ext = pathinfo($body['certificadoName'], PATHINFO_EXTENSION);
            $file_path = $body['txtNumDocumento'] . "." . $ext;
            $path = "../resources/" . $file_path;
            $move = move_uploaded_file($body['certificadoNameTmp'], $path);
            if (!$move) {
                throw new Exception('Problemas al subir el certificado.');
            }

            $pkcs12 = file_get_contents($path);
            $certificados = array();
            $respuesta = openssl_pkcs12_read($pkcs12, $certificados, $body['txtClaveCertificado']);

            if ($respuesta) {
                $publicKeyPem  = $certificados['cert'];
                $privateKeyPem = $certificados['pkey'];

                file_put_contents('../resources/private_key.pem', $privateKeyPem);
                file_put_contents('../resources/public_key.pem', $publicKeyPem);
                chmod("../resources/private_key.pem", 0777);
                chmod("../resources/public_key.pem", 0777);
            } else {
                throw new Exception('Error en crear las llaves del certificado.');
            }

            $empresa = Database::getInstance()->getDb()->prepare('UPDATE empresa SET
            certificado=?,
            claveCert=?
            WHERE idEmpresa = ?');
            $empresa->bindParam(1, $file_path, PDO::PARAM_STR);
            $empresa->bindParam(2, $body['txtClaveCertificado'], PDO::PARAM_STR);
            $empresa->bindParam(3, $body['idEmpresa'], PDO::PARAM_STR);
            $empresa->execute();

            return Tools::httpStatus201(array(
                "state" => 1,
                "message" => "Se modificó correctamente los datos."
            ));
        } catch (Exception $ex) {
            unlink('../resources/' . $file_path);
            Database::getInstance()->getDb()->rollback();
            return Tools::httpStatus500($ex->getMessage());
        }
    }
}
