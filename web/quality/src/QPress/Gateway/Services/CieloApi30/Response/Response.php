<?php

namespace QPress\Gateway\Services\CieloApi30\Response;

use QPress\Gateway\Response\AbstractResponse;

class Response extends AbstractResponse
{

    private $translationStatus = array(

        # Pendente
        'aguardando_pagamento' => \PedidoFormaPagamentoPeer::STATUS_PENDENTE,

        # Negada
        'negada' => \PedidoFormaPagamentoPeer::STATUS_NEGADO,

        # Cancelado
        'falha_na_operadora' => \PedidoFormaPagamentoPeer::STATUS_CANCELADO,

        # Aprovado
        'paga' => \PedidoFormaPagamentoPeer::STATUS_APROVADO,
        'ja_paga' => \PedidoFormaPagamentoPeer::STATUS_APROVADO,
        'paga_nao_capturada' => \PedidoFormaPagamentoPeer::STATUS_APROVADO,

    );

    public function isSuccessful()
    {
        return $this->data->erro == false;
    }

    public function isRedirect()
    {
        return isset($this->data->url_authentication) ? true : false;
    }

    public function getStatus()
    {
        return isset($this->translationStatus[$this->data->status]) ? $this->translationStatus[$this->data->status] : $this->data->status;
    }

    public function getUrl()
    {
        return $this->data->url_acesso;
    }
    
    public function getTransactionReference()
    {
        return $this->data->tid;
    }

    public function getData()
    {
        return $this->data->id;
    }

    public function getMessage()
    {
        return $this->data->message;
    }

    public function getCode()
    {
        return $this->data->code;
    }

    public function getUrlAuthentication()
    {
        return $this->data->url_authentication;
    }

    public function redirect() {
        header('Location: ' . $this->data->url_authentication);
        exit;
    }

}
