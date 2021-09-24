<?php

namespace QPress\Gateway\Services\PagSeguroTransparente;

use QPress\Gateway\Services\PagSeguroTransparente\Response\Response;

include __DIR__ . '/PagSeguroLibrary/PagSeguroLibrary.php';

class PagSeguroTransparente extends \QPress\Gateway\AbstractGateway
{

    private $session = null;

    private $credentials = null;

    private $defaultParameters = array();

    function __construct($email, $token, $environment)
    {
        $this->defaultParameters = array(
            'email' => $email,
            'token' => $token,
            'environment' => $environment
        );

        \PagSeguroConfig::setEnvironment($environment);

        $this->credentials = new \PagSeguroAccountCredentials($email, $token);

    }

    public function getDefaultParameters()
    {
        return $this->defaultParameters;
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }

    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }

    public function getSenderHash() {
        return $this->getParameter('sender_hash');
    }

    public function setSenderHash($value) {
        return $this->setParameter('sender_hash', $value);
    }

    public function getCardToken() {
        return $this->getParameter('card_token');
    }

    public function setCardToken($value) {
        return $this->setParameter('card_token', $value);
    }

    public function getCard() {
        return $this->getParameter('card');
    }

    public function setCard($value) {
        return $this->setParameter('card', $value);
    }

    public function getDebitoOnline() {
        return $this->getParameter('debito_online');
    }

    public function setDebitoOnline($value) {
        return $this->setParameter('debito_online', $value);
    }

    public function getInstallmentValue() {
        return $this->getParameter('installment_value');
    }

    public function setInstallmentValue($value) {
        return $this->setParameter('installment_value', $value);
    }


    public function getSession() {
        if (is_null($this->session)) {
            $this->session = \PagSeguroSessionService::getSession($this->getCredentials());
        }
        return $this->session;
    }


    public function getCredentials() {
        if (is_null($this->credentials)) {
            $this->credentials = new \PagSeguroAccountCredentials($this->getEmail(), $this->getToken());
        }
        return $this->credentials;
    }

    // ------------------------------------------------------------------------

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {

        $carrinho = $formaPagamento->getPedido();
        # Inicia a insersão das informações para o pagamento
        $directPaymentRequest = new \PagSeguroDirectPaymentRequest();
        $directPaymentRequest->setPaymentMode('DEFAULT');
        $directPaymentRequest->setCurrency("BRL");
        $directPaymentRequest->setReceiverEmail($this->getEmail());

        /**
         * Alica o desconto
         */
        $directPaymentRequest->setExtraAmount(-$carrinho->getValorDesconto());

        /**
         * Itens do pedido
         */
        foreach ($carrinho->getPedidoItems() as $pedidoItem)
        {
            $id             = !is_null($pedidoItem->getProdutoVariacao()->getSku()) ? $pedidoItem->getProdutoVariacao()->getSku() : $pedidoItem->getProdutoVariacao()->getId();
            $description    = resumo($pedidoItem->getProdutoVariacao()->getProdutoNomeCompleto(), 100, null);
            $quantity       = $pedidoItem->getQuantidade();
            $amount         = $pedidoItem->getValorUnitario();

            $directPaymentRequest->addItem($id, $description, $quantity, $amount);
        }

        /**
         * Adiciona o ID do pedido
         */
        $directPaymentRequest->setReference($carrinho->getId());

        /**
         * Informações do cliente
         */
        $directPaymentRequest->setSenderHash($this->getSenderHash());

        $cliente        = $carrinho->getCliente();
        $name           = $cliente->isPessoaFisica() ? $cliente->getNomeCompleto() : $cliente->getRazaoSocial();
        $documentType   = $cliente->isPessoaFisica() ? 'CPF' : 'CNPJ';
        $documentValue  = $cliente->isPessoaFisica() ? $cliente->getCpf() : $cliente->getCnpj();
        $areaCode       = preg_replace('/[^\d]/', '', $cliente->getTelefoneDDD());
        $phoneNumber    = preg_replace('/[^\d]/', '', $cliente->getTelefoneSemDDD());

        if ($this->getEnvironment() == 'sandbox') {
            $email = preg_replace("/(.*)@.*/", "$1", $this->getEmail()) . '@sandbox.pagseguro.com.br';
        } else {
            $email = $cliente->getEmail();
        }

        $directPaymentRequest->setSender(
            $name,
            $email,
            $areaCode,
            $phoneNumber,
            $documentType,
            $documentValue,
            true
        );

        /**
         * Informações da forma de entrega
         */
        $directPaymentRequest->setShippingType(\PagSeguroShippingType::getCodeByType('NOT_SPECIFIED'));
        $directPaymentRequest->setShippingCost($carrinho->getValorEntrega());

        /**
         * Informações do endereço para entrega
         */
        $postalCode = $carrinho->getEndereco()->getCep();
        $street     = $carrinho->getEndereco()->getLogradouro();
        $number     = $carrinho->getEndereco()->getNumero();
        $complement = $carrinho->getEndereco()->getComplemento();
        $district   = $carrinho->getEndereco()->getBairro();
        $city       = $carrinho->getEndereco()->getCidade()->getNome();
        $state      = $carrinho->getEndereco()->getCidade()->getEstado()->getSigla();
        $country    = 'BRA';

        $directPaymentRequest->setShippingAddress($postalCode, $street, $number, $complement, $district, $city, $state, $country);

        /**
         * PARA PAGAMENTO VIA BOLETO
         */
        if ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO) {

            $directPaymentRequest->setPaymentMethod('BOLETO');

        }
        /**
         * PARA PAGAMENTO VIA CARTAO DE CREDITO
         */
        elseif ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO) {

            $card = $this->getCard();

            $directPaymentRequest->setPaymentMethod('CREDIT_CARD');

            $cardCheckout = new \PagSeguroCreditCardCheckout(
                array(
                    'token' => $this->getCardToken(),
                    'installment' => new \PagSeguroInstallment(
                        array(
                            "quantity"      => $carrinho->getPedidoFormaPagamento()->getNumeroParcelas(),
                            "value"         => number_format($this->getInstallmentValue(), 2, '.', ''),
                        )
                    ),
                    'holder' => new \PagSeguroCreditCardHolder(
                        array(
                            'name'          => $card['titular'], //Equals in Credit Card
                            'documents'     => array(
                                'type'      => 'CPF',
                                'value'     => $card['cpf']
                            ),
                            'birthDate'     => $card['data_nascimento'],
                            'areaCode'      => $areaCode,
                            'number'        => $phoneNumber
                        )
                    ),
                    'billing' => new \PagSeguroBilling(
                        array(
                            'postalCode'    => $postalCode,
                            'street'        => $street,
                            'number'        => $number,
                            'complement'    => $complement,
                            'district'      => $district,
                            'city'          => $city,
                            'state'         => $state,
                            'country'       => $country
                        )
                    )
                )
            );

            //Set credit card for payment
            $directPaymentRequest->setCreditCard($cardCheckout);
        }
        /**
         * PARA PAGAMENTO VIA DÉBITO ONLINE
         */
        elseif ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE) {

            $directPaymentRequest->setPaymentMethod('EFT');

            $directPaymentRequest->setOnlineDebit(
                array(
                    "bankName" => $this->getDebitoOnline()
                )
            );

        }
        try {

            /* @var $responseTransaction \PagSeguroTransaction */
            $responseTransaction = $directPaymentRequest->register($this->getCredentials());

            sleep(3);
            $responseTransaction2 = $this->consult($responseTransaction->getCode());

            $status = $responseTransaction2['status'];

            $parameters = array();
            $parameters['url']              = $responseTransaction->getPaymentLink();
            $parameters['tid']              = $responseTransaction->getCode();
            $parameters['is_successful']    = true;

            if ($status == 7 || !is_null($responseTransaction->getCancellationSource())) {
                $parameters['status']           = \PedidoFormaPagamentoPeer::STATUS_CANCELADO;
            } else {
                if ($status == 3) {
                    $parameters['status']       = \PedidoFormaPagamentoPeer::STATUS_APROVADO;
                } else {
                    $parameters['status']       = \PedidoFormaPagamentoPeer::STATUS_PENDENTE;
                }
            }

            return new Response($parameters);

        } catch (\PagSeguroServiceException $e) {

            $parameters = array();
            $parameters['is_successful']    = false;
            $parameters['status']           = \PedidoFormaPagamentoPeer::STATUS_CANCELADO;
            $parameters['url']              = false;
            $parameters['tid']              = false;

            $response = new Response($parameters);

            if (count($e->getErrors()) > 0) {
                foreach ($e->getErrors() as $key => $error)
                {
                    $response->setCode($error->getCode());
                    $response->setMessage($error->getMessage());
                    break;
                }
            } else {

                if ($e->getHttpStatus()->getStatus() != 200) {
                    $response->setCode($e->getHttpStatus()->getStatus());
                    $response->setMessage($e->getHttpStatus()->getType());
                }
            }

            return $response;
        }

    }

    public function consult($transaction_reference)
    {
        $transaction = \PagSeguroTransactionSearchService::searchByCode($this->getCredentials(), $transaction_reference);

        return array(
            'status' => $transaction->getStatus()->getValue(),
            'pedido_id' => $transaction->getReference()
        );

    }

    // ------------------------------------------------------------------------

    public function getName()
    {
        return 'PagSeguro';
    }

    public function getUrlJavaScriptLib() {
        $envoironment = ($this->getEnvironment() == 'sandbox') ? 'sandbox.' : '';
        return "https://stc." . $envoironment . "pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";
    }

    public function searchByCode($transaction_reference) {
        return \PagSeguroTransactionSearchService::searchByCode($this->getCredentials(), $transaction_reference);
    }

}
