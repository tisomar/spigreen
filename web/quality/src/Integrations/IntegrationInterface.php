<?php

namespace Integrations;

interface IntegrationInterface
{

    public function getName();

    public function getDefaultParameters();

    public function initialize(array $parameters = array());

    public function getParameters();

    public function setService($serviceName);

    public function getServiceActive();

    public function removeService();

    public function gravar(array $dados, $method, $typeDados = null, $outputType = 'XML');
    public function consultar($dados, $method, $typeDados = null, $codigoId = null, $outputType = 'XML');
    public function deletar($dados, $method, $typeDados = null, $outputType = 'XML');
    public function alterar($dados, $method, $typeDados = null, $codigo = null, $outputType = 'XML');
}
