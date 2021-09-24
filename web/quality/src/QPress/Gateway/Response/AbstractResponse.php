<?php

namespace QPress\Gateway\Response;

abstract class AbstractResponse implements ResponseInterface
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function isRedirect()
    {
        return false;
    }

    public function getMessage()
    {
        return null;
    }

    public function getCode()
    {
        return null;
    }

    public function getTransactionReference()
    {
        return null;
    }
    
    public function redirect() {
        
    }
    
}
