<?php

namespace Integrations\Manager;


class IntegrationManager
{

    private $gateways = array();

    public function all()
    {
        return $this->gateways;
    }

    public function register($gateway)
    {
        $exploded = explode('\\', get_class($gateway));
        $className = array_pop($exploded);
//        $className = Helper::getGatewayShortName(get_class($gateway));
        
        if (!in_array($className, $this->gateways))
        {
            $this->gateways[$className] = $gateway;
        }
    }

    public function get($className)
    {
        if (isset($this->gateways[$className]))
        {
            return $this->gateways[$className];
        }

        return;
    }

}
