<?php
namespace Integrations\Request;

use Integrations\Response\Response;

/**
 * Class Request
 *
 */
class Request implements RequestInterface
{
    protected $baseUrl = '';
    protected $URL = '';
    protected $header = array();
    protected $body = array();
    private $response = '';

    /**
     * Init Request client with base url.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl){

        if(!ctype_digit($baseUrl) && !is_array($baseUrl))
            $this->baseUrl = $baseUrl;

        $this->response = new Response();

    }
    /**
     * @return string
     */
    public function getBaseUrl(){
        return $this->baseUrl;
    }

    /**
     * @return array
     */
    public function getHeaders(){
        return $this->header;
    }

    /*public function call($method, $uri, $headers = array(), $body = null)
    {
        $request = new Request($method, $uri, $headers, $body);
        return $this->send($request);
    }*/


    /**
     * Options Curl
     *
     * Options accepts is
     *
     * @param string[] $headers
     *
     *
     */
    private function setHeaderOptions($headers = array())
    {


        $this->header = $headers;

    }

    private function createUrl($uri)
    {

        $this->URL = ((substr($this->getBaseUrl(), -1) == '/') ? $this->getBaseUrl(): $this->getBaseUrl().'/').$uri ;

    }

    /**
     * @param string $uri
     * @param string[] $headers
     *
     * @return Response
     */
    public function get($uri, $headers = array())
    {
        $this->createUrl($uri);
        $this->setHeaderOptions($headers);
        $this->executeCurl();
        return $this->response;
    }
    /**
     * @param string $uri
     * @param string[] $headers
     *
     * @return Response
     */
    public function post($uri, $headers = array())
    {
        $this->createUrl($uri);
        $this->setHeaderOptions($headers);
        $this->executeCurl();
        return $this->response;
    }
    /**
     * @param string $uri
     * @param string[] $headers
     *
     * @return Response
     */
    public function put($uri, $headers = array())
    {
        $this->createUrl($uri);
        $this->setHeaderOptions($headers);
        $this->executeCurl();
        return $this->response;
    }

    /**
     * @param string $uri
     * @param string[] $headers
     *
     * @return Response
     */
    public function delete($uri, $headers = array())
    {
        $this->createUrl($uri);
        $this->setHeaderOptions($headers);
        $this->executeCurl();
        return $this->response;
    }
    /**
     *
     * @return Response
     */
    private function executeCurl(){

        $r = '';
        $ch = curl_init();

        try{

            curl_setopt($ch, CURLOPT_URL, $this->getUrl());
            curl_setopt_array($ch, $this->getHeaders());
            $r = curl_exec($ch);
            $info = curl_getinfo($ch);

            $error = array('number' => curl_errno($ch), 'message' => curl_error($ch));
            curl_close($ch);

        } catch (\Exception $exception){
            $info = curl_getinfo($ch);
            $error = array('number' => curl_errno($ch), 'message' => curl_error($ch));
        }
        $return = array('result' => $r, 'error' => $error, 'status' => $info);

        return $this->response->setData($return);

        //return $return;
    }


    public function isSuccessful(){}

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect(){}

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage(){}

    /**
     * Response code
     *
     * @return string A response code from the payment gateway
     */
    public function getCode(){}

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference(){}

    public function getData(){}

    public function getStatus(){}

    public function getUrl(){
        return $this->URL;
    }

}