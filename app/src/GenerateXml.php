<?php

namespace SysSoftIntegra\Src;

use DomDocument;

class GenerateXml
{
    private $xml;

    private $invoice;

    public function __construct()
    {
        $this->xml = new DomDocument('1.0', 'utf-8');
        // $xml->standalone         = true;
        $this->xml->preserveWhiteSpace = false;

        $this->invoice =  $this->xml->createElement('Invoice');
        $this->invoice =  $this->xml->appendChild($this->invoice);

        $this->invoice->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
        $this->invoice->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $this->invoice->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $this->invoice->setAttribute('xmlns:ccts', "urn:un:unece:uncefact:documentation:2");
        $this->invoice->setAttribute('xmlns:ds', "http://www.w3.org/2000/09/xmldsig#");
        $this->invoice->setAttribute('xmlns:ext', "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $this->invoice->setAttribute('xmlns:qdt', "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2");
        $this->invoice->setAttribute('xmlns:sac', "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1");
        $this->invoice->setAttribute('xmlns:udt', "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $this->invoice->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
    }

    public function addElementAndChild($parent, $name, $value = '')
    {
        return $parent->appendChild($this->xml->createElement($name, $value));
    }

    public function save($filename)
    {
        $this->xml->formatOutput = true;
        $this->xml->saveXML();
        $this->xml->save($filename);
        chmod($filename, 0777);
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }
}
