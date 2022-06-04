<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;

require_once __DIR__ . './../database/DataBaseConexion.php';

class CotizacionADO
{

    function __construct()
    {
    }

    public static function ListarCotizacion(int $opcion, string $buscar, string $fechaInicio, string $fechaFinal, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $cotizacion = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Cotizacion(?,?,?,?,?,?)}");
            $cotizacion->bindParam(1, $opcion, PDO::PARAM_INT);
            $cotizacion->bindParam(2, $buscar, PDO::PARAM_STR);
            $cotizacion->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cotizacion->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cotizacion->bindParam(5, $posicionPagina, PDO::PARAM_INT);
            $cotizacion->bindParam(6, $filasPorPagina, PDO::PARAM_INT);
            $cotizacion->execute();
            $arrayCotizacion = array();
            $count = 0;
            while ($row = $cotizacion->fetch()) {
                $count++;
                array_push($arrayCotizacion, array(
                    "Id" => $count + $posicionPagina,
                    "IdCotizacion" => $row['IdCotizacion'],
                    "FechaCotizacion" => $row['FechaCotizacion'],
                    "HoraCotizacion" => $row['HoraCotizacion'],
                    "Estado" => $row['Estado'],
                    "SimboloMoneda" => $row['SimboloMoneda'],
                    "Observaciones" => $row['Observaciones'],

                    "Apellidos" => $row['Apellidos'],
                    "Nombres" => $row['Nombres'],

                    "NumeroDocumento" => $row['NumeroDocumento'],
                    "Informacion" => $row['Informacion'],

                    "Comprobante" => $row['Comprobante'],
                    "Serie" => $row['Serie'],
                    "Numeracion" => $row['Numeracion'],

                    "Total" => floatval($row['Total']),
                ));
            }

            $cotizacion = Database::getInstance()->getDb()->prepare("{call Sp_Listar_Cotizacion_Count(?,?,?,?)}");
            $cotizacion->bindParam(1, $opcion, PDO::PARAM_INT);
            $cotizacion->bindParam(2, $buscar, PDO::PARAM_STR);
            $cotizacion->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cotizacion->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cotizacion->execute();
            $resultTotal = $cotizacion->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "data" => $arrayCotizacion,
                "total" => $resultTotal,
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function Sp_Obtener_Cotizacion_ById(string $idCotizacion)
    {
        try {
            $cmdCotizacion = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Cotizacion_ById(?)}");
            $cmdCotizacion->bindParam(1, $idCotizacion, PDO::PARAM_STR);
            $cmdCotizacion->execute();

            $cmdDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Detalle_Cotizacion_ById(?)}");
            $cmdDetalle->bindParam(1, $idCotizacion, PDO::PARAM_STR);
            $cmdDetalle->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array("data" => $cmdCotizacion->fetchObject(), "detalle" => $cmdDetalle->fetchAll(PDO::FETCH_OBJ));
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function CargarCotizacionVenta(string $idCotizacion)
    {
        try {
            $cmdCotizacion = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Cotizacion_ById(?)}");
            $cmdCotizacion->bindParam(1, $idCotizacion, PDO::PARAM_STR);
            $cmdCotizacion->execute();

            $cmdDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Detalle_Cotizacion_ById(?)}");
            $cmdDetalle->bindParam(1, $idCotizacion, PDO::PARAM_STR);
            $cmdDetalle->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array($cmdCotizacion->fetchObject(), $cmdDetalle->fetchAll(PDO::FETCH_OBJ));
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ReporteCotizacionDetalle(int $idCotizacion)
    {
        try {
            $cmdCotizacion = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Cotizacion_ById(?)}");
            $cmdCotizacion->bindParam(1, $idCotizacion, PDO::PARAM_INT);
            $cmdCotizacion->execute();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,
            e.NumeroDocumento,
            e.RazonSocial,
            e.NombreComercial,
            e.Domicilio,
            e.Telefono,
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
                "Condiciones" => $rowEmpresa['Condiciones'],
                "Image" => $rowEmpresa['Image'] == null ? "" : base64_encode($rowEmpresa['Image'])
            );

            $cmdBanco = Database::getInstance()->getDb()->prepare("SELECT 
            b.NombreCuenta,
            b.NumeroCuenta,
            m.Nombre as Moneda,
            b.FormaPago
            FROM Banco as b
            INNER JOIN MonedaTB AS m ON m.IdMoneda = b.IdMoneda 
            WHERE Mostrar = 1");
            $cmdBanco->execute();

            $cmdDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Detalle_Cotizacion_ById(?)}");
            $cmdDetalle->bindParam(1, $idCotizacion, PDO::PARAM_STR);
            $cmdDetalle->execute();

