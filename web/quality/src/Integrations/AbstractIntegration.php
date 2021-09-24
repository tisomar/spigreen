<?php

namespace Integrations;

use Integrations\Helper\Helper;
use Integrations\Helper\Array2Xml;
use Integrations\Request\Guzzle;
use Integrations\Request\Request;
use Integrations\Version\Guzzle6;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractIntegration extends Array2Xml implements IntegrationInterface
{

    /**
     * Output Type for data in send request
     */
    const OUTPUT_TYPE_XML = 'XML';
    const OUTPUT_TYPE_JSON = 'JSON';

    /**
     * Methods for send request
     */
    const METHOD_TYPE_POST = 'POST';
    const METHOD_TYPE_PUT = 'PUT';
    const METHOD_TYPE_GET = 'GET';
    const METHOD_TYPE_DELETE = 'DELETE';


    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $httpRequest;

    /**
     * Cria uma nova instancia do Integration
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->initialize($parameters);
    }

    public function getShortName()
    {
        return Helper::getGatewayShortName(get_class($this));
    }

    public function initialize(array $parameters = array())
    {

        if (is_null($this->parameters)) {
            $this->parameters = new ParameterBag();
        }

        if (is_array($this->getDefaultParameters()) && count($this->getDefaultParameters()) > 0) {
            foreach ($this->getDefaultParameters() as $key => $value) {
                if (is_array($value)) {
                    $this->parameters->set($key, reset($value));
                } else {
                    $this->parameters->set($key, $value);
                }
            }
        }

        if (is_array($parameters) && count($parameters) > 0) {
            Helper::initialize($this, $parameters);
        }

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters->all();
    }

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * @param $dados
     * @param $nodeRoot
     * @param $outputType
     * @return mixed
     */
    public function build($dados, $nodeRoot, $outputType)
    {
        $outputType = 'build' . ucfirst(mb_strtolower($outputType));

        return $this->$outputType($dados, $nodeRoot);
    }


    protected function buildXml($array, $context)
    {

        $xml = new Array2Xml($context);
        $xml->createNode($array);

        return rawurlencode(str_replace('<node>', '', str_replace('</node>', '', $xml->saveXML())));
    }

    protected function buildJson($array, $context)
    {

        return json_encode(array($context => $array));
    }

    protected function send(Request $request, $uri, $method, $headers, array $data)
    {

        $function = mb_strtolower($method);

        if (($method == 'POST' || $method == 'PUT') && count($data)) {
            $headers = $this->setPostHeaders($headers, $data);
        }

        $response = $request->$function($uri, $headers);

        return $response;
    }

    /*protected function send(Guzzle $request, $uri, $method, $contentType, array $data){

        $url = 'https://bling.com.br/Api/v2/'.$uri;
        $this->executeInsertProduct($url, $data);
    }*/

    /*public function executeInsertProduct($url, $data){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        if (curl_error($curl_handle)) {
            $error_msg = curl_error($curl_handle);
        }

        curl_close($curl_handle);

        var_dump($error_msg, $response);die;
        return $response;
    }*/

    protected function setPostHeaders($headers, array $data)
    {

        $headers[CURLOPT_POST] = count($data);
        $headers[CURLOPT_POSTFIELDS] = $data;

        return $headers;
    }
}
