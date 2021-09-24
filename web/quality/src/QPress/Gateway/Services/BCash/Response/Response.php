<?php

namespace QPress\Gateway\Services\BCash\Response;

use QPress\Gateway\Response\AbstractResponse;

class Response extends AbstractResponse
{

    public function isSuccessful()
    {
        return true;
    }

    public function isRedirect()
    {
        return true;
    }
    
    public function redirect()
    {
        header('Location: ' . $this->getUrl());
        exit;
    }
    
    public function getUrl() {
        return get_url_site() . '/carrinho/redirect-bcash.php';
    }

    public function getStatus()
    {
    }

    public function setCode($v)
    {
        $this->data['code'] = $v;
    }

    public function getCode()
    {
        return isset($this->data['code']) ? $this->data['code'] : null;
    }

    public function setMessage($v)
    {
        $this->data['message'] = $v;
    }

    public function getMessage()
    {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }

}
