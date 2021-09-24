<?php

namespace Integrations\Models\Bling\Services;


use Integrations\Helper\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractService implements ServiceInterface
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
     * AbstractService constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return Helper::getGatewayShortName(get_class($this));
    }

    /**
     * @param array $parameters
     * @return $this
     */
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

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

}
