<?php

namespace QPress\Gateway;

interface GatewayInterface
{

    public function getName();

    public function getShortName();

    public function getDefaultParameters();

    public function initialize(array $parameters = array());

    public function getParameters();

    public function purchase(\BasePedidoFormaPagamento $formaPagamento);
}
