<?php

namespace Integrations\Response;

use Integrations\Helper\Helper;

class Response implements ResponseInterface
{

    protected $content;
    protected $error;
    protected $result;
    protected $status;

    public function __construct()
    {
    }

    public function setData($data)
    {
        $this->content = $data;

        foreach ($this->content as $key => $value) {
            if (!ctype_digit($key)) {
                $this->set($key, $value);
            }
        }
    }

    public function getData()
    {
        return $this->content;
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return (in_array($this->getStatusCode(), [200, 201])
            && !isset($this->result->retorno->erros)
            && empty($this->error['message'])) ? true : false;
    }


    public function getResult()
    {
        return $this->result;
    }

    public function getError()
    {
        $error = '';
        if (!empty($this->error['message'])) {
            $error = $this->error;
        } elseif (isset($this->result->retorno->erros)) {
            $error = $this->result->retorno->erros;
        }
        return $error;
    }

    public function getStatusCode()
    {
        return isset($this->status['http_code']) ? $this->status['http_code'] : 0;
    }

    public function getStatus()
    {
        return isset($this->status) ? $this->status : '';
    }

    protected function isPropertyExist($field)
    {
        return (property_exists($this, $field)) ? true : false;
    }

    protected function set($propert, $value)
    {
        if ($this->isPropertyExist($propert)) {
            if (!is_array($value) && Helper::isJson($value)) {
                $this->$propert = json_decode($value);
            } else {
                $this->$propert = $value;
            }
        }
    }
}
