<?php

namespace QPress\Frete\Services\Axado;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\Services\Axado\Manager\VolumeManager;
use QPress\Frete\Services\Axado\Servicos\AxadoService;

class AxadoGateway {

    private $url = 'http://api.axado.com.br/v2/consulta/';
    //private $url = 'http://api.axado.com.br/v2/consulta/';
    private $token;

    private $cep_origem = 0;
    private $cep_destino = 0;
    private $valor_notafiscal = 0.00;
    private $prazo_adicional = 0;
    private $preco_adicional = 0.00;
    private $volumes;

    private $response;
    private $requested = false;
    private $quotes = array();

    function __construct($token)
    {
        $this->token = $token;
    }

    public function toArray() {

        return array(
            'cep_origem' => $this->getCepOrigem(),
            'cep_destino' => $this->getCepDestino(),
            'valor_notafiscal' => $this->getValorNotafiscal(),
            'prazo_adicional' => $this->getPrazoAdicional(),
            'preco_adicional' => $this->getPrecoAdicional(),
            'volumes' => $this->getVolumes()->toArray(),
        );
    }

    public function sendRequest()
    {

        if (empty($this->response)) {

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $this->getUrl());
            curl_setopt($ch,CURLOPT_POST, $this->getVolumes()->getNumberObjects());
            curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($this->toArray()));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);

            //execute post
            $this->response = json_decode(curl_exec($ch));

            //close connection
            curl_close($ch);

        }

        $this->requested = true;

        return $this;

    }

    public function getCotacoes()
    {

        if (!empty($this->response) && (isset($this->response->cotacoes))) {

            if (empty($this->quotes)) {
                foreach ($this->response->cotacoes as $object) {

                    /** @var $object_response DataResponseFrete */
                    $object_response = new DataResponseFrete();
                    $object_response->setDisponivel(true);
                    $object_response->setValor($object->cotacao_preco);
                    $object_response->setPrazo($object->cotacao_prazo);

                    $this->quotes[$object->cotacao_codigo] = new AxadoService($object->servico_metaname, $object->servico_nome, $object_response);

                }
            }

            return $this->quotes;

        }

        return false;

    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url . '?token=' . $this->token;
    }

    /**
     * @param array $values
     */
    public function setByArray(array $values) {
        foreach ($values as $key => $value) {
            if (($method = 'set' . $key) && true === method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * @param mixed $volumes
     */
    public function setVolumes(VolumeManager $volumes)
    {
        $this->volumes = $volumes;
    }

    /**
     * @return VolumeManager
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * @param int $cep_destino
     */
    public function setCepDestino($cep_destino)
    {
        $this->cep_destino = $cep_destino;
    }

    /**
     * @return int
     */
    public function getCepDestino()
    {
        return $this->cep_destino;
    }

    /**
     * @param int $cep_origem
     */
    public function setCepOrigem($cep_origem)
    {
        $this->cep_origem = $cep_origem;
    }

    /**
     * @return int
     */
    public function getCepOrigem()
    {
        return $this->cep_origem;
    }

    /**
     * @param int $prazo_adicional
     */
    public function setPrazoAdicional($prazo_adicional)
    {
        $this->prazo_adicional = $prazo_adicional;
    }

    /**
     * @return int
     */
    public function getPrazoAdicional()
    {
        return $this->prazo_adicional;
    }

    /**
     * @param float $preco_adicional
     */
    public function setPrecoAdicional($preco_adicional)
    {
        $this->preco_adicional = $preco_adicional;
    }

    /**
     * @return float
     */
    public function getPrecoAdicional()
    {
        return $this->preco_adicional;
    }

    /**
     * @param float $valor_notafiscal
     */
    public function setValorNotafiscal($valor_notafiscal)
    {
        $this->valor_notafiscal = $valor_notafiscal;
    }

    /**
     * @return float
     */
    public function getValorNotafiscal()
    {
        return $this->valor_notafiscal;
    }

} 