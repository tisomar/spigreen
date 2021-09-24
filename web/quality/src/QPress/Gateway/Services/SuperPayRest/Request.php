<?php

namespace QPress\Gateway\Services\SuperPayRest;

use QPress\Gateway\Services\SuperPayRest\SimpleRestClient;

class Request
{

    /**
     * Estabelecimento do cliente
     * @var String
     */
    public $token = '';

    /**
     * Dados a serem enviados
     * @var String 
     */
    public $payload = '';

    /**
     * Url do seu webservice da locaweb
     */
    public $url = '';

    /**
     * RestClient to be used
     */
    private $_restClient;

    /**
     * Método do envio
     */
    public $httpMethod = 'post';

    // Return the restclient initialized to common requests.
    protected function _buildRestClient()
    {
        return new SimpleRestClient($arquivo_certificado = null, $arquivo_key = null, $senha = null, $user_agent = "LocawebPhpPlugin (SimpleRestClient/Curl)", $options = null);
    }

    public function __construct($client = null)
    {
        if ($client == null)
        {
            $client = $this->_buildRestClient();
        }

        $this->setRestClient($client);
    }

    public function dataToBeEncoded()
    {
        return array_filter(array(
//            'token' => $this->token,
            $this->payload
        ));
    }

    /**
     * Builds the JSON meant to be sent
     * @return json
     */
    public function buildJSON()
    {
        return json_encode($this->dataToBeEncoded());
    }

    public function buildParams()
    {
        $request_url = '';
        if (count($this->dataToBeEncoded()) > 0)
        {
            $request_url .= '?';
            $request_url .= http_build_query($this->dataToBeEncoded());
        }

        return $request_url;
    }

    /**
     * Sends the data to the current url
     * @return type
     */
    public function execute()
    {
        if ($this->httpMethod == 'post')
        {
            $response = $this->post($this->payload);
        }
        else
        {
            $response = $this->get($this->buildParams());
        }

        return json_decode($response);
    }

    /**
     * Executes the get action
     * @param String $data
     * @return String
     */
    public function get($data)
    {
        $request_url = $this->url;
        $request_url .= $data;
        $restclient = $this->_restClient;
        $restclient->getWebRequest($request_url);
        return $restclient->getWebResponse();
    }

    /**
     * Executes the post action.
     * @param type $data
     * @return type
     */
    public function post($data)
    {
        $restclient = $this->_restClient;
        $restclient->postWebRequest($this->url, $data);
        return $restclient->getWebResponse();
    }

    /**
     * Allow setting a different restClient.
     * @param type $client
     */
    private function setRestClient($client)
    {
        $this->_restClient = $client;
    }

    /**
     * Allow getting the restClient.
     * @return type
     */
    public function getRestClient()
    {
        return $this->_restClient;
    }

}
