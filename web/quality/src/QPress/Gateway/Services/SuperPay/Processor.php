<?php

namespace QPress\Gateway\Services\SuperPay;

include __DIR__ . "/lib/webservices.php";

class Processor
{

    private $usuario = '';
    private $senha = '';
    private $sandbox;

    public function __construct($usuario, $senha, $is_sandbox = true)
    {
        $this->usuario = $usuario;
        $this->senha = $senha;
        $this->sandbox = $is_sandbox;
    }

    function pagamentoCompleto($dados_envio)
    {
        
        if ($this->sandbox) {
            $url = "http://homologacao2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
        } else {
            $url = "http://superpay2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
        }
        
        $parametros = array('transacao' => $dados_envio, 'usuario' => $this->usuario, 'senha' => $this->senha);
        $funcao_chamada = 'pagamentoTransacaoCompleta';
        $retorno = callWebServices($parametros, $funcao_chamada, $url);
        return $retorno;
    }

    function consultaTransacaoEspecifica($dados_envio)
    {
        
        if ($this->sandbox) {
            $url = "http://homologacao2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
        } else {
            $url = "http://superpay2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
        }
        
        $parametros = array('consultaTransacaoWS' => $dados_envio, 'usuario' => $this->usuario, 'senha' => $this->senha);
        $funcao_chamada = 'consultaTransacaoEspecifica';
        $retorno = callWebServices($parametros, $funcao_chamada, $url);
        return $retorno;
    }

//    function pagamentoCompletoMaisCartao($dados_envio)
//    {
//        $parametros = array('transacao' => $dados_envio, 'usuario' => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'pagamentoTransacaoCompletaMaisCartoesCredito';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function operacaoTransacao($dados_envio)
//    {
//        $parametros = array('operacao' => $dados_envio, 'usuario' => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'operacaoTransacao';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function criarRecorrencia($dados_envio)
//    {
//        $parametros = array('recorrenciaWS' => $dados_envio, 'usuario' => array("usuario" => $this->usuario, 'senha' => $this->senha));
//        $funcao_chamada = 'cadastrarRecorrenciaWS';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function consultarRecorrencia($dados_envio)
//    {
//        $parametros = array('recorrenciaConsultaWS' => $dados_envio, 'usuario' => array("usuario" => $this->usuario, 'senha' => $this->senha));
//        $funcao_chamada = 'consultaTransacaoRecorrenciaWS';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function cancelarRecorrencia($dados_envio)
//    {
//        $parametros = array('recorrenciaCancelarWS' => $dados_envio, 'usuario' => array("usuario" => $this->usuario, 'senha' => $this->senha));
//        $funcao_chamada = 'cancelarRecorrenciaWS';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function cadastraPagamentoOneClick($dados_envio)
//    {
//        $parametros = array("dadosOneClick" => $dados_envio, "usuario" => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'cadastraPagamentoOneClick';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function consultaDadosOneClick($dados_envio)
//    {
//        $parametros = array('token' => $dados_envio, "usuario" => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'consultaDadosOneClick';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function alteraCadastraPagamentoOneClick($dados_envio, $token)
//    {
//        $parametros = array('dadosOneClick' => $dados_envio, 'token' => $token, "usuario" => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'alteraCadastraPagamentoOneClick';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
//        return $retorno;
//    }
//
//    function pagamentoOneClick($dados_envio)
//    {
//        $parametros = array('transacao' => $dados_envio, "usuario" => $this->usuario, 'senha' => $this->senha);
//        $funcao_chamada = 'pagamentoOneClick';
//        $retorno = callWebServices($parametros, $funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
//        return $retorno;
//    }

}
