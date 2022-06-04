<?php

namespace SysSoftIntegra\Model;

use SysSoftIntegra\Src\Tools;
use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';

class NotaCreditoADO
{

    function __construct()
    {
    }

    public static function ListaNotaCredito($opcion, $buscar, $fechaInicio, $fechaFinal, $posicionPagina, $filasPorPagina)
    {
        try {
            $cmdNotaCredito = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_NotaCredito(?,?,?,?,?,?)}");
            $cmdNotaCredito->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdNotaCredito->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdNotaCredito->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cmdNotaCredito->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdNotaCredito->bindParam(5, $posicionPagina, PDO::PARAM_INT);
            $cmdNotaCredito->bindParam(6, $filasPorPagina, PDO::PARAM_INT);
            $cmdNotaCredito->execute();

            $arrayNotaCredito = array();
            $count = 0;
            while ($row = $cmdNotaCredito->fetch()) {
                $count++;
                array_push($arrayNotaCredito, array(
                    "Id" => $count+ $posicionPagina,
                    "IdVenta" => $row["IdVenta"],
                    "IdNotaCredito" => $row["IdNotaCredito"],
                    "FechaNotaCredito" => $row["FechaRegistro"],
                    "HoraNotaCredito" => $row["HoraRegistro"],
                    "SerieNotaCredito" => $row["SerieNotaCredito"],
                    "NumeracionNotaCredito" => $row["NumeracioNotaCredito"],
                    "Serie" => $row["SerieVenta"],
                    "Numeracion" => $row["NumeracionVenta"],
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Informacion" => $row["Informacion"],
                    "Estado" => $row["Estado"],
                    "Simbolo" => $row["Simbolo"],
                    "Total" => floatval($row["Total"])
                ));
            }

            $cmdNotaCreditCount = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_NotaCredito_Count(?,?,?,?)}");
            $cmdNotaCreditCount->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdNotaCreditCount->bindParam(2, $buscar, PDO::PARAM_STR);
            $cmdNotaCreditCount->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cmdNotaCreditCount->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdNotaCreditCount->execute();
            $resultTotal = $cmdNotaCreditCount->fetchColumn();

            Tools::httpStatus200();

