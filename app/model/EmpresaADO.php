<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use SysSoftIntegra\DataBase\Database;
use PDO;
use Exception;


class EmpresaADO
{

    function construct()
    {
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

            Database::getInstance()->getDb()->commit();

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
