<?php

namespace Integrations\Models\Bling\Services;

interface ServiceInterface
{

    public function getName();

    public function getDefaultParameters();

    public function initialize(array $parameters = array());

    public function getParameters();
}
