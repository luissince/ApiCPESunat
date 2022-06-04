<?php

namespace SysSoftIntegra\Src;

class Tools
{

    public static function validateDuplicateSuministro($array,$oject){
        $ret = false;
        foreach($array as $value){
            if($value["IdSuministro"] == $oject["IdSuministro"]){
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    public static function calculateTax(float $porcentaje, float $valor)
    {
        return (float) ($valor * ($porcentaje / 100.00));
    }

    public static function calculateTaxBruto(float $impuesto, float $monto)
    {
        return $monto / (($impuesto + 100) * 0.01);
    }

    public static function formatNumber($numeracion, $length = 6)
    {
        return strlen($numeracion) > $length ? $numeracion : substr(str_repeat(0, $length) . $numeracion, -$length);
    }

    public static function roundingValue(float $valor, int $decimals = 2)
    {
        return number_format(round($valor, 2, PHP_ROUND_HALF_UP), $decimals, '.', '');
    }

    public static function round($value, $precision = 2)
    {
        return round($value, $precision, PHP_ROUND_HALF_UP);
    }

    public static function formatDate($fecha)
    {
        return date("d/m/Y", strtotime($fecha));
    }

    public static function printErrorJson($result)
    {
        header('Content-Type: application/json; charset=UTF-8');
        print json_encode($result);
    }

    public static function showImageReport($image)
    {
        return $image == "" ?  '<img src="./../../view/images/logo.png" />' : '<img width="120" src="data:image/(png|jpg|gif);base64, ' . $image . '"/>';
    }

    public static function httpStatus200()
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 200 . ' ' . "OK");
    }

    public static function httpStatus201()
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 201 . ' ' . "Created");
    }

    public static function httpStatus400()
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 400 . ' ' . "Bad Request");
    }

    public static function httpStatus500()
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 500 . ' ' . "Internal Server Error");
    }
}
