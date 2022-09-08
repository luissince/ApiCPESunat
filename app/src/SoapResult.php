<?php

namespace SysSoftIntegra\Src;

use Exception;
use SoapFault;
use DOMDocument;
use SysSoftIntegra\Src\Sunat;
use SysSoftIntegra\Src\SoapBuilder;

class SoapResult
{
    private $wsdlURL;

    private $file;

    private $filename;

    private $success = false;

    private $accepted = false;

    private $hashCode = "";

    private $description = "";

    private $ticket = "";

    private $message = "";

    public function __construct($wsdlURL, $filename)
    {
        $this->wsdlURL = $wsdlURL;
        $this->filename = $filename;
    }

    public function sendBill($xmlSend)
    {
        try {
            $client = new SoapBuilder($this->wsdlURL, array('trace' => true));
            $client->SoapClientCall($xmlSend);
            $client->SoapCall("sendBill");
            $result = $client->__getLastResponse();

            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($result);

            $DocXML = $DOM->getElementsByTagName('applicationResponse');
            $response = "";
            foreach ($DocXML as $Nodo) {
                $response = $Nodo->nodeValue;
            }

            if ($response == "" || $response == null) {
                throw new Exception("No se pudo obtener el contenido del nodo applicationResponse.");
            }

            $cdr = base64_decode($response);
            $archivo = fopen('../files/R-' . $this->filename . '.zip', 'w+');
            fputs($archivo, $cdr);
            fclose($archivo);
            chmod('../files/R-' . $this->filename . '.zip', 0777);

            $isExtract = Sunat::extractZip('../files/R-' . $this->filename . '.zip', '../files/');
            if (!$isExtract) {
                throw new Exception("No se pudo extraer el contenido del archivo zip.");
            }

            $xml = file_get_contents('../files/R-' . $this->filename . '.xml');
            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($xml);

            $DocXML = $DOM->getElementsByTagName('ResponseCode');
            $code = "";
            foreach ($DocXML as $Nodo) {
                $code = $Nodo->nodeValue;
            }

            $DocXML = $DOM->getElementsByTagName('Description');
            $description = "";
            foreach ($DocXML as $Nodo) {
                $description = $Nodo->nodeValue;
            }

            $DocXML = $DOM->getElementsByTagName('DigestValue');
            $hashCode = "";
            foreach ($DocXML as $Nodo) {
                $hashCode = $Nodo->nodeValue;
            }

            if (file_exists('../files/' . $this->filename . '.zip')) {
                unlink('../files/' . $this->filename . '.zip');
            }
            if (file_exists('../files/R-' . $this->filename . '.zip')) {
                unlink('../files/R-' . $this->filename . '.zip');
            }

            if ($code == "0") {
                $this->setAccepted(true);
            } else {
                $this->setAccepted(false);
            }
            $this->setCode($code);
            $this->setDescription($description);
            $this->setHashCode($hashCode);
            $this->setSuccess(true);
        } catch (SoapFault $ex) {
            if (file_exists('../files/' . $this->filename . '.xml')) {
                // unlink('../files/' . $this->filename . '.xml');
            }
            if (file_exists('../files/' . $this->filename . '.zip')) {
                // unlink('../files/' . $this->filename . '.zip');
            }
            $code = preg_replace('/[^0-9]/', '', $ex->faultcode);
            $message = $ex->faultstring;
            $this->setSuccess(false);
            $this->setCode($code);
            $this->setDescription("SoapFault -> "+$message);
        } catch (Exception $ex) {
            if (file_exists('../files/' . $this->filename . '.xml')) {
                // unlink('../files/' . $this->filename . '.xml');
            }
            if (file_exists('../files/' . $this->filename . '.zip')) {
                // unlink('../files/' . $this->filename . '.zip');
            }
            if (file_exists('../files/R-' . $this->filename . '.zip')) {
                // unlink('../files/R-' . $this->filename . '.zip');
            }
            $this->setSuccess(false);
            $this->setCode("-1");
            $this->setDescription("Exception -> ".$ex->getMessage());
        }
    }

