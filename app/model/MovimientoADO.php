<?php

namespace SysSoftIntegra\Model;

use Database;
use PDO;
use PDOException;
use Exception;
use DateTime;

require_once __DIR__ . './../database/DataBaseConexion.php';

class MovimientoADO
{

    function __construct()
    {
    }

    public static function ListarTipoMovimiento($ajuste, $all)
    {
        try {
            $consulta = "SELECT IdTipoMovimiento,Nombre,Predeterminado,Sistema,Ajuste FROM TipoMovimientoTB";
            $query = $all === "true" ? $consulta : $consulta . " WHERE Ajuste = ?";
            $comando = Database::getInstance()->getDb()->prepare($query);
            if ($all === "false") {
                $comando->bindParam(1, $ajuste, PDO::PARAM_BOOL);
            }
            $comando->execute();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return $comando->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ListarMoviminentos(string $opcion, int $movimiento, string $fechaInicial, string $fechaFinal, int $posicionPagina, int $filasPorPagina)
    {
        try {
            $arrayMovimiento = array();
            $cmdMovimiento = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Movimiento_Inventario(?,?,?,?,?,?)}");
            $cmdMovimiento->bindValue(1, $opcion, PDO::PARAM_INT);
            $cmdMovimiento->bindValue(2, $movimiento, PDO::PARAM_INT);
            $cmdMovimiento->bindValue(3, $fechaInicial, PDO::PARAM_STR);
            $cmdMovimiento->bindValue(4, $fechaFinal, PDO::PARAM_STR);
            $cmdMovimiento->bindValue(5, $posicionPagina, PDO::PARAM_INT);
            $cmdMovimiento->bindValue(6, $filasPorPagina, PDO::PARAM_INT);
            $cmdMovimiento->execute();
            $count = 0;
            while ($row = $cmdMovimiento->fetch()) {
                $count++;
                array_push($arrayMovimiento, array(
                    "count" => $count + $posicionPagina,
                    "IdMovimientoInventario" => $row["IdMovimientoInventario"],
                    "Fecha" => $row["Fecha"],
                    "Hora" => $row["Hora"],
                    "TipoAjuste" => $row["TipoAjuste"],
                    "TipoMovimiento" => $row["TipoMovimiento"],
                    "Observacion" => $row["Observacion"],
                    "Informacion" => $row["Informacion"],
                    "Estado" => $row["Estado"]
                ));
            }

            $cmdMovimientoCount = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Movimiento_Inventario_Count(?,?,?,?)}");
            $cmdMovimientoCount->bindValue(1, $opcion, PDO::PARAM_INT);
            $cmdMovimientoCount->bindValue(2, $movimiento, PDO::PARAM_INT);
            $cmdMovimientoCount->bindValue(3, $fechaInicial, PDO::PARAM_STR);
            $cmdMovimientoCount->bindValue(4, $fechaFinal, PDO::PARAM_STR);
            $cmdMovimientoCount->execute();
            $resultTotal = $cmdMovimientoCount->fetchColumn();

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "data" => $arrayMovimiento,
                "total" => $resultTotal
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function RegistrarMovimientoInventario($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();

            $queryCodigoMovimiento = "SELECT dbo.Fc_MovimientoInventario_Codigo_Alfanumerico();";
            $codigoMovimiento = Database::getInstance()->getDb()->prepare($queryCodigoMovimiento);
            $codigoMovimiento->execute();
            $idMovimiento = $codigoMovimiento->fetchColumn();

            $movimiento = Database::getInstance()->getDb()->prepare("INSERT INTO MovimientoInventarioTB
            (IdMovimientoInventario,
            Fecha,
            Hora,
            TipoAjuste,
            TipoMovimiento,
            Observacion,
            Suministro,
            Estado,
            CodigoVerificacion)
            VALUES(?,?,?,?,?,?,?,?,?)");
            $movimiento->execute(array(
                $idMovimiento,
                $body["fecha"],
                $body["hora"],
                $body["tipoAjuste"],
                $body["tipoMovimiento"],
                $body["observacion"],
                $body["suministro"],
                $body["estado"],
                $body["codigoVerificacion"]
            ));

            $movimientoDetalle = Database::getInstance()->getDb()->prepare("INSERT INTO MovimientoInventarioDetalleTB
            (IdMovimientoInventario,
            IdSuministro,
            Cantidad,
            Costo,
            Precio)
            VALUES(?,?,?,?,?)");

            $suministroUpdate = $body["tipoAjuste"]
                ? Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = Cantidad + ? WHERE IdSuministro = ?")
                : Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = Cantidad - ? WHERE IdSuministro = ?");

            $suministroUpdateAlmacen = $body["tipoAjuste"]
                ? Database::getInstance()->getDb()->prepare("UPDATE CantidadTB SET Cantidad = Cantidad + ? WHERE IdAlmacen = ? AND IdSuministro = ?")
                : Database::getInstance()->getDb()->prepare("UPDATE CantidadTB SET Cantidad = Cantidad - ? WHERE IdAlmacen = ? AND IdSuministro = ?");

            $suministroKardex = Database::getInstance()->getDb()->prepare("INSERT INTO KardexSuministroTB
            (IdSuministro,
            Fecha,
            Hora,
            Tipo,
            Movimiento,
            Detalle,
            Cantidad,
            Costo,
            Total,
            IdAlmacen)
            VALUES(?,?,?,?,?,?,?,?,?,?)");

            foreach ($body["lista"] as $result) {
                $movimientoDetalle->execute(array(
                    $idMovimiento,
                    $result["IdSuministro"],
                    $result["Movimiento"],
                    $result["PrecioCompra"],
                    $result["PrecioVentaGeneral"],
                ));

                if ($body["idAlmacen"] == 0) {
                    $suministroUpdate->execute(array(
                        $result["Movimiento"],
                        $result["IdSuministro"],
                    ));

                    $suministroKardex->execute(array(
                        $result["IdSuministro"],
                        $body["fecha"],
                        $body["hora"],
                        $body["tipoAjuste"] ? 1 : 2,
                        $body["tipoMovimiento"],
                        $body["observacion"],
                        $result["Movimiento"],
                        $result["PrecioCompra"],
                        $result["PrecioCompra"] * $result["Movimiento"],
                        $body["idAlmacen"]
                    ));
                } else {
                    $suministroUpdateAlmacen->execute(array(
                        $result["Movimiento"],
                        $body["idAlmacen"],
                        $result["IdSuministro"]
                    ));

                    $suministroKardex->execute(array(
                        $result["IdSuministro"],
                        $body["fecha"],
                        $body["hora"],
                        $body["tipoAjuste"] ? 1 : 2,
                        $body["tipoMovimiento"],
                        $body["observacion"],
                        $result["Movimiento"],
                        $result["PrecioCompra"],
                        $result["PrecioCompra"] * $result["Movimiento"],
                        $body["idAlmacen"]
                    ));
                }
            }

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Registrado correctamente el movimiento.";
        } catch (Exception $e) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $e->getMessage();
        }
    }

    public static function RestablecerInventario($body)
    {
        try {
            Database::getInstance()->getDb()->beginTransaction();
            $suministroUpdate = Database::getInstance()->getDb()->prepare("UPDATE SuministroTB SET Cantidad = ? WHERE IdSuministro = ?");

            $suministroKardex = Database::getInstance()->getDb()->prepare("INSERT INTO KardexSuministroTB
            (IdSuministro,
            Fecha,
            Hora,
            Tipo,
            Movimiento,
            Detalle,
            Cantidad,
            Costo,
            Total,
            IdAlmacen)
            VALUES(?,?,?,?,?,?,?,?,?,?)");

            foreach ($body["lista"] as $result) {

                $cmdKardex = Database::getInstance()->getDb()->prepare("{CALL Sp_Listar_Kardex_Suministro_By_Id(0,?,0)}");
                $cmdKardex->bindParam(1, $result["IdSuministro"], PDO::PARAM_STR);
                $cmdKardex->execute();

                $cantidad = 0;
                $count = 0;
                while ($row = $cmdKardex->fetch()) {
                    $count++;
                    $cantidad = $cantidad + ($row["Tipo"] == 1 ? $row["Cantidad"] : -$row["Cantidad"]);
                }

                $valor = $cantidad * -1;
                $suministroKardex->execute(array(
                    $result["IdSuministro"],
                    $body["fecha"],
                    $body["hora"],
                    $body["tipoAjuste"],
                    $body["tipoMovimiento"],
                    $body["observacion"],
                    $valor,
                    $result["PrecioCompra"],
                    $result["PrecioCompra"] * $valor,
                    0
                ));

                $suministroUpdate->bindParam(1, $result["Movimiento"], PDO::PARAM_STR);
                $suministroUpdate->bindParam(2, $result["IdSuministro"], PDO::PARAM_STR);
                $suministroUpdate->execute();

                $suministroKardex->execute(array(
                    $result["IdSuministro"],
                    $body["fecha"],
                    $body["hora"],
                    $body["tipoAjuste"],
                    $body["tipoMovimiento"],
                    $body["observacion"],
                    $result["Movimiento"],
                    $result["PrecioCompra"],
                    $result["PrecioCompra"] * $result["Movimiento"],
                    0
                ));
            }

            Database::getInstance()->getDb()->commit();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 201 . ' ' . "Created");

            return "Se completo el proceso correctamente.";
        } catch (Exception $ex) {
            Database::getInstance()->getDb()->rollback();
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }

    public static function ObtenerMovimientoInventarioById($idMovimiento)
    {
        try {
            $array = array();

            $comandoMovimiento = Database::getInstance()->getDb()->prepare("{call Sp_Get_Movimiento_Inventario_By_Id(?)}");
            $comandoMovimiento->bindParam(1, $idMovimiento, PDO::PARAM_STR);
            $comandoMovimiento->execute();
            $resultMovimiento = $comandoMovimiento->fetchObject();

            $comandoMovimientoDetalle = Database::getInstance()->getDb()->prepare("{call Sp_Listar_Movimiento_Inventario_Detalle_By_Id(?)}");
            $comandoMovimientoDetalle->bindParam(1, $idMovimiento, PDO::PARAM_STR);
            $comandoMovimientoDetalle->execute();
            $resultMovimientoDetalle = array();
            $count = 0;
            while ($row = $comandoMovimientoDetalle->fetch()) {
                $count++;
                array_push($resultMovimientoDetalle, array(
                    "Id" => $count,
                    "IdMovimientoInventario" => $row["IdMovimientoInventario"],
                    "IdSuministro" => $row["IdSuministro"],
                    "Clave" => $row["Clave"],
                    "NombreMarca" => $row["NombreMarca"],
                    "Cantidad" => $row["Cantidad"],
                    "Costo" => $row["Costo"],
                    "Precio" => $row["Precio"]
                ));
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 200 . ' ' . "OK");

            return array(
                "movimiento" =>  $resultMovimiento,
                "detalle" => $resultMovimientoDetalle
            );
        } catch (Exception $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 500 . ' ' . "Internal Server Error");

            return $ex->getMessage();
        }
    }
}
