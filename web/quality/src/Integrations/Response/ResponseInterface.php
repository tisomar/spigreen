<?php

namespace Integrations\Response;

interface ResponseInterface
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful();

    public function getData();

    public function getStatus();

}
