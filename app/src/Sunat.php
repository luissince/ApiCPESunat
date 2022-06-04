<?php

namespace SysSoftIntegra\Src;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use DOMDocument;
use ZipArchive;

class Sunat
{

    private static $filename = "";

    public function __construct()
    {
    }

    public static function signDocument($filename)
    {
        $doc = new DOMDocument();
        $doc->load('./../files/' . $filename . '.xml');

        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objDSig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
            array('force_uri' => true)
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

        $objKey->loadKey('./../resources/private_key.pem', true);
        $objDSig->sign($objKey);
        $objDSig->add509Cert(file_get_contents('./../resources/public_key.pem'), true, false, array('subjectName' => true));
        $objDSig->appendSignature($doc->getElementsByTagName('ExtensionContent')->item(0));

        $doc->save('../files/' . $filename . '.xml');
        chmod('../files/' . $filename . '.xml', 0777);
        self::$filename = '../files/' . $filename . '.xml';
    }

    public static function getXmlSign()
    {
        $xml = file_get_contents(self::$filename);
        $DOM = new DOMDocument('1.0', 'ISO-8859-1');
        $DOM->preserveWhiteSpace = FALSE;
        $DOM->loadXML($xml);
        return $DOM->saveXML();
    }

    public static function createZip($fileoutput, $fileinput, $filename)
    {
        $zip = new ZipArchive();
        $zip->open($fileoutput, ZipArchive::CREATE);
        $zip->addFile($fileinput, $filename);
        $zip->close();
    }

    public static function extractZip($fileintput, $directoryextract)
    {
        $zip = new ZipArchive();
        if ($zip->open($fileintput) === TRUE) {
            $result = array();
            for ($i = 0; $i < $zip->numFiles; $i++) {
                array_push($result, $zip->getNameIndex($i));
            }
            $zip->extractTo($directoryextract);
            $zip->close();
            return $result;
        } else {
            return false;
        }
    }

    public static function xmlSendBill($NumeroDocumento, $UsuarioSol, $ClaveSol, $filename, $fileencode)
    {

        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken Id="ABC-123">
                    <wsse:Username>' . $NumeroDocumento . '' . $UsuarioSol . '</wsse:Username>
                    <wsse:Password>' . $ClaveSol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendBill>
                <fileName>' . $filename . '</fileName>
                <contentFile>' .  $fileencode  . '</contentFile>
            </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';
    }

    public static function xmlSendSummary($NumeroDocumento, $UsuarioSol, $ClaveSol, $filename, $fileencode)
    {

        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken Id="ABC-123">
                    <wsse:Username>' . $NumeroDocumento . '' . $UsuarioSol . '</wsse:Username>
                    <wsse:Password>' . $ClaveSol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendSummary>
                <fileName>' . $filename . '</fileName>
                <contentFile>' .  $fileencode  . '</contentFile>
            </ser:sendSummary>
        </soapenv:Body>
        </soapenv:Envelope>';
    }

    public static function xmlGetStatus($NumeroDocumento, $UsuarioSol, $ClaveSol, $ticket)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken Id="ABC-123">
                    <wsse:Username>' . $NumeroDocumento . '' . $UsuarioSol . '</wsse:Username>
                    <wsse:Password>' . $ClaveSol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:getStatus>
                <ticket>' . $ticket . '</ticket>
            </ser:getStatus>
        </soapenv:Body>
        </soapenv:Envelope>';
    }

    public static function xmlGetStatusCdr($get)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $get["rucSol"] . '' . $get["userSol"] . '</wsse:Username>
                    <wsse:Password>' . $get["passSol"] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:getStatusCdr>
                <rucComprobante>' . $get["ruc"] . '</rucComprobante>
                <tipoComprobante>' . $get["tipo"] . '</tipoComprobante>
                <serieComprobante>' . $get["serie"] . '</serieComprobante>
                <numeroComprobante>' . $get["numero"] . '</numeroComprobante>
            </ser:getStatusCdr>
        </soapenv:Body>
        </soapenv:Envelope>';
    }

    public static function xmlGetValidService($get)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $get["rucSol"] . '' . $get["userSol"] . '</wsse:Username>
                    <wsse:Password>' . $get["passSol"] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:getStatus>
                <rucComprobante>' . $get["ruc"] . '</rucComprobante>
                <tipoComprobante>' . $get["tipo"] . '</tipoComprobante>
                <serieComprobante>' . $get["serie"] . '</serieComprobante>
                <numeroComprobante>' . $get["numero"] . '</numeroComprobante>
            </ser:getStatus>
        </soapenv:Body>
        </soapenv:Envelope>';
    }
}
