<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use Exception;
use DateTime;


require_once __DIR__ . './../database/DataBaseConexion.php';


class DashboardADO
{

    function construct()
    {
    }

    public static function LoadDashboard($fecha)
    {
        try {
            $cmdVentas = Database::getInstance()->getDb()->prepare("SELECT
            ISNULL(SUM(dv.Cantidad * (dv.PrecioVenta-dv.Descuento)),0) AS Total
            FROM VentaTB AS v   
            LEFT JOIN NotaCreditoTB AS n ON v.IdVenta = n.IdVenta
	        INNER JOIN DetalleVentaTB AS dv ON dv.IdVenta = v.IdVenta
            WHERE v.Estado <> 3  AND n.IdNotaCredito IS NULL AND v.FechaVenta = CAST(GETDATE() AS DATE)");
            $cmdVentas->execute();
            $resultVentas = 0;
            if ($row = $cmdVentas->fetch()) {
                $resultVentas = floatval($row['Total']);
            }

            $cmdCompras = Database::getInstance()->getDb()->prepare("SELECT
			ISNULL(sum(d.Cantidad*(d.PrecioCompra-d.Descuento)),0) AS Total 
            FROM CompraTB AS c 
            INNER JOIN DetalleCompraTB AS d ON d.IdCompra = c.IdCompra
            WHERE c.FechaCompra =  CAST(GETDATE() AS DATE)");
            $cmdCompras->execute();
            $resultCompras = 0;
            if ($row = $cmdCompras->fetch()) {
                $resultCompras = floatval($row['Total']);
            }

            $cmdCuentasCobrar = Database::getInstance()->getDb()->prepare("SELECT COUNT(*) AS Total
            FROM VentaTB WHERE Tipo = 2 AND Estado = 2");
            $cmdCuentasCobrar->execute();
            $resultCCobrar = 0;
            if ($row = $cmdCuentasCobrar->fetch()) {
                $resultCCobrar = floatval($row['Total']);
            }

            $cmdCuentasPagar = Database::getInstance()->getDb()->prepare("SELECT ISNULL(COUNT(*),0) AS Total
            FROM CompraTB WHERE TipoCompra = 2 AND EstadoCompra = 2");
            $cmdCuentasPagar->execute();
            $resultCPagar = 0;
            if ($row = $cmdCuentasPagar->fetch()) {
                $resultCPagar = floatval($row['Total']);
            }

            $cmdProductosTotal = Database::getInstance()->getDb()->prepare("SELECT ISNULL(COUNT(*),0) AS Total FROM SuministroTB");
            $cmdProductosTotal->execute();
            $resultProductos = 0;
            if ($row = $cmdProductosTotal->fetch()) {
                $resultProductos = floatval($row['Total']);
            }

            $cmdClientesTotal = Database::getInstance()->getDb()->prepare("SELECT ISNULL(COUNT(*),0) AS Total FROM ClienteTB");
            $cmdClientesTotal->execute();
            $resultClientes = 0;
            if ($row = $cmdClientesTotal->fetch()) {
                $resultClientes = floatval($row['Total']);
            }

            $cmdProveedoresTotal = Database::getInstance()->getDb()->prepare("SELECT ISNULL(COUNT(*),0) AS Total FROM ProveedorTB");
            $cmdProveedoresTotal->execute();
            $resultProveedores = 0;
            if ($row = $cmdProductosTotal->fetch()) {
                $resultProveedores = floatval($row['Total']);
            }

            $cmdTrabajadoresTotal = Database::getInstance()->getDb()->prepare("SELECT ISNULL(COUNT(*),0) AS Total FROM EmpleadoTB");
            $cmdTrabajadoresTotal->execute();
            $resultEmpleados = 0;
            if ($row = $cmdTrabajadoresTotal->fetch()) {
                $resultEmpleados = floatval($row['Total']);
            }

            $cmdHistorialVentas = Database::getInstance()->getDb()->prepare("SELECT 
            MONTH(vt.FechaVenta) AS Mes, 
            SUM(dv.Cantidad * (dv.PrecioVenta-dv.Descuento)) AS Monto
            FROM VentaTB AS vt 
            LEFT JOIN NotaCreditoTB AS nc ON nc.IdVenta = vt.IdVenta
			INNER JOIN DetalleVentaTB AS dv ON dv.IdVenta = vt.IdVenta
            WHERE 
            vt.Estado <> 3 AND nc.IdNotaCredito IS NULL AND YEAR(vt.FechaVenta) = YEAR(GETDATE())
            GROUP BY MONTH(vt.FechaVenta)");
            $cmdHistorialVentas->execute();
            $historialVentas = $cmdHistorialVentas->fetchAll(PDO::FETCH_CLASS);

            $cmdPNegativos = Database::getInstance()->getDb()->prepare("SELECT COUNT(*) AS Total FROM SuministroTB WHERE Cantidad <= 0");
            $cmdPNegativos->execute();
            $resultPNegativos = 0;
            if ($row = $cmdPNegativos->fetch()) {
                $resultPNegativos = floatval($row['Total']);
            }

            $cmdPIntermedios = Database::getInstance()->getDb()->prepare("SELECT COUNT(*) AS Total FROM SuministroTB WHERE Cantidad > 0 AND Cantidad < StockMinimo");
            $cmdPIntermedios->execute();
            $resultPIntermedios = 0;
            if ($row = $cmdPIntermedios->fetch()) {
                $resultPIntermedios = floatval($row['Total']);
            }

            $cmdPNecesarios = Database::getInstance()->getDb()->prepare("SELECT COUNT(*) AS Total FROM SuministroTB WHERE Cantidad >= StockMinimo AND Cantidad < StockMaximo");
            $cmdPNecesarios->execute();
            $resultPNecesarios = 0;
            if ($row = $cmdPNecesarios->fetch()) {
                $resultPNecesarios = floatval($row['Total']);
            }

            $cmdPExcecentes = Database::getInstance()->getDb()->prepare("SELECT COUNT(*) AS Total FROM SuministroTB WHERE Cantidad >= StockMaximo");
            $cmdPExcecentes->execute();
            $resultPExcecentes = 0;
            if ($row = $cmdPExcecentes->fetch()) {
                $resultPExcecentes = floatval($row['Total']);
            }

            $cmdHistorialVentasTipos = Database::getInstance()->getDb()->prepare("SELECT 
            MONTH(vt.FechaVenta) AS Mes, 
			case td.Facturacion when 1 then SUM(dv.Cantidad * (dv.PrecioVenta-dv.Descuento)) else 0 end Sunat,
            case td.Facturacion when 0 then SUM(dv.Cantidad * (dv.PrecioVenta-dv.Descuento)) else 0 end Libre
            FROM VentaTB AS vt 
			INNER JOIN TipoDocumentoTB AS td on td.IdTipoDocumento = vt.Comprobante
            LEFT JOIN NotaCreditoTB AS nc ON nc.IdVenta = vt.IdVenta
			INNER JOIN DetalleVentaTB AS dv ON dv.IdVenta = vt.IdVenta
            WHERE 
            vt.Estado <> 3 AND nc.IdNotaCredito IS NULL AND YEAR(vt.FechaVenta) = YEAR(GETDATE()) 
			or
			vt.Estado <> 3 AND nc.IdNotaCredito IS NULL AND YEAR(vt.FechaVenta) = YEAR(GETDATE()) AND td.Facturacion = 1
            GROUP BY 
			MONTH(vt.FechaVenta),
			td.Facturacion");
            $cmdHistorialVentasTipos->execute();
            $resultHistorialVentasTipos = $cmdHistorialVentasTipos->fetchAll(PDO::FETCH_OBJ);


            $cmdTipoVenta = Database::getInstance()->getDb()->prepare("SELECT 
            CASE 
            WHEN v.Estado <> 2 AND v.Efectivo > 0 AND v.Tarjeta = 0 THEN 'EFECTIVO' 
            WHEN v.Estado <> 2 AND v.Tarjeta  > 0 AND v.Efectivo = 0 THEN 'TARJETA'
            WHEN v.Estado <> 2 AND v.Efectivo > 0 AND v.Tarjeta > 0 THEN 'MIXTO'
            WHEN v.Estado <> 2 AND v.Deposito > 0 THEN 'DEPOSITO' 
            ELSE 'NINGUNO' END AS FormaName,
            v.Tipo,
            v.Estado,
            v.Efectivo,
            v.Tarjeta,
            v.Deposito,
            sum(dv.Cantidad * (dv.PrecioVenta-dv.Descuento)) AS Total,
            iif(n.IdNotaCredito IS NULL,0,1) AS IdNotaCredito
            FROM VentaTB AS v
            LEFT JOIN NotaCreditoTB AS n ON v.IdVenta = n.IdVenta
            INNER JOIN DetalleVentaTB AS dv ON dv.IdVenta = v.IdVenta
            WHERE MONTH(v.FechaVenta) = MONTH(GETDATE()) AND YEAR(v.FechaVenta) = YEAR(GETDATE())
            GROUP BY             
            v.Tipo,
            v.Estado,
            v.Efectivo,
            v.Tarjeta,
            v.Deposito,
            v.Estado,
            v.FechaVenta,
            v.HoraVenta,
            n.IdNotaCredito");
            $cmdTipoVenta->execute();
            $resultTipoVenta = $cmdTipoVenta->fetchAll(PDO::FETCH_OBJ);

            $cmdProductosMasVecesVendidos = Database::getInstance()->getDb()->prepare("SELECT 
            TOP 10
            s.IdSuministro,
            s.Clave,
            s.NombreMarca,
            isnull(dc.Nombre,'') AS Categoria,
            isnull(dm.Nombre,'') AS Marca,
            isnull(du.Nombre,'') AS Medida,
            count(s.IdSuministro) as Cantidad
            FROM DetalleVentaTB dv 
            INNER JOIN VentaTB AS v ON v.IdVenta = dv.IdVenta
            INNER JOIN SuministroTB AS s ON s.IdSuministro = dv.IdArticulo
            LEFT JOIN DetalleTB AS dc ON dc.IdDetalle = s.Categoria AND dc.IdMantenimiento = '0006'
            LEFT JOIN DetalleTB AS dm ON dm.IdDetalle = s.Marca AND dm.IdMantenimiento = '0007'
            LEFT JOIN DetalleTB AS du ON du.IdDetalle = s.UnidadCompra AND du.IdMantenimiento = '0013'
            WHERE MONTH(FechaVenta) = MONTH(GETDATE()) AND YEAR(FechaVenta) = YEAR(GETDATE())
            GROUP BY
            s.IdSuministro,
            s.Clave,
            s.NombreMarca,
            dc.Nombre,
            dm.Nombre,
            du.Nombre
            ORDER BY Cantidad DESC");
            $cmdProductosMasVecesVendidos->execute();
            $resulProductosMasVecesVendidos = $cmdProductosMasVecesVendidos->fetchAll(PDO::FETCH_OBJ);

            $cmdProductosMasCantidadesVendidos = Database::getInstance()->getDb()->prepare("SELECT 
            TOP 10
            s.IdSuministro,
            s.Clave,
            s.NombreMarca,
            isnull(dc.Nombre,'') AS Categoria,
            isnull(dm.Nombre,'') AS Marca,
            isnull(du.Nombre,'') AS Medida,
            sum(dv.Cantidad) AS Suma
            FROM DetalleVentaTB dv 
            INNER JOIN VentaTB AS v ON v.IdVenta = dv.IdVenta
            INNER JOIN SuministroTB as s ON s.IdSuministro = dv.IdArticulo
            LEFT JOIN DetalleTB AS dc ON dc.IdDetalle = s.Categoria AND dc.IdMantenimiento = '0006'
            LEFT JOIN DetalleTB AS dm ON dm.IdDetalle = s.Marca AND dm.IdMantenimiento = '0007'
            LEFT JOIN DetalleTB AS du ON du.IdDetalle = s.UnidadCompra AND du.IdMantenimiento = '0013'
            WHERE MONTH(FechaVenta) = MONTH(GETDATE()) AND YEAR(FechaVenta) = YEAR(GETDATE())
            GROUP BY
            s.IdSuministro,
            s.Clave,
            s.NombreMarca,
            dc.Nombre,
            dm.Nombre,
            du.Nombre
            ORDER BY Suma DESC");
            $cmdProductosMasCantidadesVendidos->execute();
            $resulProductosMasCantidadVendidos = $cmdProductosMasCantidadesVendidos->fetchAll(PDO::FETCH_OBJ);

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "ventas" =>  $resultVentas,
                "compras" => $resultCompras,
                "ccobrar" => $resultCCobrar,
                "ccpagar" =>  $resultCPagar,
                "productos" => $resultProductos,
                "clientes" => $resultClientes,
                "proveedores" => $resultProveedores,
                "empleados" => $resultEmpleados,
                "hventas" => $historialVentas,
                "innegativo" =>   $resultPNegativos,
                "inintermedio" =>  $resultPIntermedios,
                "innecesario" => $resultPNecesarios,
                "inexcecende" => $resultPExcecentes,
                "historialventastipos" => $resultHistorialVentasTipos,
                "tipoventa" => $resultTipoVenta,
                "vecesVendidos" => $resulProductosMasVecesVendidos,
                "cantidadVendidos" => $resulProductosMasCantidadVendidos
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function LoadProductosAgotados($posicionPaginaAgotados, $filasPorPaginaAgotados)
    {
        try {

            $comandoProductosAgotados = Database::getInstance()->getDb()->prepare("SELECT 
            NombreMarca, 
            PrecioVentaGeneral, 
            Cantidad 
            FROM SuministroTB 
            WHERE Cantidad <= 0 
            ORDER BY Cantidad ASC offset ? ROWS FETCH NEXT ? ROWS ONLY");
            $comandoProductosAgotados->bindParam(1, $posicionPaginaAgotados, PDO::PARAM_INT);
            $comandoProductosAgotados->bindParam(2, $filasPorPaginaAgotados, PDO::PARAM_INT);
            $comandoProductosAgotados->execute();

            $arrayProductosAgotados = array();
            while ($rows = $comandoProductosAgotados->fetch()) {
                array_push($arrayProductosAgotados, array(
                    "NombreProducto" => $rows["NombreMarca"],
                    "Pecio" => $rows["PrecioVentaGeneral"],
                    "Cantidad" => $rows["Cantidad"],
                ));
            }
            $comandoProductosAgotadosCount = Database::getInstance()->getDb()->prepare("SELECT COUNT(*)
            FROM SuministroTB WHERE Cantidad <= 0 ");
            $comandoProductosAgotadosCount->execute();
            $resultTotal = $comandoProductosAgotadosCount->fetchColumn();

            return array(
                "estado" => 1,
                "productosAgotadosLista" => $arrayProductosAgotados,
                "productosAgotadosTotal" => $resultTotal
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 0,
                "message" => $ex->getMessage(),
            );
        }
    }

    public static function LoadProductosPorAgotarse($posicionPaginaPorAgotarse, $filasPorPaginaPorAgotarse)
    {
        try {
            $array = array();
            $comandoProductosPorAgotarse = Database::getInstance()->getDb()->prepare("SELECT NombreMarca, PrecioVentaGeneral, Cantidad FROM SuministroTB
            WHERE Cantidad > 0 AND Cantidad < StockMinimo
            ORDER BY cantidad ASC offset ? ROWS FETCH NEXT ? ROWS ONLY");
            $comandoProductosPorAgotarse->bindParam(1, $posicionPaginaPorAgotarse, PDO::PARAM_INT);
            $comandoProductosPorAgotarse->bindParam(2, $filasPorPaginaPorAgotarse, PDO::PARAM_INT);
            $comandoProductosPorAgotarse->execute();

            $arrayProductosPorAgotarse = array();
            while ($rows = $comandoProductosPorAgotarse->fetch()) {
                array_push($arrayProductosPorAgotarse, array(
                    "NombreProducto" => $rows["NombreMarca"],
                    "Pecio" => $rows["PrecioVentaGeneral"],
                    "Cantidad" => $rows["Cantidad"],
                ));
            }

            $comandoProductosAgotadosCount = Database::getInstance()->getDb()->prepare("SELECT COUNT(*)
            FROM SuministroTB WHERE Cantidad > 0 AND Cantidad < StockMinimo");
            $comandoProductosAgotadosCount->execute();
            $resultTotal = $comandoProductosAgotadosCount->fetchColumn();
            array_push($array, $arrayProductosPorAgotarse, $resultTotal);
            return array(
                "estado" => 1,
                "productoPorAgotarseLista" => $arrayProductosPorAgotarse,
                "productoPorAgotarseTotal" => $resultTotal
            );
        } catch (Exception $ex) {
            return array(
                "estado" => 0,
                "message" => $ex->getMessage(),
            );
        }
    }
}
