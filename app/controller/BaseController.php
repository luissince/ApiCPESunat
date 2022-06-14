<?php

namespace SysSoftIntegra\Controller;

class BaseController
{
    /**
     * @access private
     * @var string
     */
    private $protocol = 'HTTP/1.0';

    /**
     * __construct contructor method.
     */
    public function __construct()
    {
        $this->protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    }

    /**
     * __call magic method.
     */

    public function __call($name, $arguments)
    {
        $this->sendOutput('', array($this->protocol . ' 404 Not Found'));
    }

    /**
     * Get URI elements.
     * 
     * @return array
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        return $uri;
    }

    /**
     * Get querystring params.
     * 
     * @return array
     */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    /**
     * Send API output.
     *
     * @access protected
     * @param mixed  $data
     * @param string $httpHeader
     * @return $data
     */
    protected function sendOutput($data, $httpHeaders = array())
    {
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        echo json_encode($data);
        exit;
    }

    /**
     * @access protected
     * @return string
     */
    protected function getProtocol()
    {
        return $this->protocol;
    }
    /**
     * @access protected
     * @return string 
     */
    protected function getContentTypeJson()
    {
        return 'Content-Type: application/json';
    }

    /**
     * @access protected
     * @return string 
     */
    protected function getStatus200()
    {
        return $this->protocol . ' 200 OK';
    }
    /**
     * @access protected
     * @return string 
     */
    protected function getStatus201()
    {
        return $this->protocol . ' 201 Created';
    }

    /**
     * @access protected
     * @return string 
     */
    protected function getStatus400()
    {
        return $this->protocol . ' 400 Bad Request';
    }

    /**
     * @access protected
     * @return string 
     */
    protected function getStatus404()
    {
        return $this->protocol . ' 404 Not Found';
    }

    /**
     * @access protected
     * @return string 
     */
    protected function getStatus422()
    {
        return $this->protocol . ' 422 Unprocessable Entity';
    }

    /**
     * @access protected
     * @return string 
     */
    protected function getStatus500()
    {
        return $this->protocol . ' 500 Internal Server Error';
    }

    /**
     * @access protected
     * @return string GET|POST|PUT|DELETE
     */
    protected function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }
}