    public function sendSumary($xmlSend)
    {
        try {
            $client = new SoapBuilder($this->wsdlURL, array('trace' => true));
            $client->SoapClientCall($xmlSend);
            $client->SoapCall("sendSummary");
            $result = $client->__getLastResponse();

            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($result);

            $DocXML = $DOM->getElementsByTagName('ticket');
            $ticket = "";
            foreach ($DocXML as $Nodo) {
                $ticket = $Nodo->nodeValue;
            }

            $this->setTicket($ticket);
            $this->setSuccess(true);
        } catch (SoapFault $ex) {
            $code = preg_replace('/[^0-9]/', '', $ex->faultcode);
            $message = $ex->faultstring;
            $this->setSuccess(false);
            $this->setCode($code);
            $this->setDescription($message);
        } catch (Exception $ex) {
            $this->setSuccess(false);
            $this->setCode('-1');
            $this->setDescription($ex->getMessage());
        }
    }

    public function sendGetStatus($xmlSend)
    {
        try {
            $client = new SoapBuilder($this->wsdlURL, array('trace' => true));
            $client->SoapClientCall($xmlSend);
            $client->SoapCall("getStatus");
            $result = $client->__getLastResponse();

            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($result);

            $DocXML = $DOM->getElementsByTagName('status');
            $status = "";
            foreach ($DocXML as $Nodo) {
                $status = $Nodo->nodeValue;
            }
            if ($status == "") {
                throw new Exception("No se pudo obtener el contenido del nodo status.");
            }
            $cdr = base64_decode($status);
            $archivo = fopen('../files/R-' . $this->filename . '.zip', 'w+');
            fputs($archivo, $cdr);
            fclose($archivo);
            chmod('../files/R-' . $this->filename . '.zip', 0777);

            $isExtract = Sunat::extractZip('../files/R-' . $this->filename . '.zip', '../files/');
            if (!$isExtract) {
                throw new Exception("No se pudo extraer el contenido del archivo zip.");
            }

            $xml = file_get_contents('../files/R-' . $this->filename . '.xml');
            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($xml);

            $DocXML = $DOM->getElementsByTagName('ResponseCode');
            $code = "";
            foreach ($DocXML as $Nodo) {
                $code = $Nodo->nodeValue;
            }

            $DocXML = $DOM->getElementsByTagName('Description');
            $description = "";
            foreach ($DocXML as $Nodo) {
                $description = $Nodo->nodeValue;
            }

            if (file_exists('../files/' . $this->filename . '.zip')) {
                unlink('../files/' . $this->filename . '.zip');
            }
            if (file_exists('../files/R-' . $this->filename . '.zip')) {
                unlink('../files/R-' . $this->filename . '.zip');
            }

            if ($code == "0") {
                $this->setAccepted(true);
            } else {
                $this->setAccepted(false);
            }

            $this->setCode($code);
            $this->setDescription($description);
            $this->setSuccess(true);
        } catch (SoapFault $ex) {
            if (file_exists('../files/' . $this->filename . '.xml')) {
                unlink('../files/' . $this->filename . '.xml');
            }
            if (file_exists('../files/' . $this->filename . '.zip')) {
                unlink('../files/' . $this->filename . '.zip');
            }
            $code = preg_replace('/[^0-9]/', '', $ex->faultcode);
            $message = $ex->faultstring;
            $this->setSuccess(false);
            $this->setCode($code);
            $this->setDescription($message);
        } catch (Exception $ex) {
            if (file_exists('../files/' . $this->filename . '.xml')) {
                unlink('../files/' . $this->filename . '.xml');
            }
            if (file_exists('../files/' . $this->filename . '.zip')) {
                unlink('../files/' . $this->filename . '.zip');
            }
            if (file_exists('../files/R-' . $this->filename . '.zip')) {
                unlink('../files/R-' . $this->filename . '.zip');
            }
            $this->setSuccess(false);
            $this->setCode("-1");
            $this->setDescription($ex->getMessage());
        }
    }

