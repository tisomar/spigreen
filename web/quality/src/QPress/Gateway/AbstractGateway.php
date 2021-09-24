<?php

namespace QPress\Gateway;

use QPress\Gateway\GatewayInterface;
use QPress\Gateway\Helper\Helper;
use QPress\Gateway\Services\PagSeguro\PagSeguro;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractGateway implements GatewayInterface
{

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $httpRequest;

    /**
     * Cria uma nova instancia do Gateway
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

    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }

    protected function getDefaultHttpRequest()
    {
        return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    }

}
