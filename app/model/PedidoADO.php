<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class PedidoADO
{

    function construct()
    {
    }


    public static function RegistrarWeb($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $codigoPedido = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Pedido_Codigo_Alfanumerico();");
            $codigoPedido->execute();
            $idPedido = $codigoPedido->fetchColumn();

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM ClienteTB WHERE NumeroDocumento = ?");
            $cmdValidate->bindParam(1, $body["Documento"], PDO::PARAM_STR);
            $cmdValidate->execute();

            $idCliente = "";
            if ($row = $cmdValidate->fetchObject()) {
                $idCliente = $row->IdCliente;
            } else {
                $codigoCliente = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Cliente_Codigo_Alfanumerico();");
                $codigoCliente->execute();
                $idGeneradoCli = $codigoCliente->fetchColumn();

                $cmdCliente = Database::getInstance()->getDb()->prepare("INSERT INTO ClienteTB(
                    IdCliente,
                    TipoDocumento,
                    NumeroDocumento,
                    Informacion,
                    Telefono,
                    Celular,
                    Email,
                    Direccion,
                    Representante,
                    Estado,
                    Predeterminado,
                    Sistema)
                    VALUES(?,'1351',?,?,'',?,'',?,'',1,0,0)");
                $cmdCliente->bindParam(1, $idGeneradoCli, PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["Documento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->execute();
                $idCliente = $idGeneradoCli;
            }

            $cmdPedido = Database::getInstance()->getDb()->prepare("INSERT INTO PedidoTB(
                IdPedido,
                IdCliente,
                IdVendedor,
                IdMoneda,
                FechaPedido,
                HoraPedido,
                FechaVencimiento,
                HoraVencimiento,
                Estado,
                Observacion
            )
            VALUES(?,?,'',1,GETDATE(),GETDATE(),GETDATE(),GETDATE(),1,'')");
            $cmdPedido->execute(array(
                $idPedido,
                $idCliente
            ));

            foreach ($body["Detalle"] as $value) {
                $cmDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO PedidoDetalleTB(
                IdPedido,
                IdSuministro,
                Cantidad,
                Precio,
                Descuento
            )
            VALUES(?,?,?,?,?)");
                $cmDetalle->execute(array(
                    $idPedido,
                    $value["IdSuministro"],
                    $value["Cantidad"],
                    $value["Precio"],
                    $value["Descuento"]
                ));
            }


            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se registro correctamente su orden, le estaremos respondiendo a su n° de " . $body["Celular"] . " acerca del pedido.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }
}
