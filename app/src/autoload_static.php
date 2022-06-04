<?php

class Autoload_Static
{
    public static function files()
    {
        return array(
            'SysSoftIntegra\\Model\\VentasADO' => __DIR__  . '/../model/VentasADO.php',
            'SysSoftIntegra\\Model\\DetalleADO' => __DIR__  . '/../model/DetalleADO.php',
            'SysSoftIntegra\\Model\\SuministrosADO' => __DIR__  . '/../model/SuministrosADO.php',
            'SysSoftIntegra\\Model\\AlmacenADO' => __DIR__  . '/../model/AlmacenADO.php',
            'SysSoftIntegra\\Model\\CajaADO' => __DIR__  . '/../model/CajaADO.php',
            'SysSoftIntegra\\Model\\EmpresaADO' => __DIR__  . '/../model/EmpresaADO.php',
            'SysSoftIntegra\\Model\\ComprasADO' => __DIR__  . '/../model/ComprasADO.php',
            'SysSoftIntegra\\Model\\EmpleadoADO' => __DIR__  . '/../model/EmpleadoADO.php',
            'SysSoftIntegra\\Model\\MovimientoADO' => __DIR__  . '/../model/MovimientoADO.php',
            'SysSoftIntegra\\Model\\MonedaADO' => __DIR__  . '/../model/MonedaADO.php',
            'SysSoftIntegra\\Model\\ImpuestoADO' => __DIR__  . '/../model/ImpuestoADO.php',
            'SysSoftIntegra\\Model\\TipoDocumentoADO' => __DIR__  . '/../model/TipoDocumentoADO.php',
            'SysSoftIntegra\\Model\\ComprobanteADO' => __DIR__  . '/../model/ComprobanteADO.php',
            'SysSoftIntegra\\Model\\ClienteADO' => __DIR__  . '/../model/ClienteADO.php',
            'SysSoftIntegra\\Model\\ProveedorADO' => __DIR__  . '/../model/ProveedorADO.php',
            'SysSoftIntegra\\Model\\CotizacionADO' => __DIR__  . '/../model/CotizacionADO.php',
            'SysSoftIntegra\\Model\\IngresoADO' => __DIR__  . '/../model/IngresoADO.php',
            'SysSoftIntegra\\Model\\BancoADO' => __DIR__  . '/../model/BancoADO.php',
            'SysSoftIntegra\\Model\\PedidoADO' => __DIR__  . '/../model/PedidoADO.php',
            'SysSoftIntegra\\Model\\RolADO' => __DIR__  . '/../model/RolADO.php',
            'SysSoftIntegra\\Model\\NotaCreditoADO' => __DIR__  . '/../model/NotaCreditoADO.php',
            'SysSoftIntegra\\Model\\DashboardADO' => __DIR__  . '/../model/DashboardADO.php',
            'SysSoftIntegra\\Controller\\BaseController' => __DIR__  . '/../controller/BaseController.php',
            'SysSoftIntegra\\Src\\Sunat' => __DIR__  . '/Sunat.php',
            'SysSoftIntegra\\Src\\Tools' => __DIR__  . '/Tools.php',
            'SysSoftIntegra\\Src\\SoapResult' => __DIR__  . '/SoapResult.php',
            'SysSoftIntegra\\Src\\SoapBuilder' => __DIR__  . '/SoapBuilder.php',
            'SysSoftIntegra\\Src\\NumberLleters' => __DIR__  . '/NumberLleters.php',
            'SysSoftIntegra\\Src\\GenerateXml' => __DIR__  . '/GenerateXml.php',
            'SysSoftIntegra\\Src\\GenerateXml' => __DIR__  . '/GenerateXml.php',
            'Phpspreadsheet\\Vendor\\Autload' => __DIR__ . '/../sunat/lib/phpspreadsheet/vendor/autoload.php',
            'RobRichards\\XMLSecLibs\\XMLSecurityDSig' => __DIR__ . '/../sunat/lib/robrichards/src/XMLSecurityDSig.php',
            'RobRichards\\XMLSecLibs\\XMLSecurityKey' => __DIR__ . '/../sunat/lib/robrichards/src/XMLSecurityKey.php'
        );
    }
}