            return array("data" => $arrayNotaCredito, "total" => $resultTotal);
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }

    public static function ObtenerNotaCreditoById($idNotaCredito)
    {
        try {
            $array = array();
            $opegravada = 0;
            $opeexogenerada = 0;
            $totalsinimpuesto = 0;
            $impuesto = 0;

            $cmdNotaCredito = Database::getInstance()->getDb()->prepare("SELECT
            n.IdNotaCredito, 
            tdn.CodigoAlterno as TipoDocumentoNotaCredito,
            tdn.Nombre as Comprobante,
            n.Serie as SerieNotaCredito,
            n.Numeracion as NumeracionNotaCredito,
            n.FechaRegistro as FechaNotaCredito,
            n.HoraRegistro as HoraNotaCredito,
            tpv.CodigoAlterno,
            v.Serie,
            v.Numeracion,
            dt.IdAuxiliar as CodigoAnulacion,
            dt.Nombre as MotivoAnulacion,
            m.Nombre as NombreMoneda,
            m.Abreviado as TipoMoneda,
            m.Simbolo as Simbolo,
            dtc.IdAuxiliar as CodigoCliente,
            c.NumeroDocumento,
            c.Informacion,
            c.Direccion,
            isnull(n.CodigoHash,'') as CodigoHash
            from NotaCreditoTB as n 
            inner join TipoDocumentoTB as tdn on tdn.IdTipoDocumento = n.Comprobante
            inner join VentaTB as v on v.IdVenta = n.IdVenta
            inner join TipoDocumentoTB as tpv on tpv.IdTipoDocumento = v.Comprobante
            inner join DetalleTB as dt on dt.IdDetalle = n.Motivo and dt.IdMantenimiento = '0019'
            inner join MonedaTB as m on m.IdMoneda = n.Moneda
            inner join ClienteTB as c on c.IdCliente = n.Cliente
            inner join DetalleTB as dtc on dtc.IdDetalle = c.TipoDocumento and dtc.IdMantenimiento = '0003'
            WHERE n.IdNotaCredito = ?");
            $cmdNotaCredito->bindValue(1, $idNotaCredito, PDO::PARAM_STR);
            $cmdNotaCredito->execute();
            $resultNotaCredito = $cmdNotaCredito->fetchObject();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,
            e.NumeroDocumento,
            e.RazonSocial,
            e.NombreComercial,
            e.Domicilio,
            dbo.Fc_Obtener_Ubigeo(Ubigeo) as CodigoUbigeo,
            dbo.Fc_Obtener_Departamento(Ubigeo) as Departamento,
            dbo.Fc_Obtener_Provincia(Ubigeo) as Provincia,
            dbo.Fc_Obtener_Distrito(Ubigeo) as Distrito,
            e.Telefono,
            e.Email,
            e.Terminos,
            e.Condiciones,
            e.UsuarioSol,
            e.ClaveSol 
            FROM EmpresaTB AS e INNER JOIN DetalleTB AS d ON e.TipoDocumento = d.IdDetalle AND d.IdMantenimiento = '0003'");
            $cmdEmpresa->execute();
            $resultEmpresa = $cmdEmpresa->fetchObject();

            $cmdDetalleNotaCredito =  Database::getInstance()->getDb()->prepare("SELECT 
            s.ClaveSat,
            ISNULL(d.IdAuxiliar,'') as CodigoUnidad,
            d.Nombre as UnidadCompra,
            s.Clave,
            s.NombreMarca,
            nd.Cantidad,
            nd.Precio,
            nd.Descuento,
            nd.ValorImpuesto,
            i.Codigo,
            isnull(i.Numeracion,'') as Numeracion,
	        isnull(i.Letra,'') as Letra,
	        isnull(i.Categoria,'') as Categoria
            from NotaCreditoDetalleTB nd 
            inner join SuministroTB as s on s.IdSuministro = nd.IdSuministro
            inner join ImpuestoTB as i on s.Impuesto = i.IdImpuesto
            left join DetalleTB AS d ON d.IdDetalle = s.UnidadCompra AND d.IdMantenimiento = '0013'
            where nd.IdNotaCredito = ?");
            $cmdDetalleNotaCredito->bindValue(1, $idNotaCredito, PDO::PARAM_STR);
            $cmdDetalleNotaCredito->execute();

            $count = 0;
            $arrayDetalleNotaCredito = array();
            while ($row = $cmdDetalleNotaCredito->fetch()) {
                $count++;
                array_push($arrayDetalleNotaCredito, array(
                    "Id" => $count,
                    "ClaveSat" => $row["ClaveSat"],
                    "CodigoUnidad" => $row["CodigoUnidad"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Cantidad" => $row["Cantidad"],
                    "Precio" => $row["Precio"],
                    "Descuento" => $row["Descuento"],
                    "ValorImpuesto" => $row["ValorImpuesto"],
                    "Codigo" => $row["Codigo"],
                    "UnidadCompra" => $row["UnidadCompra"],
                    "Numeracion" => $row["Numeracion"],
                    "Letra" => $row["Letra"],
                    "Categoria" => $row["Categoria"]
                ));
            }

            $cmdBanco = Database::getInstance()->getDb()->prepare("SELECT 
            b.NombreCuenta,
            b.NumeroCuenta,
            m.Nombre as Moneda,
            b.FormaPago
            FROM Banco as b
            INNER JOIN MonedaTB AS m ON m.IdMoneda = b.IdMoneda 
            WHERE Mostrar = 1");
            $cmdBanco->execute();


            foreach ($arrayDetalleNotaCredito as $value) {
                $cantidad = $value['Cantidad'];
                $valorImpuesto = $value['ValorImpuesto'];
                $precioBruto = $value['Precio'] / (($valorImpuesto / 100.00) + 1);

                $opegravada +=  $valorImpuesto == 0 ? 0 : $cantidad * $precioBruto;
                $opeexogenerada += $valorImpuesto == 0 ? $cantidad * $precioBruto : 0;

                $totalsinimpuesto += $cantidad * $precioBruto;
                $impuesto += $cantidad  * ($precioBruto * ($valorImpuesto / 100.00));
            }

            array_push(
                $array,
                $resultNotaCredito,
                $resultEmpresa,
                $arrayDetalleNotaCredito,
                array(
                    "opgravada" => $opegravada,
                    "opexonerada" =>   $opeexogenerada,
                    "totalsinimpuesto" => $totalsinimpuesto,
                    "totalimpuesto" => $impuesto,
                    "totalconimpuesto" => $totalsinimpuesto + $impuesto
                ),
                $cmdBanco->fetchAll(PDO::FETCH_OBJ)
            );
            return $array;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function  ListarDetalleNotaCredito($idNotaCredito)
    {
        try {

            $notacredito = null;
            $empresa = null;
            $notacreditodetalle = array();

            $comandoNotaCredito = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_NotaCredito_ById(?)}");
            $comandoNotaCredito->bindParam(1, $idNotaCredito, PDO::PARAM_STR);
            $comandoNotaCredito->execute();
            $notacredito = $comandoNotaCredito->fetchObject();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,e.NumeroDocumento,e.RazonSocial,e.NombreComercial,e.Domicilio,
            e.Telefono,e.Celular,e.Email,e.Image
            FROM EmpresaTB AS e INNER JOIN DetalleTB AS d ON e.TipoDocumento = d.IdDetalle AND d.IdMantenimiento = '0003'");
            $cmdEmpresa->execute();
            $rowEmpresa = $cmdEmpresa->fetch();
            $empresa  = (object)array(
                "IdAuxiliar" => $rowEmpresa['IdAuxiliar'],
                "NumeroDocumento" => $rowEmpresa['NumeroDocumento'],
                "RazonSocial" => $rowEmpresa['RazonSocial'],
                "NombreComercial" => $rowEmpresa['NombreComercial'],
                "Domicilio" => $rowEmpresa['Domicilio'],
                "Telefono" => $rowEmpresa['Telefono'],
                "Celular" => $rowEmpresa['Celular'],
                "Email" => $rowEmpresa['Email'],
                "Image" => $rowEmpresa['Image'] == null ? "" : base64_encode($rowEmpresa['Image'])
            );

            $comandoNotaCreditoDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_NotaCredito_Detalle_ById(?)}");
            $comandoNotaCreditoDetalle->bindParam(1, $idNotaCredito, PDO::PARAM_STR);
            $comandoNotaCreditoDetalle->execute();
            $count = 0;
            while ($row = $comandoNotaCreditoDetalle->fetch()) {
                $count++;
                array_push($notacreditodetalle, array(
                    "id" => $count,
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Unidad" => $row["Unidad"],
                    "Cantidad" => floatval($row["Cantidad"]),
                    "Precio" => floatval($row["Precio"]),
                    "Descuento" => floatval($row["Descuento"]),
                    "NombreImpuesto" => $row["NombreImpuesto"],
                    "ValorImpuesto" => floatval($row["ValorImpuesto"]),
                    "Importe" => floatval($row["Cantidad"] * ($row["Precio"] - $row["Descuento"])),
                ));
            }
            
            Tools::httpStatus200();

            return  array(
                "nota" => $notacredito,
                "notadetalle" => $notacreditodetalle,
                "empresa" => $empresa
            );
        } catch (Exception $ex) {
            Tools::httpStatus500();

            return $ex->getMessage();
        }
    }


    public static function CambiarEstadoSunatNotaCredito($idNotaCredito, $codigo, $descripcion, $hash, $xmlgenerado)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE NotaCreditoTB SET 
            Xmlsunat = ? , Xmldescripcion = ?, CodigoHash = ?,Xmlgenerado = ?  WHERE IdNotaCredito = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $hash, PDO::PARAM_STR);
            $comando->bindParam(4, $xmlgenerado, PDO::PARAM_STR);
            $comando->bindParam(5, $idNotaCredito, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }

    public static function CambiarEstadoSunatNotaCreditoUnico($idNotaCredito, $codigo, $descripcion)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $comando = Database::getInstance()->getDb()->prepare("UPDATE NotaCreditoTB SET 
            Xmlsunat = ? , Xmldescripcion = ?  WHERE IdNotaCredito = ?");
            $comando->bindParam(1, $codigo, PDO::PARAM_STR);
            $comando->bindParam(2, $descripcion, PDO::PARAM_STR);
            $comando->bindParam(3, $idNotaCredito, PDO::PARAM_STR);
            $comando->execute();
            Database::getInstance()->getDb()->commit();
            return "updated";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return $ex->getMessage();
        }
    }
}
