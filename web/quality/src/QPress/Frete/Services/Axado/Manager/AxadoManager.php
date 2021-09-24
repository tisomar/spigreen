<?php

namespace QPress\Frete\Services\Axado\Manager;

use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;
use QPress\Frete\Services\Axado\AbstractAxado;
use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\Services\Axado\Manager\VolumeManager;
use QPress\Frete\Services\Axado\AxadoGateway;

/**
 * Class AxadoManager
 * @package QPress\Frete\Services\Axado\Manager
 */
class AxadoManager {

    private $token;

    function __construct($token)
    {
        $this->setToken($token);
    }

    function setToken($v) {
        $this->token = $v;
    }

    function getToken()
    {
        return $this->token;
    }

    function consultQuotes(Package $package) {

        $objGateway = new AxadoGateway($this->getToken());
        $collVolumes = new VolumeManager($package->getAllItems());

        $objGateway->setByArray(array(
            'Volumes' => $collVolumes,
            'CepOrigem' => $package->getClient()->getCepFrom(),
            'CepDestino' => $package->getClient()->getCepTo(),
            'ValorNotafiscal' => $collVolumes->getTotalPrice(),
        ));

        $objGateway->sendRequest()->getCotacoes();

        return $objGateway;

    }

}