            return array(
                "cotizacion" => $cmdCotizacion->fetchObject(),
                "empresa" => $empresa,
                "banco" => $cmdBanco->fetchAll(PDO::FETCH_OBJ),
                "detalle" => $cmdDetalle->fetchAll(PDO::FETCH_ASSOC)
            );
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function CrudCotizacion($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT * FROM CotizacionTB WHERE IdCotizacion = ?");
            $cmdValidate->bindParam(1, $body["idCotizacion"], PDO::PARAM_INT);
            $cmdValidate->execute();
            if ($cmdValidate->fetch()) {

                $cmdCotizacion = Database::getInstance()->getDb()->prepare("UPDATE CotizacionTB SET
                IdCliente=?,
                IdVendedor=?,
                IdMoneda=?,
                FechaCotizacion=?,
                HoraCotizacion=GETDATE(),
                FechaVencimiento=?,
                HoraVencimiento=GETDATE(),
                Estado=?,
                Observaciones=?
                WHERE IdCotizacion = ?");

                $cmdCotizacion->execute(array(
                    $body["idCliente"],
                    $body["idEmpleado"],
                    $body["idMoneda"],
                    $body["fechaEmision"],
                    $body["fechaVencimientto"],
                    $body["estado"],
                    $body["observacion"],
                    $body["idCotizacion"]
                ));

                $cmdBorrar = Database::getInstance()->getDb()->prepare("DELETE FROM DetalleCotizacionTB WHERE IdCotizacion = ?");
                $cmdBorrar->execute(array($body["idCotizacion"]));

                $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO DetalleCotizacionTB
                (IdCotizacion,
                IdSuministro,
                Cantidad,
                Precio,
                Descuento,
                IdImpuesto,
                IdMedida)
                VALUES(?,?,?,?,?,?,?)");
                foreach ($body["detalle"] as $value) {
                    $cmdDetalle->execute(array(
                        $body["idCotizacion"],
                        $value["idSuministro"],
                        $value["cantidad"],
                        $value["precioVentaGeneral"],
                        $value["descuento"],
                        $value["idImpuesto"],
                        $value["idUnidadCompra"],
                    ));
                }

                Database::getInstance()->getDb()->commit();
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 201 . ' ' . "Created");

                return "Se actualizó correctamente la cotización.";
            } else {

                $codigoCotizacion = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Cotizacion_Codigo_Alfanumerico()");
                $codigoCotizacion->execute();
                $idCotizacion = $codigoCotizacion->fetchColumn();

                $cmdCotizacion = Database::getInstance()->getDb()->prepare("INSERT INTO CotizacionTB
                (IdCotizacion,
                IdCliente,
                IdVendedor,
                IdMoneda,
                FechaCotizacion,
                HoraCotizacion,
                FechaVencimiento,
                HoraVencimiento,
                Estado,
                Observaciones)
                VALUES(?,?,?,?,?,GETDATE(),?,GETDATE(),?,?)");
                $cmdCotizacion->execute(array(
                    $idCotizacion,
                    $body["idCliente"],
                    $body["idEmpleado"],
                    $body["idMoneda"],
                    $body["fechaEmision"],
                    $body["fechaVencimientto"],
                    $body["estado"],
                    $body["observacion"]
                ));

                $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO DetalleCotizacionTB
                (IdCotizacion,
                IdSuministro,
                Cantidad,
                Precio,
                Descuento,
                IdImpuesto,
                IdMedida)
                VALUES(?,?,?,?,?,?,?)");
                foreach ($body["detalle"] as $value) {
                    $cmdDetalle->execute(array(
                        $idCotizacion,
                        $value["idSuministro"],
                        $value["cantidad"],
                        $value["precioVentaGeneral"],
                        $value["descuento"],
                        $value["idImpuesto"],
                        $value["idUnidadCompra"],
                    ));
                }

                Database::getInstance()->getDb()->commit();
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 201 . ' ' . "Created");

                return "Se registró correctamente la cotización.";
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function DeleteCotizacionById($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdCotizacion = Database::getInstance()->getDb()->prepare("DELETE FROM CotizacionTB WHERE IdCotizacion = ?");
            $cmdCotizacion->execute(array($body["idCotizacion"]));

            $cmdCotizacion = Database::getInstance()->getDb()->prepare("DELETE FROM DetalleCotizacionTB WHERE IdCotizacion = ?");
            $cmdCotizacion->execute(array($body["idCotizacion"]));

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se eliminó correctamente la cotización.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }
}
