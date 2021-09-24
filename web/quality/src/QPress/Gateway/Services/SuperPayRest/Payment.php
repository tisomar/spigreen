<?php

namespace QPress\Gateway\Services\SuperPayRest;

class Payment
{
    # Atributo informando a url de retorno para a loja após a finalização
    # do processo de pagamento
    # 'url_retorno' => 'http://www.minha-loja.com.br/confirmacao-pedido.php?id=12345'

//    public $url_retorno = '';

    # Atributo que informa se deverá ocorrer a captura automática.
    # Valido apenas para operações via Cielo e Redecard.
    # 'capturar' => 'true'
//    public $capturar = '';

    # Campo contendo os detalhes do pedido a ser enviado:
    #  'pedido' => array(
    #     'numero' => "123",
    #     'total' => "100.00",
    #     'moeda' => "real",
    #     'descricao' => "My Camaro car!"
    #   )
//    public $pedido = '';

    # Atributo contendo as informações sobre o pagamento:
    #  'pagamento' => array(
    #    'meio_pagamento' => 'redecard' # Nesse campo se determina o convenio utilizado.
    #    'bandeira' => "visa",
    #    'cartao_numero' => "4012001037141112",
    #    'cartao_cvv' => "973",
    #    'parcelas' => "1",
    #    'tipo_operacao' => "credito_a_vista",
    #    'cartao_validade' => "082015"
    #  )
//    public $pagamento = '';

    # Atributo contendo as informações sobre o comprador:
    #  'comprador' => array(
    #     'nome' => "Bruna da Silva",
    #     'documento' => "27836038881",
    #     'endereco' => "Rua da Casa",
    #     'numero' => "23",
    #     'cep' => "09710240",
    #     'bairro' => "Centro",
    #     'cidade' => "São Paulo",
    #     'estado' => "SP"
    #   )
//    public $comprador = '';

    public $codigoEstabelecimento = '';

    public $codigoFormaPagamento = '';
    /**
     * 'transacao' => array (
     *   'numeroTransacao' => '699'
     *   'valor' =>  34594
     *   'moeda' => 'real'
     *   'parcelas' => 1
     *  )
     */
    public $transacao = '';

    /**
     *  'dadosCartao' => array (
     *    'nomePortador' => 'Suporte asd'
     *    'numeroCartao' => '5555666677778884'
     *    'codigoSeguranca' => '123'
     *     'dataValidade' => '12/2028'
     *  )
     */
    public $dadosCartao = '';

    /**
     *  'dadosCobranca' => array (
     *    'nome' => 'teste suporte'
     *    'documento' => '740.863.833-49'
     *  )
     */
    public $dadosCobranca = '';

    /**
     *  'itensDoPedido' => array (
     *      0 =>
     *           array (
     *               'quantidadeProduto' => 1
     *              'valorUnitarioProduto' => 337
     *          )
     *  )
     */
    public $itensDoPedido = '';

    public function __construct(Array $data = array())
    {
        extract($data);
        if (isset($codigoEstabelecimento))
        {
            $this->codigoEstabelecimento = $codigoEstabelecimento;
        }
        if (isset($codigoFormaPagamento))
        {
            $this->codigoFormaPagamento = $codigoFormaPagamento;
        }
        if (isset($transacao))
        {
            $this->transacao = $transacao;
        }
        if (isset($dadosCartao))
        {
            $this->dadosCartao= $dadosCartao;
        }
        if (isset($dadosCobranca))
        {
            $this->dadosCobranca = $dadosCobranca;
        }
        if (isset($itensDoPedido))
        {
            $this->itensDoPedido = $itensDoPedido;
        }
    }

    public function toPayload()
    {
        $payload = array();
//        $this->setArrayItemIfValueIsPresent($payload, 'pagamento', $this->pagamento);
//        $this->setArrayItemIfValueIsPresent($payload, 'comprador', $this->comprador);
//        $this->setArrayItemIfValueIsPresent($payload, 'pedido', $this->pedido);
//        $this->setArrayItemIfValueIsPresent($payload, 'capturar', $this->capturar);
//        $this->setArrayItemIfValueIsPresent($payload, 'url_retorno', $this->url_retorno);
        $this->setArrayItemIfValueIsPresent($payload, 'codigoEstabelecimento', $this->codigoEstabelecimento);
        $this->setArrayItemIfValueIsPresent($payload, 'codigoFormaPagamento', $this->codigoFormaPagamento);
        $this->setArrayItemIfValueIsPresent($payload, 'transacao', $this->transacao);
        $this->setArrayItemIfValueIsPresent($payload, 'dadosCartao', $this->dadosCartao);
        $this->setArrayItemIfValueIsPresent($payload, 'dadosCobranca', $this->dadosCobranca);
        $this->setArrayItemIfValueIsPresent($payload, 'itensDoPedido', $this->itensDoPedido);
        return $payload;
    }

    private function setArrayItemIfValueIsPresent(&$array, $index, $value)
    {
        if (isset($value) && !empty($value))
        {
            $array[$index] = $value;
            return true;
        }
        return false;
    }

}
