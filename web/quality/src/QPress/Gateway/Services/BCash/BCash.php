<?php

namespace QPress\Gateway\Services\BCash;

use QPress\Gateway\Services\BCash\Response\Response;

class BCash extends \QPress\Gateway\AbstractGateway
{

    CONST STATUS_TRANSACAO_EM_ANDAMENTO = 0;
    CONST STATUS_TRANSACAO_CONCLUIDA = 1;
    CONST STATUS_TRANSACAO_CANCELADA = 2;

    public function getName()
    {
        return 'BCash';
    }

    function __construct()
    {
        $this->initialize();
    }

    public function getDefaultParameters()
    {
        return array();
    }

    // ------------------------------------------------------------------------

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        return new Response(array());
    }

    public function consult($transaction_referente)
    {

//        $email = "rafael.cordeiro@qualitypress.com.br";
//        $token = "D3B32CC7EEA04FE74DCF02C623FB476D";
//        $urlPost = "https://www.bcash.com.br/checkout/verify/";
//
//        $post = "transacao=$transaction_referente" .
//            "&token=$token";
//
//        ob_start();
//        $ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,$urlPost);
//        curl_setopt($ch,CURLOPT_POST,1);
//        curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
//        curl_setopt($ch,CURLOPT_HTTPHEADER,array("Authorization: Basic ".base64_encode($email.":".$token)));
//        curl_exec($ch);
//        $resposta=ob_get_contents();
//        ob_end_clean();
//        //$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
//
//        echo "<pre>";
//        var_dump($resposta);
//        die;
//
//        if($httpCode != "200"){
//
//        }else{
//
//        }

    }
}
