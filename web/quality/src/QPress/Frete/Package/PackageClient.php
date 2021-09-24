<?php

namespace QPress\Frete\Package;

class PackageClient {

    /**
     * @var string
     */
    private $cepFrom;

    /**
     * @var string
     */
    private $cepTo;

    function __construct($cepFrom, $cepTo)
    {
        $this->cepFrom = $this->_cleanCep($cepFrom);
        $this->cepTo = $this->_cleanCep($cepTo);
    }

    private function _cleanCep($cep) {
        return $this->_fillCep(preg_replace('/[^0-9]/', '', $cep));
    }

    private function _fillCep($cep) {
        return str_pad($cep, 8, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $cepFrom
     */
    public function setCepFrom($cepFrom)
    {
        $this->cepFrom = $cepFrom;
    }

    /**
     * @return string
     */
    public function getCepFrom()
    {
        return $this->cepFrom;
    }

    /**
     * @param string $cepTo
     */
    public function setCepTo($cepTo)
    {
        $this->cepTo = $cepTo;
    }

    /**
     * @return string
     */
    public function getCepTo()
    {
        return $this->cepTo;
    }

}