    public function sendGetStatusCdr($xmlSend)
    {
        try {
            $client = new SoapBuilder($this->wsdlURL, array('trace' => true));
            $client->SoapClientCall($xmlSend);
            $client->SoapCall("getStatusCdr");
            $result = $client->__getLastResponse();

            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;

            if ($DOM->loadXML($result)) {
                $DocXML = $DOM->getElementsByTagName('statusCode');
                $code = "";
                foreach ($DocXML as $Nodo) {
                    $code = $Nodo->nodeValue;
                }

                $DocXML = $DOM->getElementsByTagName('statusMessage');
                $message = "";
                foreach ($DocXML as $Nodo) {
                    $message = $Nodo->nodeValue;
                }

                $DocXML = $DOM->getElementsByTagName('content');
                $content = "";
                foreach ($DocXML as $Nodo) {
                    $content = $Nodo->nodeValue;
                }

                if ($content != "") {
                    $cdr = base64_decode($content);
                    $archivo = fopen('../../files/R-' . $this->filename . '.zip', 'w+');
                    fputs($archivo, $cdr);
                    fclose($archivo);
                    chmod('../../files/R-' . $this->filename . '.zip', 0777);

                    $isExtract = Sunat::extractZip('../../files/R-' . $this->filename . '.zip', '../../files/');
                    if (!$isExtract) {
                        throw new Exception("No se pudo extraer el contenido del archivo zip.");
                    }

                    $xml = file_get_contents('../../files/' . $isExtract[0]);
                    $DOM = new DOMDocument('1.0', 'utf-8');
                    $DOM->preserveWhiteSpace = FALSE;
                    $DOM->loadXML($xml);

                    $DocXML = $DOM->getElementsByTagName('ResponseCode');
                    $code = "";
                    foreach ($DocXML as $Nodo) {
                        $code = $Nodo->nodeValue;
                    }

                    $DocXML = $DOM->getElementsByTagName('Description');
                    $description = "";
                    foreach ($DocXML as $Nodo) {
                        $description = $Nodo->nodeValue;
                    }

                    $this->setAccepted(true);
                    $this->setCode($code);
                    $this->setMessage($message);
                    $this->setDescription($description);
                    $this->setSuccess(true);
                    $this->setFile('R-' . $this->filename . '.zip');
                } else {
                    $this->setAccepted(false);
                    $this->setCode($code);
                    $this->setMessage($message);
                    $this->setSuccess(true);
                }
            } else {
                throw new Exception("No se pudo obtener el xml de respuesta.");
            }
        } catch (SoapFault $ex) {
            $code = preg_replace('/[^0-9]/', '', $ex->faultcode);
            $message = $ex->faultstring;
            $this->setSuccess(false);
            $this->setCode($code);
            $this->setMessage($message);
        } catch (Exception $ex) {
            $this->setSuccess(false);
            $this->setCode("-1");
            $this->setMessage($ex->getMessage());
        }
    }

    public function sendGetStatusValid($xmlSend)
    {
        try {
            $client = new SoapBuilder($this->wsdlURL, array('trace' => true));
            $client->SoapClientCall($xmlSend);
            $client->SoapCall("getStatus");
            $result = $client->__getLastResponse();

            $DOM = new DOMDocument('1.0', 'utf-8');
            $DOM->preserveWhiteSpace = FALSE;

            if ($DOM->loadXML($result)) {

                $DocXML = $DOM->getElementsByTagName('statusCode');
                $code = "";
                foreach ($DocXML as $Nodo) {
                    $code = $Nodo->nodeValue;
                }

                $DocXML = $DOM->getElementsByTagName('statusMessage');
                $message = "";
                foreach ($DocXML as $Nodo) {
                    $message = $Nodo->nodeValue;
                }

                if ($code == "0001") {
                    $this->setAccepted(true);
                } else {
                    $this->setAccepted(false);
                }
                $this->setCode($code);
                $this->setMessage($message);
                $this->setSuccess(true);
            } else {
                throw new Exception("No se pudo obtener el xml de respuesta.");
            }
        } catch (SoapFault $ex) {
            $code = preg_replace('/[^0-9]/', '', $ex->faultcode);
            $message = $ex->faultstring;
            $this->setSuccess(false);
            $this->setCode($code);
            $this->setMessage($message);
        } catch (Exception $ex) {
            $this->setSuccess(false);
            $this->setCode("-1");
            $this->setMessage($ex->getMessage());
        }
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function isAccepted()
    {
        return $this->accepted;
    }

    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getHashCode()
    {
        return $this->hashCode;
    }

    public function setHashCode($hashCode)
    {
        $this->hashCode = $hashCode;
    }

    public function getTicket()
    {
        return $this->ticket;
    }

    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }
}
