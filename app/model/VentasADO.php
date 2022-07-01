<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';


class VentasADO
{

    function construct()
    {
    }

    public static function ListVentas(int $opcion, string $value, string $fechaInicial, string $fechaFinal, int $comprobante, int $estado, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $comandoVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_All(?,?,?,?,?,?,?,?)}");
            $comandoVenta->bindParam(1, $opcion, PDO::PARAM_INT);
            $comandoVenta->bindParam(2, $value, PDO::PARAM_STR);
            $comandoVenta->bindParam(3, $fechaInicial, PDO::PARAM_STR);
            $comandoVenta->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $comandoVenta->bindParam(5, $comprobante, PDO::PARAM_INT);
            $comandoVenta->bindParam(6, $estado, PDO::PARAM_INT);
            $comandoVenta->bindParam(7, $posicionPagina, PDO::PARAM_STR);
            $comandoVenta->bindParam(8, $filasPorPagina, PDO::PARAM_STR);
            $comandoVenta->execute();
            $arrayVenta = array();
            $count = 0;
            while ($row = $comandoVenta->fetch()) {
                $count++;
                array_push($arrayVenta, array(
                    "id" => $count + $posicionPagina,
                    "IdVenta" => $row["IdVenta"],
                    "FechaVenta" => $row["FechaVenta"],
                    "HoraVenta" => $row["HoraVenta"],
                    "ApellidosVendedor" => $row["ApellidosVendedor"],
                    "NombresVendedor" => $row["NombresVendedor"],
                    "DocumentoCliente" => $row["DocumentoCliente"],
                    "Cliente" => $row["Cliente"],
                    "Comprobante" => $row["Comprobante"],
                    "TipoComprobante" => $row['TipoComprobante'],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],
                    "Tipo" => $row["Tipo"],
                    "Estado" => $row["Estado"],
                    "FormaName" => $row["FormaName"],
                    "Simbolo" => $row["Simbolo"],
                    "NombreMoneda" => $row["NombreMoneda"],
                    "TipoMoneda" => $row["TipoMoneda"],
                    "Total" => $row["Total"],
                    "Observaciones" => $row["Observaciones"],
                    "IdNotaCredito" => $row["IdNotaCredito"],
                    "SerieNotaCredito" => $row["SerieNotaCredito"],
                    "NumeracionNotaCredito" => $row["NumeracionNotaCredito"]
                ));
            }

            $comandoTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_All_Count(?,?,?,?,?,?)}");
            $comandoTotal->bindParam(1, $opcion, PDO::PARAM_INT);
            $comandoTotal->bindParam(2, $value, PDO::PARAM_STR);
            $comandoTotal->bindParam(3, $fechaInicial, PDO::PARAM_STR);
            $comandoTotal->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $comandoTotal->bindParam(5, $comprobante, PDO::PARAM_INT);
            $comandoTotal->bindParam(6, $estado, PDO::PARAM_INT);
            $comandoTotal->execute();
            $resultTotal = $comandoTotal->fetchColumn();

            $comandoSuma = Database::getInstance()->getDb()->prepare("SELECT 
            ISNULL(sum(dv.Cantidad*(dv.PrecioVenta-dv.Descuento)),0) AS Total
            FROM VentaTB as v 
            INNER JOIN DetalleVentaTB as dv on dv.IdVenta = v.IdVenta
            LEFT JOIN NotaCreditoTB as nc on nc.IdVenta = v.IdVenta
            WHERE 
            CAST(v.FechaVenta AS DATE) BETWEEN ? AND ? AND v.Tipo = 1 AND v.Estado = 1 and nc.IdNotaCredito is null
            OR
            CAST(v.FechaVenta AS DATE) BETWEEN ? AND ? AND v.Tipo = 2 AND v.Estado = 1 and nc.IdNotaCredito is null
            OR
            CAST(v.FechaVenta AS DATE) BETWEEN ? AND ? AND v.Tipo = 1 AND v.Estado = 4 and nc.IdNotaCredito is null
            ");
            $comandoSuma->bindParam(1, $fechaInicial, PDO::PARAM_STR);
            $comandoSuma->bindParam(2, $fechaFinal, PDO::PARAM_STR);
            $comandoSuma->bindParam(3, $fechaInicial, PDO::PARAM_STR);
            $comandoSuma->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $comandoSuma->bindParam(5, $fechaInicial, PDO::PARAM_STR);
            $comandoSuma->bindParam(6, $fechaFinal, PDO::PARAM_STR);
            $comandoSuma->execute();
            $resultSuma = 0;
            if ($row = $comandoSuma->fetch()) {
                $resultSuma = floatval(round($row["Total"], 2, PHP_ROUND_HALF_UP));
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "data" => $arrayVenta,
                "total" => $resultTotal,
                "suma" => $resultSuma
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ListComprobantes($opcion, $busqueda, $fechaInicio, $fechaFinal, $comprobante, $estado, $posicionPagina, $filasPorPagina)
    {
        try {
            $cmdComprobantes = Database::getInstance()->getDb()->prepare("{CALL Sp_Lista_Cpe_Comprobantes(?,?,?,?,?,?,?,?)}");
            $cmdComprobantes->bindParam(1, $opcion, PDO::PARAM_STR);
            $cmdComprobantes->bindParam(2, $busqueda, PDO::PARAM_STR);
            $cmdComprobantes->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cmdComprobantes->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdComprobantes->bindParam(5, $comprobante, PDO::PARAM_INT);
            $cmdComprobantes->bindParam(6, $estado, PDO::PARAM_INT);
            $cmdComprobantes->bindParam(7, $posicionPagina, PDO::PARAM_INT);
            $cmdComprobantes->bindParam(8, $filasPorPagina, PDO::PARAM_INT);
            $cmdComprobantes->execute();

            $arrayComprobantes = array();
            while ($row = $cmdComprobantes->fetch(PDO::FETCH_OBJ)) {
                array_push($arrayComprobantes, $row);
            }

            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Lista_Cpe_Comprobantes_Count(?,?,?,?,?,?)}");
            $cmdTotal->bindParam(1, $opcion, PDO::PARAM_STR);
            $cmdTotal->bindParam(2, $busqueda, PDO::PARAM_STR);
            $cmdTotal->bindParam(3, $fechaInicio, PDO::PARAM_STR);
            $cmdTotal->bindParam(4, $fechaFinal, PDO::PARAM_STR);
            $cmdTotal->bindParam(5, $comprobante, PDO::PARAM_INT);
            $cmdTotal->bindParam(6, $estado, PDO::PARAM_INT);
            $cmdTotal->execute();

            $resulTotal = 0;
            while ($row = $cmdTotal->fetch()) {
                $resulTotal += $row['Total'];
            }


            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array("data" => $arrayComprobantes, "total" =>  $resulTotal);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ListVentaDetalle($idventa)
    {
        try {

            $venta = null;
            $ventadetalle = array();

            $comandoVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Venta_ById(?)}");
            $comandoVenta->bindParam(1, $idventa, PDO::PARAM_STR);
            $comandoVenta->execute();
            $venta = $comandoVenta->fetchObject();

            $comandoVentaDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Detalle_By_Id(?)}");
            $comandoVentaDetalle->bindParam(1, $idventa, PDO::PARAM_STR);
            $comandoVentaDetalle->execute();
            $count = 0;
            while ($row = $comandoVentaDetalle->fetch()) {
                $count++;
                array_push($ventadetalle, array(
                    "id" => $count,
                    "IdSuministro" => $row["IdSuministro"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Inventario" => $row["Inventario"],
                    "ValorInventario" => $row["ValorInventario"],
                    "ClaveSat" => $row["ClaveSat"],
                    "CodigoUnidad" => $row["CodigoUnidad"],
                    "UnidadCompra" => $row["UnidadCompra"],
                    "Cantidad" => floatval($row["Cantidad"]),
                    "Bonificacion" => floatval($row["Bonificacion"]),
                    "CostoVenta" => floatval($row["CostoVenta"]),
                    "PrecioVenta" => floatval($row["PrecioVenta"]),
                    "Descuento" => floatval($row["Descuento"]),
                    "IdImpuesto" => $row["IdImpuesto"],
                    "NombreImpuesto" => $row["NombreImpuesto"],
                    "ValorImpuesto" => floatval($row["ValorImpuesto"]),
                    "Codigo" => $row["Codigo"]
                ));
            }


            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "venta" => $venta,
                "ventadetalle" => $ventadetalle
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }


    public static function ReporteVentaDetalle($idventa)
    {
        try {

            $venta = null;
            $ventadetalle = array();

            $comandoVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Venta_ById(?)}");
            $comandoVenta->bindParam(1, $idventa, PDO::PARAM_STR);
            $comandoVenta->execute();
            $venta = $comandoVenta->fetchObject();

            $comandoVentaDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Detalle_By_Id(?)}");
            $comandoVentaDetalle->bindParam(1, $idventa, PDO::PARAM_STR);
            $comandoVentaDetalle->execute();
            $count = 0;
            while ($row = $comandoVentaDetalle->fetch()) {
                $count++;
                array_push($ventadetalle, array(
                    "id" => $count,
                    "IdSuministro" => $row["IdSuministro"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Inventario" => $row["Inventario"],
                    "ValorInventario" => $row["ValorInventario"],
                    "ClaveSat" => $row["ClaveSat"],
                    "CodigoUnidad" => $row["CodigoUnidad"],
                    "UnidadCompra" => $row["UnidadCompra"],
                    "Cantidad" => floatval($row["Cantidad"]),
                    "CostoVenta" => floatval($row["CostoVenta"]),
                    "PrecioVenta" => floatval($row["PrecioVenta"]),
                    "Descuento" => floatval($row["Descuento"]),
                    "IdImpuesto" => $row["IdImpuesto"],
                    "NombreImpuesto" => $row["NombreImpuesto"],
                    "ValorImpuesto" => floatval($row["ValorImpuesto"]),
                    "Codigo" => $row["Codigo"]
                ));
            }

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

            $cmdBanco = Database::getInstance()->getDb()->prepare("SELECT 
            b.NombreCuenta,
            b.NumeroCuenta,
            m.Nombre as Moneda,
            b.FormaPago
            FROM Banco as b
            INNER JOIN MonedaTB AS m ON m.IdMoneda = b.IdMoneda 
            WHERE Mostrar = 1");
            $cmdBanco->execute();

            return array(
                "venta" => $venta,
                "ventadetalle" => $ventadetalle,
                "empresa" => $empresa,
                "banco" => $cmdBanco->fetchAll(PDO::FETCH_OBJ)
            );
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public static function ListarUtilidad(string $fechaInicio, string $fechaFinal, string $idSuministro, int $idCategoria, int $idMarca, int $idPresentacion)
    {
        try {
            $cmdUtilidad = Database::getInstance()->getDb()->prepare("{call Sp_Listar_Utilidad(?,?,?,?,?,?)}");
            $cmdUtilidad->bindValue(1, $fechaInicio, PDO::PARAM_STR);
            $cmdUtilidad->bindValue(2, $fechaFinal, PDO::PARAM_STR);
            $cmdUtilidad->bindValue(3, $idSuministro, PDO::PARAM_INT);
            $cmdUtilidad->bindValue(4, $idCategoria, PDO::PARAM_STR);
            $cmdUtilidad->bindValue(5, $idMarca, PDO::PARAM_INT);
            $cmdUtilidad->bindValue(6, $idPresentacion, PDO::PARAM_INT);
            $cmdUtilidad->execute();

            $arrayUtilidad = array();
            while ($row = $cmdUtilidad->fetch()) {
                array_push($arrayUtilidad, array(
                    "IdSuministro" => $row["IdSuministro"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Cantidad" => $row["Cantidad"],
                    "UnidadCompraNombre" => $row["UnidadCompraNombre"],
                    "Costo" => floatval($row["Costo"]),
                    "CostoTotal" => floatval($row["CostoTotal"]),
                    "Precio" => floatval($row["Precio"]),
                    "PrecioTotal" => floatval($row["PrecioTotal"]),
                    "Utilidad" => floatval($row["Utilidad"]),
                    "ValorInventario" => $row["ValorInventario"],
                    "Simbolo" => $row["Simbolo"]
                ));
            }

            return $arrayUtilidad;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function ResumenProductoVendidos($fechaInicio, $fechaFinal, $marca, $categoria)
    {
        try {
            $cmdProductos = Database::getInstance()->getDb()->prepare("SELECT 
            a.IdSuministro,
            a.Clave, 
            a.NombreMarca,         
            sum(dv.Cantidad) as Cantidad, 
            sum(dv.Cantidad*dv.CostoVenta) as CostoTotal,
			sum(dv.Cantidad*dv.PrecioVenta) as PrecioTotal,
			ds.Nombre as Medida,
            sum((dv.Cantidad * dv.PrecioVenta )- (dv.Cantidad * dv.CostoVenta )) as Utilidad
                    from DetalleVentaTB as dv
                    inner join SuministroTB as a on dv.IdArticulo = a.IdSuministro 
					inner join DetalleTB as ds on ds.IdDetalle = a.UnidadCompra and ds.IdMantenimiento = '0013'
                    inner join VentaTB as v on v.IdVenta = dv.IdVenta
                    left join NotaCreditoTB as nc on nc.IdVenta = v.IdVenta
                    inner join MonedaTB as m on m.IdMoneda = v.Moneda
                    where v.FechaVenta between ? and ? and  v.Estado <> 3 and ? = 0 and ? = 0 and nc.IdNotaCredito is null
                    or v.FechaVenta between ? and ? and  v.Estado <> 3	and a.Marca = ? and  ? = 0 and nc.IdNotaCredito is null
                    or v.FechaVenta between ? and ? and  v.Estado <> 3	and ? = 0  and a.Categoria = ? and nc.IdNotaCredito is null
                    or v.FechaVenta between ? and ? and  v.Estado <> 3	and a.Marca = ? and a.Categoria = ? and nc.IdNotaCredito is null
					group by   
					a.IdSuministro,
                    a.Clave, 
                    a.NombreMarca,
			        ds.Nombre	
                    order by Utilidad desc,Cantidad desc, Medida asc");
            $cmdProductos->bindValue(1, $fechaInicio, PDO::PARAM_STR);
            $cmdProductos->bindValue(2, $fechaFinal, PDO::PARAM_STR);
            $cmdProductos->bindValue(3, $marca, PDO::PARAM_INT);
            $cmdProductos->bindValue(4, $categoria, PDO::PARAM_INT);

            $cmdProductos->bindValue(5, $fechaInicio, PDO::PARAM_STR);
            $cmdProductos->bindValue(6, $fechaFinal, PDO::PARAM_STR);
            $cmdProductos->bindValue(7, $marca, PDO::PARAM_INT);
            $cmdProductos->bindValue(8, $categoria, PDO::PARAM_INT);

            $cmdProductos->bindValue(9, $fechaInicio, PDO::PARAM_STR);
            $cmdProductos->bindValue(10, $fechaFinal, PDO::PARAM_STR);
            $cmdProductos->bindValue(11, $marca, PDO::PARAM_INT);
            $cmdProductos->bindValue(12, $categoria, PDO::PARAM_INT);

            $cmdProductos->bindValue(13, $fechaInicio, PDO::PARAM_STR);
            $cmdProductos->bindValue(14, $fechaFinal, PDO::PARAM_STR);
            $cmdProductos->bindValue(15, $marca, PDO::PARAM_INT);
            $cmdProductos->bindValue(16, $categoria, PDO::PARAM_INT);

            $cmdProductos->execute();
            $arrayDetalle = array();
            $count = 0;
            while ($row = $cmdProductos->fetch()) {
                $count++;
                array_push($arrayDetalle, array(
                    "Id" => $count,
                    "IdSuministro" => $row["IdSuministro"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Cantidad" => floatval($row["Cantidad"]),
                    "CostoTotal" => floatval($row["CostoTotal"]),
                    "PrecioTotal" => floatval($row["PrecioTotal"]),
                    "Medida" => $row["Medida"],
                    "Utilidad" => floatval($row["Utilidad"])
                ));
            }
            return array(
                "estado" => 1,
                "data" => $arrayDetalle
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 2,
                "message" => $ex->getMessage(),
            );
        }
    }

    public static function ResumenGeneral($fechaInicial, $fechaFinal)
    {
        try {
            $array = array();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,e.NumeroDocumento,e.RazonSocial,e.NombreComercial,e.Domicilio,
            e.Telefono,e.Email,e.Image
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
                "Email" => $rowEmpresa['Email'],
                "Image" => $rowEmpresa['Image'] == null ? "" : base64_encode($rowEmpresa['Image'])
            );

            array_push($array, $empresa);
            return $array;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function ListVentasMostrarLibres($opcion, $search, $posicionPagina, $filasPorPagina)
    {
        try {

            $cmdVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Mostrar(?,?,?,?)}");
            $cmdVenta->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdVenta->bindParam(2, $search, PDO::PARAM_STR);
            $cmdVenta->bindParam(3, $posicionPagina, PDO::PARAM_INT);
            $cmdVenta->bindParam(4, $filasPorPagina, PDO::PARAM_INT);
            $cmdVenta->execute();

            $count = 0;
            $arrayVenta = array();
            while ($row = $cmdVenta->fetch()) {
                $count++;
                array_push($arrayVenta, array(
                    "Id" => $count + $posicionPagina,
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Cliente" => $row["Cliente"],
                    "IdVenta" => $row["IdVenta"],
                    "FechaVenta" => $row["FechaVenta"],
                    "HoraVenta" => $row["HoraVenta"],
                    "Comprobante" => $row["Comprobante"],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],
                    "Simbolo" => $row["Simbolo"],
                    "Total" => floatval($row["Total"]),
                    "IdNotaCredito" => $row["IdNotaCredito"],
                    "SerieNotaCredito" => $row["SerieNotaCredito"],
                    "NumeracionNotaCredito" => $row["NumeracionNotaCredito"]
                ));
            }

            $cmdTotal = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Mostrar_Count(?,?)}");
            $cmdTotal->bindParam(1, $opcion, PDO::PARAM_INT);
            $cmdTotal->bindParam(2, $search, PDO::PARAM_STR);
            $cmdTotal->execute();
            $resultTotal = $cmdTotal->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "data" => $arrayVenta,
                "total" => $resultTotal
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function VentaAgregarTerminar(string $idVenta)
    {
        try {

            $cmdVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Obtener_Venta_ById(?)}");
            $cmdVenta->bindParam(1, $idVenta, PDO::PARAM_STR);
            $cmdVenta->execute();

            $array = array();
            if ($row = $cmdVenta->fetch()) {
                array_push($array, array(
                    "IdVenta" => $idVenta,
                    "FechaVenta" => $row["FechaVenta"],
                    "HoraVenta" => $row["HoraVenta"],
                    "IdCliente" => $row["IdCliente"],
                    "IdAuxiliar" => $row["IdAuxiliar"],
                    "TipoDocumento" => $row["TipoDocumento"],
                    "NombreDocumento" => $row["NombreDocumento"],
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Informacion" => $row["Informacion"],
                    "Telefono" => $row["Telefono"],
                    "Celular" => $row["Celular"],
                    "Email" => $row["Email"],
                    "Direccion" => $row["Direccion"],
                    "IdMoneda" => $row["IdMoneda"],
                    "CodigoAlterno" => $row["CodigoAlterno"],
                    "IdComprobante" => $row["IdComprobante"]
                ));

                $cmdDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Detalle_By_Id(?)}");
                $cmdDetalle->bindParam(1, $idVenta, PDO::PARAM_STR);
                $cmdDetalle->execute();
                $arrayDetalle = array();
                while ($rowd = $cmdDetalle->fetch()) {
                    array_push($arrayDetalle, array(
                        "IdSuministro" => $rowd["IdSuministro"],
                        "Clave" => $rowd["Clave"],
                        "NombreMarca" => $rowd["NombreMarca"],
                        "Inventario" => $rowd["Inventario"],
                        "ValorInventario" => $rowd["ValorInventario"],
                        "UnidadCompra" => $rowd["UnidadCompra"],
                        "Estado" => $rowd["Estado"],
                        "PorLlevar" => $rowd["PorLlevar"],
                        "Cantidad" => $rowd["Cantidad"],
                        "Bonificacion" => $rowd["Bonificacion"],
                        "CostoVenta" => $rowd["CostoVenta"],
                        "Operacion" => $rowd["Operacion"],
                        "NombreImpuesto" => $rowd["NombreImpuesto"],
                        "IdImpuesto" => $rowd["IdImpuesto"],
                        "ValorImpuesto" => $rowd["ValorImpuesto"],
                        "PrecioVenta" => $rowd["PrecioVenta"],
                        "Inventario" => $rowd["Inventario"],
                        "UnidadVenta" => $rowd["UnidadVenta"],
                        "ValorInventario" => $rowd["ValorInventario"],
                    ));
                }

                array_push($array,  $arrayDetalle);

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 200 . ' ' . "OK");

                return $array;
            } else {
                throw new Exception("No hay datos para mostrar.");
            }
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function TopProductoVendidos($fechaInicial, $fechaFinal)
    {
        try {
            $array = array();

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,e.NumeroDocumento,e.RazonSocial,e.NombreComercial,e.Domicilio,
            e.Telefono,e.Email,e.Image
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
                "Email" => $rowEmpresa['Email'],
                "Image" => $rowEmpresa['Image'] == null ? "" : base64_encode($rowEmpresa['Image'])
            );

            $cmdTopVentas = Database::getInstance()->getDb()->prepare("SELECT 
            c.NumeroDocumento,
            c.Informacion ,
            count(v.IdVenta) as NumeroVentas ,
            sum(dv.Cantidad*dv.PrecioVenta) as MontoComprado
            from 
            ClienteTB as c 
            inner join VentaTB as v on c.IdCliente = v.Cliente
            inner join DetalleVentaTB as dv on dv.IdVenta = v.IdVenta
            where v.Estado <> 3 and v.FechaVenta between ? and ?
            group by c.NumeroDocumento,c.Informacion
            order by MontoComprado desc,NumeroVentas desc");
            $cmdTopVentas->bindValue(1, $fechaInicial, PDO::PARAM_STR);
            $cmdTopVentas->bindValue(2, $fechaFinal, PDO::PARAM_STR);
            $cmdTopVentas->execute();

            $count = 0;
            $arrayDetalle = array();
            while ($row = $cmdTopVentas->fetch()) {
                $count++;
                array_push($arrayDetalle, array(
                    "Id" => $count,
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Informacion" => $row["Informacion"],
                    "NumeroVentas" => $row["NumeroVentas"],
                    "MontoComprado" => floatval($row["MontoComprado"])
                ));
            }
            array_push($array, $empresa, $arrayDetalle);
            return $array;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
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

    public static function ListarDetalleVentPorId($idVenta)
    {
        try {
            $lista = array();
            $resultVenta = null;
            $detalleventa = array();
            $opegravada = 0;
            $opeexogenerada = 0;
            $totalsinimpuesto = 0;
            $numeroitems = 0;
            $impuesto = 0;
            $resultEmpresa = null;
            $resultCliente = null;

            $cmdVenta = Database::getInstance()->getDb()->prepare("SELECT 
                dbo.Fc_Obtener_Nombre_Moneda(v.Moneda) as NombreMoneda,
		        dbo.Fc_Obtener_Abreviatura_Moneda(v.Moneda) as TipoMoneda,
                td.CodigoAlterno as TipoComprobante,
                v.Serie,v.Numeracion,v.FechaVenta,v.HoraVenta,
                ISNULL(v.Correlativo,0) as Correlativo
                FROM VentaTB as v inner join TipoDocumentoTB as td 
                on v.Comprobante = td.IdTipoDocumento
                WHERE v.IdVenta = ?");
            $cmdVenta->bindParam(1, $idVenta, PDO::PARAM_STR);
            $cmdVenta->execute();
            $resultVenta = $cmdVenta->fetchObject();

            $cmdCorrelativo = Database::getInstance()->getDb()->prepare("SELECT MAX(ISNULL(Correlativo,0)) as Correlativo FROM VentaTB WHERE FechaCorrelativo = CAST(GETDATE() AS DATE)");
            $cmdCorrelativo->execute();
            $resultCorrelativo = $cmdCorrelativo->fetchColumn();

            $cmdDetail = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Ventas_Detalle_By_Id(?)}");
            $cmdDetail->bindParam(1, $idVenta, PDO::PARAM_STR);
            $cmdDetail->execute();
            $count = 0;

            while ($rowdetailt = $cmdDetail->fetch()) {
                $count++;
                $numeroitems++;
                array_push($detalleventa, array(
                    "Id" => $count,
                    "Clave" => $rowdetailt["Clave"],
                    "NombreMarca" => $rowdetailt["NombreMarca"],
                    "ClaveSat" => $rowdetailt["ClaveSat"],
                    "CodigoUnidad" => $rowdetailt["CodigoUnidad"],
                    "Cantidad" => $rowdetailt["Cantidad"],
                    "PrecioVenta" => $rowdetailt["PrecioVenta"],
                    "Descuento" => $rowdetailt["Descuento"],
                    "ValorImpuesto" => $rowdetailt["ValorImpuesto"],
                    "Codigo" => $rowdetailt["Codigo"],
                    "Numeracion" => $rowdetailt["Numeracion"],
                    "NombreImpuesto" => $rowdetailt["NombreImpuesto"],
                    "Letra" => $rowdetailt["Letra"],
                    "Categoria" => $rowdetailt["Categoria"]
                ));
            }

            foreach ($detalleventa as $value) {
                $cantidad = $value['Cantidad'];
                $valorImpuesto = $value['ValorImpuesto'];
                $precioBruto = $value['PrecioVenta'] / (($valorImpuesto / 100.00) + 1);

                $opegravada +=  $valorImpuesto == 0 ? 0 : $cantidad * $precioBruto;
                $opeexogenerada += $valorImpuesto == 0 ? $cantidad * $precioBruto : 0;

                $totalsinimpuesto += $cantidad * $precioBruto;
                $impuesto += $cantidad  * ($precioBruto * ($valorImpuesto / 100.00));
            }

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar,e.NumeroDocumento,e.RazonSocial,e.NombreComercial,e.Domicilio,
            dbo.Fc_Obtener_Ubigeo(Ubigeo) as CodigoUbigeo,
            dbo.Fc_Obtener_Departamento(Ubigeo) as Departamento,
            dbo.Fc_Obtener_Provincia(Ubigeo) as Provincia,
            dbo.Fc_Obtener_Distrito(Ubigeo) as Distrito,
            e.Telefono,e.Email,
            e.UsuarioSol,e.ClaveSol 
            FROM EmpresaTB AS e INNER JOIN DetalleTB AS d ON e.TipoDocumento = d.IdDetalle AND d.IdMantenimiento = '0003'");
            $cmdEmpresa->execute();
            $resultEmpresa = $cmdEmpresa->fetchObject();

            $cmdCliente = Database::getInstance()->getDb()->prepare("SELECT d.IdAuxiliar,c.NumeroDocumento,c.Informacion
            FROM ClienteTB AS c INNER JOIN VentaTB AS v ON c.IdCliente = v.Cliente
            INNER JOIN DetalleTB as d on c.TipoDocumento = d.IdDetalle and d.IdMantenimiento = '0003'
            WHERE v.IdVenta = ? ");
            $cmdCliente->bindParam(1, $idVenta, PDO::PARAM_STR);
            $cmdCliente->execute();
            $resultCliente = $cmdCliente->fetchObject();

            array_push(
                $lista,
                array(
                    "opgravada" => $opegravada,
                    "opexonerada" =>   $opeexogenerada,
                    "totalsinimpuesto" => $totalsinimpuesto,
                    "totalimpuesto" => $impuesto,
                    "totalconimpuesto" => $totalsinimpuesto + $impuesto,
                    "numeroitems" => $numeroitems,
                    "detalle" => $detalleventa
                ),
                $resultEmpresa,
                $resultCliente,
                $resultVenta,
                $resultCorrelativo
            );

            return $lista;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function DetalleVentaSunat($idVenta)
    {
        try {
            $cmdCabecera = Database::getInstance()->getDb()->prepare("SELECT 
            co.codigo AS codcomprobante,
            v.serie,
            v.numeracion,
            v.fecha,
            v.hora,
            v.tipo,
            v.credito,
            v.frecuencia,
            IFNULL(v.correlativo,0) AS correlativo,
            m.nombre AS nommoneda,
            m.simbolo,
            m.codiso,
            tp.codigo AS coddocumento,
            cl.documento,
            cl.informacion
            FROM venta AS v 
            INNER JOIN comprobante AS co ON co.idComprobante  = v.idComprobante 
            INNER JOIN moneda AS m ON m.idMoneda = v.idMoneda
            INNER JOIN cliente AS cl ON cl.idCliente  = v.idCliente 
            INNER JOIN tipoDocumento AS tp ON tp.idTipoDocumento = cl.idTipoDocumento 
            WHERE v.idVenta = ?");
            $cmdCabecera->bindParam(1, $idVenta, PDO::PARAM_STR);
            $cmdCabecera->execute();

            $cmdCorrelativo = Database::getInstance()->getDb()->prepare("SELECT 
            MAX(IFNULL(correlativo,0)) AS correlativo 
            FROM venta 
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
            l.descripcion,
            vd.precio,
            vd.cantidad,
            im.porcentaje AS impporcen,
            im.codigo AS impcodido
            FROM
            ventaDetalle AS vd
            INNER JOIN lote AS l ON l.idLote = vd.idLote 
            INNER JOIN medida AS md ON md.idMedida = l.idMedida 
            INNER JOIN impuesto AS im ON vd.idImpuesto  = im.idImpuesto 
            WHERE vd.idVenta = ?");
            $cmdDetalle->bindParam(1, $idVenta, PDO::PARAM_STR);
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

    public static function CambiarEstadoSunatVenta($idVenta, $codigo, $descripcion, $hash, $xmlgenerado)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $cmdValidate = Database::getInstance()->getDb()->prepare("UPDATE venta SET
            xmlSunat=?,xmlDescripcion=?,codigoHash=?,xmlGenerado=? WHERE idVenta = ?");
            $cmdValidate->bindParam(1, $codigo, PDO::PARAM_STR);
            $cmdValidate->bindParam(2, $descripcion, PDO::PARAM_STR);
            $cmdValidate->bindParam(3, $hash, PDO::PARAM_STR);
            $cmdValidate->bindParam(4, $xmlgenerado, PDO::PARAM_STR);
            $cmdValidate->bindParam(5, $idVenta, PDO::PARAM_STR);
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

    public static function GenerarComprobantesFacturados($fechaInicial, $fechaFinal)
    {
        try {
            $array = array();

            $arrayVentas = array();
            $comando = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Cpe_Facturados(?,?)}");
            $comando->bindParam(1, $fechaInicial, PDO::PARAM_STR);
            $comando->bindParam(2, $fechaFinal, PDO::PARAM_STR);
            $comando->execute();
            $count = 0;
            while ($row = $comando->fetch()) {
                $count++;
                array_push($arrayVentas, array(
                    "Id" => $count,
                    "Tipo" => $row["Tipo"],
                    "FechaRegistro" => $row["FechaRegistro"],
                    "Nombre" => $row["Nombre"],
                    "TipoComprobante" => $row["TipoComprobante"],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],
                    "TipoDocumento" => $row["TipoDocumento"],
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Informacion" => $row["Informacion"],
                    "Base" => floatval($row["Base"]),
                    "Igv" => floatval($row["Igv"]),
                    "Xmlsunat" => $row["Xmlsunat"],
                    "Xmldescripcion" => $row["Xmldescripcion"]
                ));
            }

            $cmdEmpresa = Database::getInstance()->getDb()->prepare("SELECT TOP 1 
            d.IdAuxiliar
            ,e.NumeroDocumento,
            e.RazonSocial,
            e.Telefono,
            e.Email,
            e.UsuarioSol,
            e.ClaveSol 
            FROM EmpresaTB AS e 
            INNER JOIN DetalleTB AS d ON e.TipoDocumento = d.IdDetalle AND d.IdMantenimiento = '0003'");
            $cmdEmpresa->execute();
            $resultEmpresa = $cmdEmpresa->fetchObject();

            array_push($array, $arrayVentas,  $resultEmpresa);
            return $array;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    public static function ListarComprobanteParaNotaCredito($comprobante)
    {
        try {
            $cmdNotaCredito = Database::getInstance()->getDb()->prepare("SELECT IdTipoDocumento,Nombre FROM TipoDocumentoTB WHERE NotaCredito = 1");
            $cmdNotaCredito->execute();

            $arrayNotaCredito = array();
            while ($row = $cmdNotaCredito->fetch()) {
                array_push($arrayNotaCredito, array(
                    "IdTipoDocumento" => $row["IdTipoDocumento"],
                    "Nombre" => $row["Nombre"]
                ));
            }

            $cmdMoneda = Database::getInstance()->getDb()->prepare("SELECT IdMoneda,Nombre,Predeterminado FROM MonedaTB");
            $cmdMoneda->execute();

            $arrayMoneda = array();
            while ($row = $cmdMoneda->fetch()) {
                array_push($arrayMoneda, array(
                    "IdMoneda" => $row["IdMoneda"],
                    "Nombre" => $row["Nombre"],
                    "Predeterminado" => $row["Predeterminado"]
                ));
            }


            $cmdTipoComprobante = Database::getInstance()->getDb()->prepare("SELECT IdTipoDocumento,Nombre FROM TipoDocumentoTB");
            $cmdTipoComprobante->execute();

            $arrayTipoComprobante = array();
            while ($row = $cmdTipoComprobante->fetch()) {
                array_push($arrayTipoComprobante, array(
                    "IdTipoDocumento" => $row["IdTipoDocumento"],
                    "Nombre" => $row["Nombre"]
                ));
            }

            $cmdMotivoAnulacion = Database::getInstance()->getDb()->prepare("SELECT IdDetalle,Nombre FROM DetalleTB WHERE IdMantenimiento = '0019'");
            $cmdMotivoAnulacion->execute();
            $arrayMotivoAnulacion = array();
            while ($row = $cmdMotivoAnulacion->fetch()) {
                array_push($arrayMotivoAnulacion, array(
                    "IdDetalle" => $row["IdDetalle"],
                    "Nombre" => $row["Nombre"],
                ));
            }

            $cmdTipoDocumento = Database::getInstance()->getDb()->prepare("SELECT IdDetalle,Nombre FROM DetalleTB WHERE IdMantenimiento = '0003'");
            $cmdTipoDocumento->execute();

            $arrayTipoDocumento = array();
            while ($row = $cmdTipoDocumento->fetch()) {
                array_push($arrayTipoDocumento, array(
                    "IdDetalle" => $row["IdDetalle"],
                    "Nombre" => $row["Nombre"],
                ));
            }

            $cmdVenta = Database::getInstance()->getDb()->prepare("SELECT 
            v.IdVenta,
            v.Comprobante,
            v.Serie,
            v.Numeracion,
            c.IdCliente,
            c.TipoDocumento,
            c.NumeroDocumento,
            c.Informacion,
            c.Direccion,
            c.Celular,
            c.Email 
            FROM VentaTB AS v INNER JOIN ClienteTB AS c ON c.IdCliente = v.Cliente
            WHERE CONCAT(v.Serie,'-',v.Numeracion) = ? ");
            $cmdVenta->bindValue(1, $comprobante, PDO::PARAM_STR);
            $cmdVenta->execute();
            $resultVenta = $cmdVenta->fetchObject();

            $cmdDetalle = Database::getInstance()->getDb()->prepare("SELECT 
            dv.IdVenta,
            dv.IdArticulo,
            s.Clave,
            s.NombreMarca,
            isnull(d.Nombre,'') UnidadMarca,
            dv.Cantidad,
            dv.PrecioVenta,
            dv.Descuento,
            dv.ValorImpuesto,
            dv.NombreImpuesto 
            FROM DetalleVentaTB AS dv 
            INNER JOIN SuministroTB AS s ON s.IdSuministro = dv.IdArticulo
            LEFT JOIN DetalleTB AS d ON d.IdDetalle = s.UnidadCompra AND d.IdMantenimiento = '0013'
            WHERE dv.IdVenta = ?");
            $cmdDetalle->bindValue(1, $resultVenta->IdVenta, PDO::PARAM_STR);
            $cmdDetalle->execute();

            $arrayDetalle = array();
            while ($row = $cmdDetalle->fetch()) {
                array_push($arrayDetalle, array(
                    "IdVenta" => $row["IdVenta"],
                    "IdArticulo" => $row["IdArticulo"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "UnidadMarca" => $row["UnidadMarca"],
                    "Cantidad" => $row["Cantidad"],
                    "PrecioVenta" => $row["PrecioVenta"],
                    "Descuento" => $row["Descuento"],
                    "ValorImpuesto" => $row["ValorImpuesto"],
                    "NombreImpuesto" => $row["NombreImpuesto"]
                ));
            }

            return array(
                "estado" => 1,
                "notaCredito" => $arrayNotaCredito,
                "monedas" => $arrayMoneda,
                "tipoComprobante" => $arrayTipoComprobante,
                "motivoAnulacion" => $arrayMotivoAnulacion,
                "tipoDocumento" => $arrayTipoDocumento,
                "venta" => $resultVenta,
                "detalle" => $arrayDetalle
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 2,
                "message" => $ex->getMessage(),
            );
        }
    }

    public static function RegistrarNotaCredito($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdCodigoNotaCredito = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_NotaCredito_Codigo_Alfanumerico();");
            $cmdCodigoNotaCredito->execute();
            $codigoNotaCredito = $cmdCodigoNotaCredito->fetchColumn();

            $cmdSerieNumeracion = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Serie_Numero(?)");
            $cmdSerieNumeracion->bindValue(1, $body["IdNotaCredito"], PDO::PARAM_INT);
            $cmdSerieNumeracion->execute();
            $serieNumeracion = explode('-', $cmdSerieNumeracion->fetchColumn());

            $cmdNotaCredito = Database::getInstance()->getDb()->prepare("INSERT INTO NotaCreditoTB(
                IdNotaCredito,
                Vendedor,
                Cliente,
                Comprobante,
                Moneda,
                Serie,
                Numeracion,
                Motivo,
                FechaRegistro,
                HoraRegistro,
                IdVenta,
                Estado) 
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $cmdNotaCredito->bindValue(1, $codigoNotaCredito, PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(2, $body["IdVendedor"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(3, $body["IdCliente"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(4, $body["IdNotaCredito"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(5, $body["IdMoneda"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(6, $serieNumeracion[0], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(7, $serieNumeracion[1], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(8, $body["IdMotivo"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(9, $body["FechaRegistro"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(10, $body["HoraRegistro"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(11, $body["IdVenta"], PDO::PARAM_STR);
            $cmdNotaCredito->bindValue(12, $body["Estado"], PDO::PARAM_INT);
            $cmdNotaCredito->execute();

            $cmdComprobante = Database::getInstance()->getDb()->prepare("INSERT INTO ComprobanteTB(IdTipoDocumento,Serie,Numeracion,FechaRegistro)VALUES(?,?,?,?)");
            $cmdComprobante->bindValue(1, $body["IdNotaCredito"], PDO::PARAM_INT);
            $cmdComprobante->bindValue(2, $serieNumeracion[0], PDO::PARAM_STR);
            $cmdComprobante->bindValue(3, $serieNumeracion[1], PDO::PARAM_STR);
            $cmdComprobante->bindValue(4, $body["FechaRegistro"], PDO::PARAM_STR);
            $cmdComprobante->execute();

            $cmdNotaCreditoDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO NotaCreditoDetalleTB(
            IdNotaCredito,
            IdSuministro,
            Cantidad,
            Precio,
            Descuento,
            ValorImpuesto) 
            VALUES(?,?,?,?,?,?)");

            foreach ($body["detalle"] as $value) {
                $cmdNotaCreditoDetalle->bindValue(1, $codigoNotaCredito, PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->bindValue(2, $value["IdArticulo"], PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->bindValue(3, $value["Cantidad"], PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->bindValue(4, $value["PrecioVenta"], PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->bindValue(5, $value["Descuento"], PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->bindValue(6, $value["ValorImpuesto"], PDO::PARAM_STR);
                $cmdNotaCreditoDetalle->execute();
            }

            Database::getInstance()->getDb()->commit();
            return array(
                "estado" => 1,
                "message" => "Se registro correctamente la nota de crédito."
            );
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            return array(
                "estado" => 0,
                "message" => $ex->getMessage(),
            );
        }
    }


    public static function ListarNotificaciones()
    {
        try {
            $array = array();

            $cmdNotificaciones = Database::getInstance()->getDb()->prepare("SELECT 
            v.Serie,
            td.Nombre,
            case v.Estado 
            when 3 
            then 'Dar de Baja'
            else 'Por Declarar' end as Estado,
            count(v.Serie) AS Cantidad
            FROM VentaTB AS v 
            INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = v.Comprobante            
            WHERE
            td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') <> '0' AND ISNULL(v.Xmlsunat,'') <> '1032'
            OR
            td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') = '0' AND v.Estado = 3
            GROUP BY v.Serie,td.Nombre,v.Estado");
            $cmdNotificaciones->execute();
            while ($row = $cmdNotificaciones->fetch()) {
                array_push($array, array(
                    "Nombre" => $row["Nombre"],
                    "Estado" => $row["Estado"],
                    "Cantidad" => $row["Cantidad"]
                ));
            }

            $cmdNotificaciones = Database::getInstance()->getDb()->prepare("SELECT 
            nc.Serie,
            td.Nombre,
            case nc.Estado 
            when 3
            then 'Dar de Baja'
            else 'Por Declarar' end as Estado,
            count(nc.Serie) AS Cantidad
            FROM NotaCreditoTB AS nc
            INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = nc.Comprobante            
            WHERE
            td.Facturacion = 1 AND ISNULL(nc.Xmlsunat,'') <> '0' AND ISNULL(nc.Xmlsunat,'') <> '1032'
            GROUP BY nc.Serie,td.Nombre,nc.Estado");
            $cmdNotificaciones->execute();
            while ($row =  $cmdNotificaciones->fetch()) {
                array_push($array, array(
                    "Nombre" => $row["Nombre"],
                    "Estado" => $row["Estado"],
                    "Cantidad" => $row["Cantidad"]
                ));
            }

            return array(
                "estado" => 1,
                "data" => $array
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 2,
                "message" => $ex->getMessage(),
            );
        }
    }

    public static function ListarDetalleNotificaciones($posicionPagina, $filasPorPagina)
    {
        try {

            $cmdNotificaciones = Database::getInstance()->getDb()->prepare("SELECT 
            v.FechaVenta as Fecha,
			v.HoraVenta as Hora,
            v.Serie,
            v.Numeracion,
            td.Nombre,
            case v.Estado 
            when 3 then 'Dar de Baja'
            ELSE 'Por Declarar' end as Estado            
            FROM VentaTB AS v 
			INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = v.Comprobante			
            WHERE
			td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') <> '0' AND ISNULL(v.Xmlsunat,'') <> '1032'
			OR
			td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') = '0' AND v.Estado = 3 
			
            UNION

			SELECT
			nc.FechaRegistro AS Fecha,
			nc.HoraRegistro AS Hora,
            nc.Serie,
            nc.Numeracion,
            td.Nombre,
            CASE nc.Estado 
            WHEN 3 THEN 'Dar de Baja'
            ELSE 'Por Declarar' END AS Estado
			FROM NotaCreditoTB AS nc
			INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = nc.Comprobante			
			WHERE
			td.Facturacion = 1 AND ISNULL(nc.Xmlsunat,'') <> '0' AND ISNULL(nc.Xmlsunat,'') <> '1032'
            ORDER BY Fecha DESC,Hora DESC
            offset ? ROWS FETCH NEXT ? ROWS only");
            $cmdNotificaciones->bindParam(1, $posicionPagina, PDO::PARAM_INT);
            $cmdNotificaciones->bindParam(2, $filasPorPagina, PDO::PARAM_INT);
            $cmdNotificaciones->execute();
            $resultLista = $cmdNotificaciones->fetchAll(PDO::FETCH_OBJ);


            $cmdNotificaciones = Database::getInstance()->getDb()->prepare("SELECT 
            count(v.IdVenta) as Total              
            FROM VentaTB AS v 
			INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = v.Comprobante			
            WHERE
			td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') <> '0' AND ISNULL(v.Xmlsunat,'') <> '1032'
			OR
			td.Facturacion = 1 AND ISNULL(v.Xmlsunat,'') = '0' AND v.Estado = 3 
			UNION

			SELECT
			count(nc.IdNotaCredito) as Total
			FROM NotaCreditoTB AS nc
			INNER JOIN TipoDocumentoTB AS td ON td.IdTipoDocumento = nc.Comprobante			
			WHERE
			td.Facturacion = 1 AND ISNULL(nc.Xmlsunat,'') <> '0' AND ISNULL(nc.Xmlsunat,'') <> '1032'");
            $cmdNotificaciones->execute();
            $resultTotal = 0;
            while ($row = $cmdNotificaciones->fetch()) {
                $resultTotal +=  $row["Total"];
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "estado" => 1,
                "data" => $resultLista,
                "total" => $resultTotal,
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }


    public static function GetDetalleId($idMantenimiento)
    {
        try {
            $cmdDetalle = Database::getInstance()->getDb()->prepare("{CALL Sp_Get_Detalle_Id(?)}");
            $cmdDetalle->bindValue(1, $idMantenimiento, PDO::PARAM_STR);
            $cmdDetalle->execute();

            return array(
                "estado" => 1,
                "data" => $cmdDetalle->fetchAll(PDO::FETCH_OBJ)
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 2,
                "message" => $ex->getMessage(),
            );
        }
    }

    public static function RegistrarVentaContado($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $idCliente = "";
            $dig5 = rand(10000, 90000);

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT IdCliente FROM ClienteTB WHERE NumeroDocumento = ?");
            $cmdValidate->bindParam(1, $body["NumeroDocumento"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($row = $cmdValidate->fetch()) {
                $idCliente = $row["IdCliente"];
                $cmdCliente = Database::getInstance()->getDb()->prepare("UPDATE ClienteTB SET TipoDocumento=?,Informacion = ?,Celular=?,Email=?,Direccion=? WHERE IdCliente =  ?");
                $cmdCliente->bindParam(1, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $row["IdCliente"], PDO::PARAM_STR);
                $cmdCliente->execute();
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
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $cmdCliente->bindParam(1, $idGeneradoCli, PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["NumeroDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Telefono"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(7, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(8, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(9, $body["Representante"], PDO::PARAM_STR);
                $cmdCliente->bindParam(10, $body["Estado"], PDO::PARAM_INT);
                $cmdCliente->bindParam(11, $body["Predeterminado"], PDO::PARAM_BOOL);
                $cmdCliente->bindParam(12, $body["Sistema"], PDO::PARAM_BOOL);
                $cmdCliente->execute();
                $idCliente = $idGeneradoCli;
            }

            $serie_numeracion = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Serie_Numero(?)");
            $serie_numeracion->bindParam(1, $body["IdComprobante"], PDO::PARAM_STR);
            $serie_numeracion->execute();
            $id_comprabante =  explode("-", $serie_numeracion->fetchColumn());

            $codigo_venta = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Venta_Codigo_Alfanumerico()");
            $codigo_venta->execute();
            $id_venta = $codigo_venta->fetchColumn();

            $venta = Database::getInstance()->getDb()->prepare("INSERT INTO VentaTB
                       (IdVenta
                       ,Cliente
                       ,Vendedor
                       ,Comprobante
                       ,Moneda
                       ,Serie
                       ,Numeracion
                       ,FechaVenta
                       ,HoraVenta
                       ,FechaVencimiento
                       ,HoraVencimiento                       
                       ,Tipo
                       ,Estado
                       ,Observaciones
                       ,Efectivo
                       ,Vuelto
                       ,Tarjeta
                       ,Codigo
                       ,Deposito
                       ,NumeroOperacion
                       ,Procedencia)
                 VALUES (?,?,?,?,?,?,?,GETDATE(),GETDATE(),?,?,?,?,?,?,?,?,?,?,?,1)");
            $venta->execute(array(
                $id_venta,
                $idCliente,
                $body["IdEmpleado"],
                $body["IdComprobante"],
                $body["IdMoneda"],
                $id_comprabante[0],
                $id_comprabante[1],
                $body["FechaVencimiento"],
                $body["HoraVencimiento"],
                $body["Tipo"],
                $body["Estado"],
                "",
                $body["Efectivo"],
                $body["Vuelto"],
                $body["Tarjeta"],
                $dig5 + $id_comprabante[1],
                $body["Deposito"],
                $body["NumeroOperacion"]
            ));

            $cmdComprobante = Database::getInstance()->getDb()->prepare("INSERT INTO ComprobanteTB(IdTipoDocumento,Serie,Numeracion,FechaRegistro)VALUES(?,?,?,GETDATE())");
            $cmdComprobante->execute(array(
                $body["IdComprobante"],
                $id_comprabante[0],
                $id_comprabante[1],
            ));

            $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO DetalleVentaTB
            (IdVenta
            ,IdArticulo
            ,Cantidad
            ,CostoVenta
            ,PrecioVenta
            ,Descuento
            ,IdOperacion
            ,IdImpuesto
            ,NombreImpuesto
            ,ValorImpuesto          
            ,Bonificacion
            ,PorLlevar
            ,Estado)
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $cmdSuministroUpdate = Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = Cantidad - ? WHERE IdSuministro = ?");

            $cmdSuministroKardex = Database::getInstance()->getDb()->prepare("INSERT INTO 
            KardexSuministroTB(
            IdSuministro,
            Fecha,
            Hora,
            Tipo,
            Movimiento,
            Detalle,
            Cantidad,
            Costo, 
            Total,
            IdAlmacen) 
            VALUES(?,GETDATE(),GETDATE(),?,?,?,?,?,?,?)");

            foreach ($body["Lista"] as $value) {
                // $cantidad = $value["valorInventario"] == 2 ? $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"] : $value["cantidad"];
                // $precio = $value["valorInventario"] == 2 ? $value["precioVentaGeneralAuxiliar"] : $value["precioVentaGeneral"];
                $cantidad = $value["cantidad"];

                $precio = $value["precioVentaGeneral"];

                $cmdDetalle->execute(array(
                    $id_venta,
                    $value["idSuministro"],
                    $cantidad,
                    $value["costoCompra"],
                    $precio,
                    $value["descuento"],
                    $value["impuestoOperacion"],
                    $value["idImpuesto"],
                    $value["impuestoNombre"],
                    $value["impuestoValor"],
                    $value["bonificacion"],
                    $cantidad,
                    "C"
                ));

                if ($value["inventario"] == "1" && $value["valorInventario"] == "1") {
                    $cmdSuministroUpdate->execute(array(
                        $value["cantidad"] + $value["bonificacion"],
                        $value["idSuministro"]
                    ));
                } else if ($value["inventario"] == "1" && $value["valorInventario"] == "2") {
                    $cmdSuministroUpdate->execute(array(
                        // $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"],
                        $value["cantidad"],
                        $value["idSuministro"]
                    ));
                } else if ($value["inventario"] == "1" && $value["valorInventario"] == "3") {
                    $cmdSuministroUpdate->execute(array(
                        $value["cantidad"],
                        $value["idSuministro"]
                    ));
                }

                if ($value["valorInventario"] == "1") {
                    $cantidadKardex = $value["cantidad"] + $value["bonificacion"];
                } else if ($value["valorInventario"] == "2") {
                    // $cantidadKardex = $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"];
                    $cantidadKardex = $value["cantidad"];
                } else {
                    $cantidadKardex = $value["cantidad"];
                }

                $cmdSuministroKardex->execute(array(
                    $value["idSuministro"],
                    2,
                    1,
                    "VENTA CON SERIE Y NUMERACIÓN: " . $id_comprabante[0] . "-" . $id_comprabante[1] . ($value["bonificacion"] <= 0 ? "" : "(BONIFICACIÓN: " . $value["bonificacion"] . ")"),
                    $cantidadKardex,
                    $value["costoCompra"],
                    $cantidadKardex * $value["costoCompra"],
                    0
                ));
            }

            $cmdIngreso = Database::getInstance()->getDb()->prepare("INSERT INTO IngresoTB(
                IdProcedencia,
                IdUsuario,
                Detalle,
                Procedencia,
                Fecha,
                Hora,
                Forma,
                Monto)
                VALUES(?,?,?,?,GETDATE(),GETDATE(),?,?)");

            if ($body["Deposito"] > 0) {
                $cmdIngreso->execute(array(
                    $id_venta,
                    $body["IdEmpleado"],
                    "VENTA CON DEPOSITO DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                    1,
                    3,
                    $body["Deposito"]
                ));
            } else {
                if ($body["Efectivo"] > 0) {
                    $cmdIngreso->execute(array(
                        $id_venta,
                        $body["IdEmpleado"],
                        "VENTA CON EFECTIVO DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                        1,
                        1,
                        $body["Tarjeta"] > 0 ? $body["Efectivo"] : $body["Total"]
                    ));
                }

                if ($body["Tarjeta"] > 0) {
                    $cmdIngreso->execute(array(
                        $id_venta,
                        $body["IdEmpleado"],
                        "VENTA CON TAJETA DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                        1,
                        2,
                        $body["Tarjeta"]
                    ));
                }
            }

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se registro correctamente la venta.";
        } catch (PDOException $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        }
    }

    public static function RegistrarVentaCredito($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $idCliente = "";
            $dig5 = rand(10000, 90000);

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT IdCliente FROM ClienteTB WHERE NumeroDocumento = ?");
            $cmdValidate->bindParam(1, $body["NumeroDocumento"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($row = $cmdValidate->fetch()) {
                $idCliente = $row["IdCliente"];
                $cmdCliente = Database::getInstance()->getDb()->prepare("UPDATE ClienteTB SET TipoDocumento=?,Informacion = ?,Celular=?,Email=?,Direccion=? WHERE IdCliente =  ?");
                $cmdCliente->bindParam(1, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $row["IdCliente"], PDO::PARAM_STR);
                $cmdCliente->execute();
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
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $cmdCliente->bindParam(1, $idGeneradoCli, PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["NumeroDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Telefono"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(7, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(8, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(9, $body["Representante"], PDO::PARAM_STR);
                $cmdCliente->bindParam(10, $body["Estado"], PDO::PARAM_INT);
                $cmdCliente->bindParam(11, $body["Predeterminado"], PDO::PARAM_BOOL);
                $cmdCliente->bindParam(12, $body["Sistema"], PDO::PARAM_BOOL);
                $cmdCliente->execute();
                $idCliente = $idGeneradoCli;
            }

            $serie_numeracion = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Serie_Numero(?)");
            $serie_numeracion->bindParam(1, $body["IdComprobante"], PDO::PARAM_STR);
            $serie_numeracion->execute();
            $id_comprabante =  explode("-", $serie_numeracion->fetchColumn());

            $codigo_venta = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Venta_Codigo_Alfanumerico()");
            $codigo_venta->execute();
            $id_venta = $codigo_venta->fetchColumn();

            $venta = Database::getInstance()->getDb()->prepare("INSERT INTO VentaTB
                       (IdVenta
                       ,Cliente
                       ,Vendedor
                       ,Comprobante
                       ,Moneda
                       ,Serie
                       ,Numeracion
                       ,FechaVenta
                       ,HoraVenta
                       ,FechaVencimiento
                       ,HoraVencimiento                       
                       ,Tipo
                       ,Estado
                       ,Observaciones
                       ,Efectivo
                       ,Vuelto
                       ,Tarjeta
                       ,Codigo
                       ,Deposito
                       ,NumeroOperacion
                       ,Procedencia)
                 VALUES (?,?,?,?,?,?,?,GETDATE(),GETDATE(),?,?,?,?,?,?,?,?,?,?,?,1)");
            $venta->execute(array(
                $id_venta,
                $idCliente,
                $body["IdEmpleado"],
                $body["IdComprobante"],
                $body["IdMoneda"],
                $id_comprabante[0],
                $id_comprabante[1],
                $body["FechaVencimiento"],
                $body["HoraVencimiento"],
                $body["Tipo"],
                $body["Estado"],
                "",
                $body["Efectivo"],
                $body["Vuelto"],
                $body["Tarjeta"],
                $dig5 + $id_comprabante[1],
                $body["Deposito"],
                $body["NumeroOperacion"]
            ));

            $cmdComprobante = Database::getInstance()->getDb()->prepare("INSERT INTO ComprobanteTB(IdTipoDocumento,Serie,Numeracion,FechaRegistro)VALUES(?,?,?,GETDATE())");
            $cmdComprobante->execute(array(
                $body["IdComprobante"],
                $id_comprabante[0],
                $id_comprabante[1],
            ));

            $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO DetalleVentaTB
            (IdVenta
            ,IdArticulo
            ,Cantidad
            ,CostoVenta
            ,PrecioVenta
            ,Descuento
            ,IdOperacion
            ,IdImpuesto
            ,NombreImpuesto
            ,ValorImpuesto          
            ,Bonificacion
            ,PorLlevar
            ,Estado)
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $cmdSuministroUpdate = Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = Cantidad - ? WHERE IdSuministro = ?");

            $cmdSuministroKardex = Database::getInstance()->getDb()->prepare("INSERT INTO 
            KardexSuministroTB(
            IdSuministro,
            Fecha,
            Hora,
            Tipo,
            Movimiento,
            Detalle,
            Cantidad,
            Costo, 
            Total,
            IdAlmacen) 
            VALUES(?,GETDATE(),GETDATE(),?,?,?,?,?,?,?)");

            foreach ($body["Lista"] as $value) {
                // $cantidad = $value["valorInventario"] == 2 ? $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"] : $value["cantidad"];
                // $precio = $value["valorInventario"] == 2 ? $value["precioVentaGeneralAuxiliar"] : $value["precioVentaGeneral"];
                $cantidad = $value["cantidad"];

                $precio = $value["precioVentaGeneral"];

                $cmdDetalle->execute(array(
                    $id_venta,
                    $value["idSuministro"],
                    $cantidad,
                    $value["costoCompra"],
                    $precio,
                    $value["descuento"],
                    $value["impuestoOperacion"],
                    $value["idImpuesto"],
                    $value["impuestoNombre"],
                    $value["impuestoValor"],
                    $value["bonificacion"],
                    $cantidad,
                    "C"
                ));

                if ($value["inventario"] == "1" && $value["valorInventario"] == "1") {
                    $cmdSuministroUpdate->execute(array(
                        $value["cantidad"] + $value["bonificacion"],
                        $value["idSuministro"]
                    ));
                } else if ($value["inventario"] == "1" && $value["valorInventario"] == "2") {
                    $cmdSuministroUpdate->execute(array(
                        // $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"],
                        $value["cantidad"],
                        $value["idSuministro"]
                    ));
                } else if ($value["inventario"] == "1" && $value["valorInventario"] == "3") {
                    $cmdSuministroUpdate->execute(array(
                        $value["cantidad"],
                        $value["idSuministro"]
                    ));
                }

                if ($value["valorInventario"] == "1") {
                    $cantidadKardex = $value["cantidad"] + $value["bonificacion"];
                } else if ($value["valorInventario"] == "2") {
                    // $cantidadKardex = $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"];
                    $cantidadKardex = $value["cantidad"];
                } else {
                    $cantidadKardex = $value["cantidad"];
                }

                $cmdSuministroKardex->execute(array(
                    $value["idSuministro"],
                    2,
                    1,
                    "VENTA CON SERIE Y NUMERACIÓN: " . $id_comprabante[0] . "-" . $id_comprabante[1] . ($value["bonificacion"] <= 0 ? "" : "(BONIFICACIÓN: " . $value["bonificacion"] . ")"),
                    $cantidadKardex,
                    $value["costoCompra"],
                    $cantidadKardex * $value["costoCompra"],
                    0
                ));
            }

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se registro correctamente la venta.";
        } catch (PDOException $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        }
    }

    public static function RegistrarVentaAdelantado($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $idCliente = "";
            $dig5 = rand(10000, 90000);

            $cmdValidate = Database::getInstance()->getDb()->prepare("SELECT IdCliente FROM ClienteTB WHERE NumeroDocumento = ?");
            $cmdValidate->bindParam(1, $body["NumeroDocumento"], PDO::PARAM_STR);
            $cmdValidate->execute();
            if ($row = $cmdValidate->fetch()) {
                $idCliente = $row["IdCliente"];
                $cmdCliente = Database::getInstance()->getDb()->prepare("UPDATE ClienteTB SET TipoDocumento=?,Informacion = ?,Celular=?,Email=?,Direccion=? WHERE IdCliente =  ?");
                $cmdCliente->bindParam(1, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $row["IdCliente"], PDO::PARAM_STR);
                $cmdCliente->execute();
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
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $cmdCliente->bindParam(1, $idGeneradoCli, PDO::PARAM_STR);
                $cmdCliente->bindParam(2, $body["TipoDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(3, $body["NumeroDocumento"], PDO::PARAM_STR);
                $cmdCliente->bindParam(4, $body["Informacion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(5, $body["Telefono"], PDO::PARAM_STR);
                $cmdCliente->bindParam(6, $body["Celular"], PDO::PARAM_STR);
                $cmdCliente->bindParam(7, $body["Email"], PDO::PARAM_STR);
                $cmdCliente->bindParam(8, $body["Direccion"], PDO::PARAM_STR);
                $cmdCliente->bindParam(9, $body["Representante"], PDO::PARAM_STR);
                $cmdCliente->bindParam(10, $body["Estado"], PDO::PARAM_INT);
                $cmdCliente->bindParam(11, $body["Predeterminado"], PDO::PARAM_BOOL);
                $cmdCliente->bindParam(12, $body["Sistema"], PDO::PARAM_BOOL);
                $cmdCliente->execute();
                $idCliente = $idGeneradoCli;
            }

            $serie_numeracion = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Serie_Numero(?)");
            $serie_numeracion->bindParam(1, $body["IdComprobante"], PDO::PARAM_STR);
            $serie_numeracion->execute();
            $id_comprabante =  explode("-", $serie_numeracion->fetchColumn());

            $codigo_venta = Database::getInstance()->getDb()->prepare("SELECT dbo.Fc_Venta_Codigo_Alfanumerico()");
            $codigo_venta->execute();
            $id_venta = $codigo_venta->fetchColumn();

            $venta = Database::getInstance()->getDb()->prepare("INSERT INTO VentaTB
                       (IdVenta
                       ,Cliente
                       ,Vendedor
                       ,Comprobante
                       ,Moneda
                       ,Serie
                       ,Numeracion
                       ,FechaVenta
                       ,HoraVenta
                       ,FechaVencimiento
                       ,HoraVencimiento                       
                       ,Tipo
                       ,Estado
                       ,Observaciones
                       ,Efectivo
                       ,Vuelto
                       ,Tarjeta
                       ,Codigo
                       ,Deposito
                       ,NumeroOperacion
                       ,Procedencia)
                 VALUES (?,?,?,?,?,?,?,GETDATE(),GETDATE(),?,?,?,?,?,?,?,?,?,?,?,1)");
            $venta->execute(array(
                $id_venta,
                $idCliente,
                $body["IdEmpleado"],
                $body["IdComprobante"],
                $body["IdMoneda"],
                $id_comprabante[0],
                $id_comprabante[1],
                $body["FechaVencimiento"],
                $body["HoraVencimiento"],
                $body["Tipo"],
                $body["Estado"],
                "",
                $body["Efectivo"],
                $body["Vuelto"],
                $body["Tarjeta"],
                $dig5 + $id_comprabante[1],
                $body["Deposito"],
                $body["NumeroOperacion"]
            ));

            $cmdComprobante = Database::getInstance()->getDb()->prepare("INSERT INTO ComprobanteTB(IdTipoDocumento,Serie,Numeracion,FechaRegistro)VALUES(?,?,?,GETDATE())");
            $cmdComprobante->execute(array(
                $body["IdComprobante"],
                $id_comprabante[0],
                $id_comprabante[1],
            ));

            $cmdDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO DetalleVentaTB
            (IdVenta
            ,IdArticulo
            ,Cantidad
            ,CostoVenta
            ,PrecioVenta
            ,Descuento
            ,IdOperacion
            ,IdImpuesto
            ,NombreImpuesto
            ,ValorImpuesto          
            ,Bonificacion
            ,PorLlevar
            ,Estado)
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            foreach ($body["Lista"] as $value) {
                // $cantidad = $value["valorInventario"] == 2 ? $value["importeNeto"] / $value["precioVentaGeneralAuxiliar"] : $value["cantidad"];
                // $precio = $value["valorInventario"] == 2 ? $value["precioVentaGeneralAuxiliar"] : $value["precioVentaGeneral"];
                $cantidad = $value["cantidad"];

                $precio = $value["precioVentaGeneral"];

                $cmdDetalle->execute(array(
                    $id_venta,
                    $value["idSuministro"],
                    $cantidad,
                    $value["costoCompra"],
                    $precio,
                    $value["descuento"],
                    $value["impuestoOperacion"],
                    $value["idImpuesto"],
                    $value["impuestoNombre"],
                    $value["impuestoValor"],
                    $value["bonificacion"],
                    0,
                    "L"
                ));
            }

            $cmdIngreso = Database::getInstance()->getDb()->prepare("INSERT INTO IngresoTB(
                IdProcedencia,
                IdUsuario,
                Detalle,
                Procedencia,
                Fecha,
                Hora,
                Forma,
                Monto)
                VALUES(?,?,?,?,GETDATE(),GETDATE(),?,?)");

            if ($body["Deposito"] > 0) {
                $cmdIngreso->execute(array(
                    $id_venta,
                    $body["IdEmpleado"],
                    "VENTA CON DEPOSITO DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                    1,
                    3,
                    $body["Deposito"]
                ));
            } else {
                if ($body["Efectivo"] > 0) {
                    $cmdIngreso->execute(array(
                        $id_venta,
                        $body["IdEmpleado"],
                        "VENTA CON EFECTIVO DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                        1,
                        1,
                        $body["Tarjeta"] > 0 ? $body["Efectivo"] : $body["Total"]
                    ));
                }

                if ($body["Tarjeta"] > 0) {
                    $cmdIngreso->execute(array(
                        $id_venta,
                        $body["IdEmpleado"],
                        "VENTA CON TAJETA DE SERIE Y NUMERACIÓN DEL COMPROBANTE " . $id_comprabante[0] . "-" . $id_comprabante[1],
                        1,
                        2,
                        $body["Tarjeta"]
                    ));
                }
            }

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se registro correctamente la venta.";
        } catch (PDOException $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return array("estado" => 0, "message" => $ex->getMessage());
        }
    }

    public static function AnularVenta($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $cmdValidar = Database::getInstance()->getDb()->prepare("SELECT * FROM VentaTB WHERE IdVenta = ? and Estado = 3");
            $cmdValidar->bindParam(1, $body["idVenta"], PDO::PARAM_STR);
            $cmdValidar->execute();
            if ($cmdValidar->fetch()) {
                Database::getInstance()->getDb()->rollback();
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . 400 . ' ' . "Bad Request");

                return "La venta ya se encuentra anulada.";
            } else {

                $cmdValidar = Database::getInstance()->getDb()->prepare("SELECT * FROM VentaCreditoTB WHERE IdVenta = ?");
                $cmdValidar->bindParam(1, $body["idVenta"], PDO::PARAM_STR);
                $cmdValidar->execute();
                if ($cmdValidar->fetch()) {
                    Database::getInstance()->getDb()->rollback();
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                    header($protocol . ' ' . 400 . ' ' . "Bad Request");

                    return "No se puede anular la venta porque tiene asociados abonos.";
                } else {

                    $cmdValidar = Database::getInstance()->getDb()->prepare("SELECT * FROM VentaTB WHERE IdVenta = ? AND FechaVenta = CAST(GETDATE() AS DATE)");
                    $cmdValidar->bindParam(1, $body["idVenta"], PDO::PARAM_STR);
                    $cmdValidar->execute();
                    if ($row = $cmdValidar->fetch()) {

                        $cmdVenta = Database::getInstance()->getDb()->prepare("UPDATE VentaTB SET Estado = ?, Observaciones = ? WHERE IdVenta = ?");
                        $cmdVenta->execute(array(
                            3,
                            $body["empleado"] . " ANULÓ LA VENTA POR EL MOTIVO: " . $body["motivo"],
                            $body["idVenta"]
                        ));

                        $cmdSuministro = Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = Cantidad + ? WHERE IdSuministro = ?");

                        $cmdKardex = Database::getInstance()->getDb()->prepare("INSERT INTO  
                        KardexSuministroTB(
                        IdSuministro,
                        Fecha,
                        Hora,
                        Tipo,
                        Movimiento,
                        Detalle,
                        Cantidad, 
                        Costo, 
                        Total,
                        IdAlmacen) 
                        VALUES(?,GETDATE(),GETDATE(),?,?,?,?,?,?,?)");

                        if ($row["Estado"] != 4) {
                            $cmdDetalleVenta = Database::getInstance()->getDb()->prepare("SELECT 
                            dv.IdArticulo,
                            dv.CostoVenta,
                            dv.Cantidad,
                            dv.Bonificacion,
                            s.Inventario,
                            s.ValorInventario
                            FROM DetalleVentaTB AS dv
                            INNER JOIN SuministroTB AS s ON dv.IdArticulo = s.IdSuministro
                            WHERE dv.IdVenta = ?");
                            $cmdDetalleVenta->bindParam(1, $body["idVenta"], PDO::PARAM_STR);
                            $cmdDetalleVenta->execute();

                            while ($sumi = $cmdDetalleVenta->fetch()) {
                                if ($sumi["Inventario"] == 1 && $sumi["ValorInventario"] == 1) {
                                    $cmdSuministro->execute(array(
                                        $sumi["Cantidad"] + $sumi["Bonificacion"],
                                        $sumi["IdArticulo"]
                                    ));
                                } else if ($sumi["Inventario"] == 1 && $sumi["ValorInventario"] == 2) {
                                    $cmdSuministro->execute(array(
                                        $sumi["Cantidad"],
                                        $sumi["IdArticulo"]
                                    ));
                                } else if ($sumi["Inventario"] == 1 && $sumi["ValorInventario"] == 3) {
                                    $cmdSuministro->execute(array(
                                        $sumi["Cantidad"],
                                        $sumi["IdArticulo"]
                                    ));
                                }

                                $cantidadTotal =  $sumi["ValorInventario"] == 1
                                    ? $sumi["Cantidad"] + $sumi["Bonificacion"]
                                    : $sumi["Cantidad"];

                                $cmdKardex->execute(array(
                                    $sumi["IdArticulo"],
                                    1,
                                    2,
                                    "DEVOLUCIÓN DE PRODUCTO",
                                    $cantidadTotal,
                                    $sumi["CostoVenta"],
                                    $cantidadTotal * $sumi["CostoVenta"],
                                    0
                                ));
                            }
                        }

                        $cmdIngreso = Database::getInstance()->getDb()->prepare("DELETE FROM IngresoTB WHERE IdProcedencia = ?");
                        $cmdIngreso->execute(array(
                            $body["idVenta"]
                        ));

                        Database::getInstance()->getDb()->commit();
                        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                        header($protocol . ' ' . 201 . ' ' . "Created");

                        return "Se anuló correctamente la venta.";
                    } else {
                        Database::getInstance()->getDb()->rollback();
                        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                        header($protocol . ' ' . 400 . ' ' . "Bad Request");

                        return "No se puede anular la venta porque la fecha es distinta a la fecha de emisión.";
                    }
                }
            }
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function Sp_Reporte_General_Ventas($procedencia, $fechaInicial, $fechaFinal, $tipoComprobante, $cliente, $vendedor, $tipo, $metodo, $valormetodo)
    {
        try {

            $cmdVenta = Database::getInstance()->getDb()->prepare("{CALL Sp_Reporte_General_Ventas(?,?,?,?,?,?,?,?,?)}");
            $cmdVenta->bindParam(1, $procedencia, PDO::PARAM_INT);
            $cmdVenta->bindParam(2, $fechaInicial, PDO::PARAM_STR);
            $cmdVenta->bindParam(3, $fechaFinal, PDO::PARAM_STR);
            $cmdVenta->bindParam(4, $tipoComprobante, PDO::PARAM_INT);
            $cmdVenta->bindParam(5, $cliente, PDO::PARAM_STR);
            $cmdVenta->bindParam(6, $vendedor, PDO::PARAM_STR);
            $cmdVenta->bindParam(7, $tipo, PDO::PARAM_INT);
            $cmdVenta->bindParam(8, $metodo, PDO::PARAM_BOOL);
            $cmdVenta->bindParam(9, $valormetodo, PDO::PARAM_INT);
            $cmdVenta->execute();

            $count = 0;
            $arrayVenta = array();
            while ($row = $cmdVenta->fetch()) {
                $count++;
                array_push($arrayVenta, array(
                    "Id" => $count,
                    "FechaVenta" => $row["FechaVenta"],
                    "NumeroDocumento" => $row["NumeroDocumento"],
                    "Cliente" => $row["Cliente"],
                    "Nombre" => $row["Nombre"],
                    "Serie" => $row["Serie"],
                    "Numeracion" => $row["Numeracion"],
                    "Tipo" => intval($row["Tipo"]),
                    "TipoName" => $row["TipoName"],
                    "Estado" => intval($row["Estado"]),
                    "EstadoName" => $row["EstadoName"],
                    "FormaName" => $row["FormaName"],
                    "Simbolo" => $row["Simbolo"],
                    "Efectivo" => floatval($row["Efectivo"]),
                    "Tarjeta" => floatval($row["Tarjeta"]),
                    "Deposito" => floatval($row["Deposito"]),
                    "Total" => floatval($row["Total"]),
                    "IdNotaCredito" => $row["IdNotaCredito"],
                ));
            }

            return $arrayVenta;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
