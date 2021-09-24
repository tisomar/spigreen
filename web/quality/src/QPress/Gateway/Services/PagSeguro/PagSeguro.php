<?php

namespace QPress\Gateway\Services\PagSeguro;

include 'PagSeguroLibrary/PagSeguroLibrary.php';

class PagSeguro extends \QPress\Gateway\AbstractGateway
{

    function __construct($email, $token)
    {
        $this->initialize(array(
            'email' => $email,
            'token' => $token,
        ));
    }

    public function getDefaultParameters()
    {
        return array();
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

    // ------------------------------------------------------------------------

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $carrinho = $formaPagamento->getPedido();
        $paymentRequest = $this->createRequest($carrinho);

        $credentials = new \PagSeguroAccountCredentials($this->getEmail(), $this->getToken());

        try
        {
            $url = $paymentRequest->register($credentials);
            return new \QPress\Gateway\Services\PagSeguro\Response\Response(array('url' => $url));
        }
        catch (\PagSeguroServiceException $e)
        {

            $response = new \QPress\Gateway\Services\PagSeguro\Response\Response(array());

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
        $credentials = new \PagSeguroAccountCredentials(\Config::get('pagseguro_email'), \Config::get('pagseguro_token'));
        $transaction = \PagSeguroTransactionSearchService::searchByCode($credentials, $transaction_reference);

        return array(
            'status' => $transaction->getStatus()->getTypeFromValue(),
            'pedido_id' => $transaction->getReference()
        );
        
    }

    // ------------------------------------------------------------------------
    /* @var $carrinho \Carrinho */
    public function createRequest(\BasePedido $carrinho)
    {
        $paymentRequest = new \PagSeguroPaymentRequest();
        $paymentRequest->setCurrency("BRL");

        ### Alterado para acrescentar este valor no total do pedido, pois se mantiver por item, o valor do frete é multiplicado pelo item, e para cada item
        $valorFrete = number_format($carrinho->getValorEntrega(), 2, '.', '');

        /* @var $pedidoItem PedidoItem */
        foreach ($carrinho->getPedidoItems() as $pedidoItem)
        {

            $id = !is_null($pedidoItem->getProdutoVariacao()->getSku()) ? $pedidoItem->getProdutoVariacao()->getSku() : $pedidoItem->getProdutoVariacao()->getId();
            $description = resumo($pedidoItem->getProdutoVariacao()->getProdutoNomeCompleto(), 100, null);
            $quantity = $pedidoItem->getQuantidade();
            $amount = $pedidoItem->getValorUnitario();
            $weight = $pedidoItem->getPeso();

            $paymentRequest->addItem($id, $description, $quantity, $amount, $weight, 0);
        }

        $paymentRequest->setExtraAmount(-$carrinho->getValorDesconto());

        $paymentRequest->setReference($carrinho->getId());

        $codigoFrete = \PagSeguroShippingType::getCodeByType('NOT_SPECIFIED');
        $paymentRequest->setShippingType($codigoFrete);
        $paymentRequest->setShippingCost($valorFrete);

        $postalCode = $carrinho->getEndereco()->getCep();
        $street = $carrinho->getEndereco()->getLogradouro();
        $number = $carrinho->getEndereco()->getNumero();
        $complement = $carrinho->getEndereco()->getComplemento();
        $district = $carrinho->getEndereco()->getBairro();
        $city = $carrinho->getEndereco()->getCidade()->getNome();
        $state = $carrinho->getEndereco()->getCidade()->getEstado()->getSigla();
        $country = 'BRA';

        $paymentRequest->setShippingAddress($postalCode, $street, $number, $complement, $district, $city, $state, $country);

        if ($carrinho->getCliente()->isPessoaJuridica())
        {
            $paymentRequest->setSenderName(trim($carrinho->getCliente()->getRazaoSocial()));
        }
        else
        {
            $paymentRequest->setSenderName(trim($carrinho->getCliente()->getNomeCompleto()));
        }

        $paymentRequest->setSenderEmail($carrinho->getCliente()->getEmail());

        $paymentRequest->setRedirectUrl(get_url_site() . '/pagseguro/response/');

        return $paymentRequest;
    }

    public function getName()
    {
        return 'PagSeguro';
    }

}
