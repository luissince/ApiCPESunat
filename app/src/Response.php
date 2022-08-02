<?php
namespace SysSoftIntegra\Src;

class Response
{

    public static function sendSuccess($result)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 200 . ' ' . "OK");
        echo json_encode($result);
        exit;
    }

    public static function sendSave($result)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 201 . ' ' . "Created");
        echo json_encode($result);
        exit;
    }

    public static function sendClient($result)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 400 . ' ' . "Bad Request");
        echo json_encode($result);
        exit;
    }

    public static function sendError($result)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 500 . ' ' . "Internal Server Error");
        echo json_encode($result);
        exit;
    }
}
