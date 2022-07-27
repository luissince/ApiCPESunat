<?php

namespace SysSoftIntegra\Model;

use PDO;
use SysSoftIntegra\DataBase\Database;
use Exception;
use DateTime;


class VentasADO
{

    function construct()
    {
    }

    public static function ValidateDuplicate($array, $idProducto)
    {
        $ret = false;
        foreach ($array as $value) {
            if ($value["IdSuministro"] == $idProducto) {
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    public static function DetalleVentaSunat($idCobro)
    {
        try {
            $cmdCabecera = Database::getInstance()->getDb()->prepare("SELECT 
            co.codigo AS codcomprobante,
            v.serie,
            v.numeracion,
            v.fecha,
            v.hora,
            1 AS tipo,
            IFNULL(v.correlativo,0) AS correlativo,
            m.nombre AS nommoneda,
            m.simbolo,
            m.codiso,
            tp.codigo AS coddocumento,
            cl.documento,
            cl.informacion
            FROM cobro AS v 
            INNER JOIN comprobante AS co ON co.idComprobante  = v.idComprobante 
            INNER JOIN moneda AS m ON m.idMoneda = v.idMoneda
            INNER JOIN cliente AS cl ON cl.idCliente  = v.idCliente 
            INNER JOIN tipoDocumento AS tp ON tp.idTipoDocumento = cl.idTipoDocumento 
            WHERE v.idCobro  = ?");
            $cmdCabecera->bindParam(1, $idCobro, PDO::PARAM_STR);
            $cmdCabecera->execute();

            $cmdCorrelativo = Database::getInstance()->getDb()->prepare("SELECT 
            MAX(IFNULL(correlativo,0)) AS correlativo 
            FROM cobro 
            WHERE fechaCorrelativo = CURRENT_DATE()");
            $cmdCorrelativo->execute();

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
            md.codigo AS unidad,
            'CUOTA' AS descripcion,
            vd.precio,
            1 AS cantidad,
            im.porcentaje AS impporcen,
            im.codigo AS impcodido
            FROM
            cobroVenta AS vd
            INNER JOIN plazo AS p ON p.idPlazo  = vd.idPlazo  
            INNER JOIN medida AS md ON md.idMedida = vd.idMedida 
            INNER JOIN impuesto AS im ON vd.idImpuesto  = im.idImpuesto 
            WHERE vd.idCobro  = ?");
            $cmdDetalle->bindParam(1, $idCobro, PDO::PARAM_STR);
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
                $cmdCorrelativo->fetchColumn(),
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

    public static function CambiarEstadoSunatVenta($idCobro, $codigo, $descripcion, $hash, $xmlgenerado)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $cmdValidate = Database::getInstance()->getDb()->prepare("UPDATE cobro SET
            xmlSunat=?,xmlDescripcion=?,codigoHash=?,xmlGenerado=? WHERE idCobro  = ?");
            $cmdValidate->bindParam(1, $codigo, PDO::PARAM_STR);
            $cmdValidate->bindParam(2, $descripcion, PDO::PARAM_STR);
            $cmdValidate->bindParam(3, $hash, PDO::PARAM_STR);
            $cmdValidate->bindParam(4, $xmlgenerado, PDO::PARAM_STR);
            $cmdValidate->bindParam(5, $idCobro, PDO::PARAM_STR);
            $cmdValidate->execute();

            // $comando = Database::getInstance()->getDb()->prepare("UPDATE VentaTB SET 
            // Xmlsunat = ? , Xmldescripcion = ?, CodigoHash = ?,Xmlgenerado = ? WHERE IdVenta = ?");
            // $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            // $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            // $comando->bindParam(3, $hash, PDO::PARAM_STR);
            // $comando->bindParam(4, $xmlgenerado, PDO::PARAM_STR);
            // $comando->bindParam(5, $idVenta, PDO::PARAM_STR);
            // $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }

    public static function CambiarEstadoSunatVentaUnico($idVenta, $codigo, $descripcion)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE VentaTB SET 
            Xmlsunat = ? , Xmldescripcion = ? WHERE IdVenta = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $idVenta, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }

    public static function CambiarEstadoSunatResumen($idVenta, $codigo, $descripcion, $correlativo, $fechaCorrelativo)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE VentaTB SET 
              Xmlsunat = ? , Xmldescripcion = ?,Correlativo=?,FechaCorrelativo=? WHERE IdVenta = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $correlativo, PDO::PARAM_INT);
            $comando->bindParam(4, $fechaCorrelativo, PDO::PARAM_STR);
            $comando->bindParam(5, $idVenta, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }


}
