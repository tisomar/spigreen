<?php

namespace QPress\Gateway\Services\PagSeguro\Response;

use QPress\Gateway\Response\AbstractResponse;

class Response extends AbstractResponse
{

    public function isSuccessful()
    {
        return isset($this->data['url']) && !is_null($this->data['url']);
    }

    public function isRedirect()
    {
        return true;
    }
    
    public function redirect() {
        header('Location: ' . $this->data['url']);
        exit;
    }
    
    public function getUrl() {
        return $this->data['url'];
    }

    public function getStatus()
    {
        
    }

    public function setCode($v) {
        $this->data['code'] = $v;
    }

    public function getCode() {
        return isset($this->data['code']) ? $this->data['code'] : null;
    }

    public function setMessage($v) {
        $this->data['message'] = $v;
    }

    public function getMessage() {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }

}
