<?php

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_pedido_forma_pagamento' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PedidoFormaPagamentoPeer extends BasePedidoFormaPagamentoPeer
{

    /**
     * Meios de Pagamento
     */
    CONST FORMA_PAGAMENTO_BOLETO = 'BOLETO';
    CONST FORMA_PAGAMENTO_CARTAO_CREDITO = 'CARTAO_CREDITO';
    CONST FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO = 'CIELO_CARTAO_CREDITO';
    CONST FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO = 'CIELO_CARTAO_DEBITO';
    CONST FORMA_PAGAMENTO_CIELO_BOLETO_BB = 'CIELO_BOLETO_BB';
    CONST FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO = 'CIELO_BOLETO_BRADESCO';
    CONST FORMA_PAGAMENTO_PAGSEGURO = 'PAGSEGURO';
    CONST FORMA_PAGAMENTO_BCASH = 'BCASH';
    CONST FORMA_PAGAMENTO_PAYPAL = 'PAYPAL';
    CONST FORMA_PAGAMENTO_FATURAMENTO_DIRETO = 'FATURAMENTO_DIRETO';
    CONST FORMA_PAGAMENTO_ITAUSHOPLINE = 'ITAU_SHOPLINE';
    CONST FORMA_PAGAMENTO_PAGSEGURO_BOLETO = 'PAGSEGURO_BOLETO';
    CONST FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO = 'PAGSEGURO_CARTAO_CREDITO';
    CONST FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE = 'PAGSEGURO_DEBITO_ONLINE';
    CONST FORMA_PAGAMENTO_PONTOS = 'PONTOS';
    CONST FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL = 'PONTOS_CLIENTE_PREFERENCIAL';
    CONST FORMA_PAGAMENTO_BONUS_FRETE = 'BONUS_FRETE';
    CONST FORMA_PAGAMENTO_EM_LOJA = 'EM_LOJA';
    CONST FORMA_PAGAMENTO_TRANSFERENCIA = 'TRANSFERENCIA';

    /**
     * @todo
     *
     * Forma de pagamento abaixo não será usada no momento, apenas na implementação da assinatura.
     *
     */
    CONST FORMA_PAGAMENTO_CIELO_RECORRENCIA = 'CIELO_RECORRENCIA';

    /*
     * Tipo de Desconto do Boleto
     */
    CONST BOLETO_DESCONTO_ITENS = '1';
    CONST BOLETO_DESCONTO_TOTAL = '2';

    /**
     * Bandeiras para Cartões de Crédito
     */
    CONST BANDEIRA_VISA = 'VISA';
    CONST BANDEIRA_MASTERCARD = 'MASTERCARD';
    CONST BANDEIRA_DINERS = 'DINERS';
    CONST BANDEIRA_AMEX = 'AMEX';
    CONST BANDEIRA_ELO = 'ELO';
    CONST BANDEIRA_DISCOVERS = 'DISCOVER';
    CONST BANDEIRA_HIPERCARD = 'HIPERCARD';
    CONST BANDEIRA_JCB = 'JCB';

    CONST BANDEIRA_DESC_VISA = 'Visa';
    CONST BANDEIRA_DESC_MASTERCARD = 'Mastercard';
    CONST BANDEIRA_DESC_DINERS = 'Diners';
    CONST BANDEIRA_DESC_AMEX = 'American Express';
    CONST BANDEIRA_DESC_ELO = 'ELO';
    CONST BANDEIRA_DESC_DISCOVERS = 'Discover';
    CONST BANDEIRA_DESC_HIPERCARD = 'Hipercad';
    CONST BANDEIRA_DESC_JCB = 'JCB';

    CONST CODIGOS_FORMA_PAGAMENTO = array(
        'visa' => 120,
        'mastercard' => 121,
        'american express' => 122,
        'elo' => 123,
        'diners' => 124,
        'discover' => 125,
        'hipercard' => 126,
        'jcb' => 127
    );

    /**
     * Status do pagamento
     */
    CONST STATUS_PENDENTE = 'PENDENTE';
    CONST STATUS_APROVADO = 'APROVADO';
    CONST STATUS_NEGADO = 'NEGADO';
    CONST STATUS_CANCELADO = 'CANCELADO';

    /**
     * Retorna as bandeiras disponíveis
     *
     * @return array
     */
    public static function listBandeirasDisponiveis()
    {
        $response = array();

//        if (Config::get('meio_pagamento.cc_visa')) {
            $response[self::BANDEIRA_VISA] = array(self::BANDEIRA_VISA => self::BANDEIRA_DESC_VISA);
//        }

//        if (Config::get('meio_pagamento.cc_mastercard')) {
            $response[self::BANDEIRA_MASTERCARD] = array(self::BANDEIRA_MASTERCARD => self::BANDEIRA_DESC_MASTERCARD);
//        }

//        if (Config::get('meio_pagamento.cc_amex')) {
            $response[self::BANDEIRA_AMEX] = array(self::BANDEIRA_AMEX => self::BANDEIRA_DESC_AMEX) ;
//        }

//        if (Config::get('meio_pagamento.cc_diners')) {
            $response[self::BANDEIRA_DINERS] = array(self::BANDEIRA_DINERS => self::BANDEIRA_DESC_DINERS);
//        }

//        if (Config::get('meio_pagamento.cc_elo')) {
            $response[self::BANDEIRA_ELO] = array(self::BANDEIRA_ELO => self::BANDEIRA_DESC_ELO);
//        }

//        if (Config::get('meio_pagamento.cc_discover')) {
            $response[self::BANDEIRA_DISCOVERS] = array(self::BANDEIRA_DISCOVERS => self::BANDEIRA_DESC_DISCOVERS);
//        }

//        if (Config::get('meio_pagamento.cc_hipercard')) {
            $response[self::BANDEIRA_HIPERCARD] = array(self::BANDEIRA_HIPERCARD => self::BANDEIRA_DESC_HIPERCARD);
//}

//        if (Config::get('meio_pagamento.cc_jcb')) {
            // $response[self::BANDEIRA_JCB] = array(self::BANDEIRA_JCB => self::BANDEIRA_DESC_JCB);
//}
        return $response;
    }


    /**
     * Retorna as bandeiras disponíveis débito
     *
     * @return array
     */
    public static function listBandeirasDisponiveisDebito()
    {
        $response = array();

//        if (Config::get('meio_pagamento.cc_visa')) {
        $response[self::BANDEIRA_VISA] = array(self::BANDEIRA_VISA => self::BANDEIRA_DESC_VISA);
//        }

//        if (Config::get('meio_pagamento.cc_mastercard')) {
        $response[self::BANDEIRA_MASTERCARD] = array(self::BANDEIRA_MASTERCARD => self::BANDEIRA_DESC_MASTERCARD);
//        }

//        if (Config::get('meio_pagamento.cc_amex')) {
//        $response[self::BANDEIRA_AMEX] = array(self::BANDEIRA_AMEX => self::BANDEIRA_DESC_AMEX) ;
//        }

//        if (Config::get('meio_pagamento.cc_diners')) {
//        $response[self::BANDEIRA_DINERS] = array(self::BANDEIRA_DINERS => self::BANDEIRA_DESC_DINERS);
//        }

//        if (Config::get('meio_pagamento.cc_elo')) {
//        $response[self::BANDEIRA_ELO] = array(self::BANDEIRA_ELO => self::BANDEIRA_DESC_ELO);
//        }

//        if (Config::get('meio_pagamento.cc_discover')) {
//        $response[self::BANDEIRA_DISCOVERS] = array(self::BANDEIRA_DISCOVERS => self::BANDEIRA_DESC_DISCOVERS);
//        }

        return $response;
    }


    public static function getFormasPagamento() {

        $bandeiras = self::listBandeirasDisponiveis();

        return $bandeiras + array(
            self::FORMA_PAGAMENTO_CARTAO_CREDITO              => 'Cartão de Crédito',
            self::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO        => 'Cartão de Crédito',
            self::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO         => 'Cartão de Débito',
            self::FORMA_PAGAMENTO_BOLETO                      => 'Boleto',
            self::FORMA_PAGAMENTO_PAYPAL                      => 'PayPal',
            self::FORMA_PAGAMENTO_PAGSEGURO                   => 'PagSeguro',
            self::FORMA_PAGAMENTO_FATURAMENTO_DIRETO          => 'Faturamento Direto',
            self::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE     => 'PagSeguro - Débito Online',
            self::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO    => 'PagSeguro - Cartão de Crédito',
            self::FORMA_PAGAMENTO_PAGSEGURO_BOLETO            => 'PagSeguro - Boleto',
            self::FORMA_PAGAMENTO_ITAUSHOPLINE                => 'Itaú Shopline',
            self::FORMA_PAGAMENTO_PONTOS                      => 'Bônus',
            self::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL => 'Pontos',
            self::FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO       => 'Boleto Bradesco',
            self::FORMA_PAGAMENTO_CIELO_BOLETO_BB             => 'Boleto Banco do Brasil',
            self::FORMA_PAGAMENTO_BONUS_FRETE                 => 'Bônus Frete',
            self::FORMA_PAGAMENTO_EM_LOJA                     => 'Pagamento em Loja',
            self::FORMA_PAGAMENTO_TRANSFERENCIA               => 'Transferência',
        );

    }

    /**
     * Gerar uma hash de identificação para a forma de pagamento através do ID desta forma de pagamento.
     *
     * @param   string $hash
     * @return  string
     */
    public static function getHashBoleto($hash)
    {
        return md5(md5($hash));
    }

}
