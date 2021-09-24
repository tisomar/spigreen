<?php

namespace QPress\Gateway\Services\PagSeguroTransparente\Response;

use QPress\Gateway\Response\AbstractResponse;

class Response extends AbstractResponse
{

    public function isSuccessful() {
        return $this->data['is_successful'];
    }

    public function getUrl() {
        return $this->data['url'];
    }

    public function getTransactionReference() {
        return $this->data['tid'];
    }

    public function getStatus()
    {
        return $this->data['status'];
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
