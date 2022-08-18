<?php

namespace SysSoftIntegra\Model;

use PDO;
use SysSoftIntegra\DataBase\Database;
use Exception;
use DateTime;


class NotaCreditoADO
{

    function construct()
    {
    }

    public static function DetalleNotaCreditoSunat($idNotaCredito)
    {
        try {
            $cmdCabecera = Database::getInstance()->getDb()->prepare("SELECT 
            co.codigo AS codcomprobante,
            nc.serie,
            nc.numeracion,
            nc.fecha,
            nc.hora,
            1 AS tipo,
            m.nombre AS nommoneda,
            m.simbolo,
            m.codiso,
            tp.codigo AS coddocumento,
            cl.documento,
            cl.informacion,
            mt.codigo AS codmotivo,
            mt.nombre AS descmotivo,
            cop.codigo AS codcomprobantemod,
            c.serie AS seriemod,
            c.numeracion AS numeracionmod
            FROM notaCredito AS nc 
            INNER JOIN motivo AS mt ON mt.idMotivo = nc.idMotivo 
            INNER JOIN comprobante AS co ON co.idComprobante  = nc.idComprobante 
            INNER JOIN moneda AS m ON m.idMoneda = nc.idMoneda
            INNER JOIN cliente AS cl ON cl.idCliente  = nc.idCliente 
            INNER JOIN tipoDocumento AS tp ON tp.idTipoDocumento = cl.idTipoDocumento 
            INNER JOIN cobro AS c ON nc.idCobro = c.idCobro
            INNER JOIN comprobante AS cop ON cop.idComprobante  = c.idComprobante 
            WHERE nc.idNotaCredito = ?");
            $cmdCabecera->bindParam(1, $idNotaCredito, PDO::PARAM_STR);
            $cmdCabecera->execute();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT
            tp.codigo AS coddocumento,
            e.documento AS ruc,
            e.razonSocial,
            e.nombreEmpresa,
            e.direccion,
            e.useSol, 
            e.claveSol
            FROM empresa AS e  
            INNER JOIN tipoDocumento AS tp ON tp.idTipoDocumento = e.idTipoDocumento 
            LIMIT 1");
            $cmdEmpresa->execute();

            $cmdSede = Database::getInstance()->getDb()->prepare("SELECT
            ub.ubigeo,
            ub.departamento,
            ub.provincia,
            ub.distrito,
            s.telefono,
            s.email
            FROM sede AS s 
            INNER JOIN ubigeo AS ub ON ub.idUbigeo = s.idUbigeo  
            WHERE s.idSede  = 'SD0001'");
            $cmdSede->execute();

            $cmdDetalle = Database::getInstance()->getDb()->prepare("SELECT 
            CASE 
            WHEN nc.tipo = 0 THEN CONCAT('CUOTA',' ',pl.cuota)
            ELSE co.nombre END AS descripcion,
            md.codigo AS unidad,
            nc.cantidad,
            nc.precio,
            nc.idImpuesto,
            imp.nombre AS impuesto,
            imp.porcentaje AS impporcen,
            imp.codigo AS impcodido
            FROM notaCreditoDetalle AS nc 
            LEFT JOIN concepto AS co ON co.idConcepto = nc.idConcepto
            LEFT JOIN plazo AS pl ON pl.idPlazo = nc.idPlazo 
            INNER JOIN medida AS md ON md.idMedida = nc.idMedida
            INNER JOIN impuesto AS imp ON imp.idImpuesto = nc.idImpuesto
            WHERE nc.idNotaCredito = ?");
            $cmdDetalle->bindParam(1, $idNotaCredito, PDO::PARAM_STR);
            $cmdDetalle->execute();

            $detalle =  $cmdDetalle->fetchAll(PDO::FETCH_OBJ);

            $opegravada = 0;
            $opeexogenada = 0;

            $totalsinimp = 0;
            $totalconimp = 0;

            foreach ($detalle as $value) {
                $cantidad = floatval($value->cantidad);
                $impuesto = floatval($value->impporcen);
                $precioBruto = $value->precio / (($impuesto / 100.00) + 1);

                $opegravada += $impuesto == 0 ? 0 : $cantidad * $precioBruto;
                $opeexogenada += $impuesto == 0 ? $cantidad * $precioBruto : 0;

                $totalsinimp += $cantidad * $precioBruto;
                $totalconimp += $cantidad * ($precioBruto * ($impuesto / 100.00));
            }

            $totalimporte = $totalconimp + $totalsinimp;

            return array(
                (object)array_merge((array)$cmdSede->fetchObject(), (array) $cmdEmpresa->fetchObject()),
                $cmdCabecera->fetchObject(),
                $detalle,
                array(
                    "opegravada" => $opegravada,
                    "opeexonerada" => $opeexogenada,
                    "totalsinimpuesto" => $totalsinimp,
                    "totalconimpuesto" => $totalconimp,
                    "totalimporte" => $totalimporte
                )
            );
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function SunatSuccess($idNotaCredito , $codigo, $descripcion, $hash, $xmlgenerado)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $cmdValidate = Database::getInstance()->getDb()->prepare("UPDATE notaCredito SET
            xmlSunat=?, xmlDescripcion=?, codigoHash=?, xmlGenerado=? WHERE idNotaCredito   = ?");
            $cmdValidate->bindParam(1, $codigo, PDO::PARAM_STR);
            $cmdValidate->bindParam(2, $descripcion, PDO::PARAM_STR);
            $cmdValidate->bindParam(3, $hash, PDO::PARAM_STR);
            $cmdValidate->bindParam(4, $xmlgenerado, PDO::PARAM_STR);
            $cmdValidate->bindParam(5, $idNotaCredito , PDO::PARAM_STR);
            $cmdValidate->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }

    public static function SunatWarning($idCobro, $codigo, $descripcion)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE notaCredito SET 
            xmlSunat = ?, xmlDescripcion = ? WHERE idNotaCredito  = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $idCobro, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }

    public static function SunatResumenSuccess($idCobro, $codigo, $descripcion, $correlativo, $fechaCorrelativo)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE cobro SET 
              xmlSunat = ?, xmlDescripcion = ?, correlativo=?, fechaCorrelativo=? WHERE idCobro = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $correlativo, PDO::PARAM_INT);
            $comando->bindParam(4, $fechaCorrelativo, PDO::PARAM_STR);
            $comando->bindParam(5, $idCobro, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }
}
