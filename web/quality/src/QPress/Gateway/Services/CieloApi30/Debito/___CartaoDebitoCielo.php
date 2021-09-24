<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 18/11/2018
 * Time: 11:22
 */

namespace QPress\Gateway\Services\CieloApi30\Debito;

use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use QPress\Gateway\Services\CieloApi30\Response\Response;

use Cielo\API30\Ecommerce\Request\CieloRequestException;

class CartaoDebitoCielo
{
    const INICIO_PAGAMENTO      = 'inicio_pagamento';
    const CIELO_AGUARDANDO      = 'aguardando_pagamento';
    const CIELO_PAGO            = 'paga';
    const CIELO_NAO_CAPTURADO   = 'paga_nao_capturada';
    const CIELO_NEGADO          = 'negada';
    const CIELO_CANCELADO       = 'cancelada';

    const TAX_AMOUNT_SERVICE = 0;

    const responseCodeAPI = array(
        "0" => "aguardando_pagamento",
        "1" => "paga",
        "2" => "paga",
        "3" => "negada",
        "10" => "negada",
        "11" => "negada",
        "12" => "aguardando_pagamento",
        "13" => "negada",
        "20" => "paga_nao_capturada",
        "99" => "aguardando_pagamento",
    );

    const responseMessageCodeAPI = array(
        "0" => "Aguardando atualização de status",
        "1" => "Pagamento apto a ser capturado ou definido como pago",
        "2" => "Pagamento confirmado e finalizado",
        "3" => "Pagamento negado por Autorizador",
        "10" => "Pagamento cancelado",
        "11" => "Pagamento cancelado após 23:59 do dia de autorização",
        "12" => "Aguardando Status de instituição financeira",
        "13" => "Pagamento cancelado por falha no processamento ou por ação do AF",
        "20" => "Recorrência agendada",
        "99" => "Pendente de autenticação"
    );

    /**
     * @param \BasePedido $carrinho
     * @param array $amiente
     * @param \CartaoCieloDados $cartaoCieloDados
     */

    public function pagar(\BasePedido $carrinho, array $ambiente, \CartaoCieloDados $cartaoCieloDados = null){

        if(is_null($cartaoCieloDados)){

        }

        $pagamento = $carrinho->getPedidoFormaPagamento();



        //echo'<pre>';var_dump(345345,);die;
        $cliente = $carrinho->getCliente();

        // ...
        // Configure o ambiente
        $environment = $ambiente['ambiente'] == 'sandbox' ? Environment::sandbox() : Environment::production();

        // Configure seu merchant
        $merchant = new Merchant($ambiente['merchant_id'], $ambiente['merchant_key']);

        // Crie uma instância de Sale informando o ID do pedido na loja
        $sale = new Sale($carrinho->getId());

        // Crie uma instância de Customer informando o nome do cliente
        $customer = $sale->customer($cartaoCieloDados->getNome());

        // Crie uma instância de Payment informando o valor do pagamento
        $payment = $sale->payment($carrinho->getValorTotal() * 100);

        $payment->setReturnUrl(get_url_site() . '/checkout/cielo-debito-retorno');

        // Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $payment->setType(Payment::PAYMENTTYPE_DEBITCARD)
            ->setAuthenticate(true)
            ->debitCard($cartaoCieloDados->getCodigo(), $pagamento->getBandeiraCielo())
            ->setExpirationDate($cartaoCieloDados->getValidade())
            ->setCardNumber(str_replace("-","",$cartaoCieloDados->getNumero()))
            ->setHolder($cartaoCieloDados->getNome());

        // Crie o pagamento na Cielo
        try {

            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

            // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
            // dados retornados pela Cielo
            $paymentId = $sale->getPayment()->getPaymentId();

            // Utilize a URL de autenticação para redirecionar o cliente ao ambiente
            // de autenticação do emissor do cartão
            $authenticationUrl = (null !== $sale->getPayment()) ? $sale->getPayment()->getAuthenticationUrl() : null;
            $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;


            $pagamento->setTransacaoId($tid);

            $cartaoCieloDados->setPedidoId($carrinho->getId());
            $cartaoCieloDados->setCieloPaymentId($paymentId);
            $cartaoCieloDados->setStatus(self::INICIO_PAGAMENTO);
            $cartaoCieloDados->save();

            $statusPagamento = self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? 'PENDENTE' : 'APROVADO';

            if(!is_null($authenticationUrl)){

                $cartaoCieloDados->setStatus(self::responseCodeAPI[99]);
                $cartaoCieloDados->save();

                $pagamento->setStatus($statusPagamento);
                $pagamento->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    "status" => self::responseCodeAPI[99] ,
                    "erro" => self::responseCodeAPI[99] != "paga" ? self::responseCodeAPI[99] : false,
                    'code' => 99,
                    'message' => self::responseMessageCodeAPI[99],
                    "url_authentication" => $authenticationUrl,
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);

                return $obj;
            } else {
                if($sale->getPayment()->getStatus() == 1 || $sale->getPayment()->getStatus() == 2){

                    $cartaoCieloDados->setStatus(self::responseCodeAPI[$sale->getPayment()->getStatus()]);
                    $cartaoCieloDados->save();

                    $pagamento->setStatus($statusPagamento);
                    $pagamento->save();

                    $replay = array(
                        "pedido_id" => $carrinho->getId(),
                        'id' => $paymentId,
                        'tid' => $tid,
                        "status" => self::responseCodeAPI[$sale->getPayment()->getStatus()] ,
                        "erro" => self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? $sale->getPayment()->getStatus() : false,
                        'code' => $sale->getPayment()->getStatus(),
                        'message' => self::responseMessageCodeAPI[$sale->getPayment()->getStatus()],
                    );
                    $data = json_decode(json_encode($replay));
                    $obj = new Response($data);

                    return $obj;

                } else {

                    $cartaoCieloDados->setStatus(self::responseCodeAPI[$sale->getPayment()->getStatus()]);
                    $cartaoCieloDados->save();

                    $replay = array(
                        "pedido_id" => $carrinho->getId(),
                        'id' => $paymentId,
                        'tid' => $tid,
                        "status" => self::responseCodeAPI[$sale->getPayment()->getStatus()] ,
                        "erro" => self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? $sale->getPayment()->getReturnCode() : false,
                        'code' => $sale->getPayment()->getReturnCode(),
                        'message' => $sale->getPayment()->getReturnMessage(),
                    );
                    $data = json_decode(json_encode($replay));
                    $obj = new Response($data);

                    return $obj;
                }
            }

        } catch (CieloRequestException $e) {
            // Em caso de erros de integração, podemos tratar o erro aqui.
            // os códigos de erro estão todos disponíveis no manual de integração.
            $cartaoCieloDados->setStatus(self::CIELO_AGUARDANDO);
            $cartaoCieloDados->save();

            $pagamento->setStatus('PENDENTE');
            $pagamento->save();

            $error = $e->getCieloError();

            $errorCode = $error ? $error->getCode() : $e->getCode();
            $errorMessage = $error ? $error->getMessage() : $e->getMessage();

            $replay = array(
                "pedido_id" => $carrinho->getId(),
                'id' => '',
                'tid' => '',
                "status" => self::CIELO_AGUARDANDO,
                "erro" => $errorCode ? $errorCode : false,
                'code' => $errorCode,
                'message' => $errorMessage,
            );
            $data = json_decode(json_encode($replay));
            $obj = new Response($data);

            return $obj;
        }
    }

    public function autenticarPagamento(\BasePedido $carrinho, array $ambiente, \CartaoCieloDados $cartaoCieloDados = null){

        $cliente = $carrinho->getCliente();

        $pagamento = $carrinho->getPedidoFormaPagamento();
        // ...
        // Configure o ambiente
        $environment = $ambiente['ambiente'] == 'sandbox' ? Environment::sandbox() : Environment::production();

        // Configure seu merchant
        $merchant = new Merchant($ambiente['merchant_id'], $ambiente['merchant_key']);

        // Crie uma instância de Sale informando o ID do pedido na loja
        $sale = new Sale($carrinho->getId());

        // Crie uma instância de Customer informando o nome do cliente
        $customer = $sale->customer($cartaoCieloDados->getNome());

        // Crie uma instância de Payment informando o valor do pagamento
        $payment = $sale->payment($carrinho->getValorTotal() * 100);

        // Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $payment->setType(Payment::PAYMENTTYPE_DEBITCARD)
            ->setAuthenticate(true)
            ->debitCard($cartaoCieloDados->getCodigo(), $carrinho->getPedidoFormaPagamento()->getBandeiraCielo())
            ->setExpirationDate($cartaoCieloDados->getValidade())
            ->setCardNumber(str_replace("-","",$cartaoCieloDados->getNumero()))
            ->setHolder($cartaoCieloDados->getNome());

        // Crie o pagamento na Cielo
        try {

            $paymentId = $cartaoCieloDados->getCieloPaymentId();
            // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
            $sale = (new CieloEcommerce($merchant, $environment))->getSale($paymentId);

            $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;

            $statusPagamento = self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? 'PENDENTE' : 'APROVADO';


            if (is_null($sale->getPayment()->getReturnCode()) || $sale->getPayment()->getReturnCode() == "00" || $sale->getPayment()->getReturnCode() == "000" || ($sale->getPayment()->getStatus() == 1 || $sale->getPayment()->getStatus() == 2)) {

                $cartaoCieloDados->setStatus(self::responseCodeAPI[$sale->getPayment()->getStatus()]);
                $cartaoCieloDados->save();

                $pagamento->setStatus('APROVADO');
                $pagamento->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    "status" => self::responseCodeAPI[$sale->getPayment()->getStatus()] ,
                    "erro" => self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? $sale->getPayment()->getStatus() : false,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);

                return $obj;

            } else {

                $cartaoCieloDados->setStatus(self::responseCodeAPI[$sale->getPayment()->getStatus()]);
                $cartaoCieloDados->save();

                $pagamento->setStatus('PENDENTE');
                $pagamento->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    "status" => self::responseCodeAPI[$sale->getPayment()->getStatus()] ,
                    "erro" => self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? $sale->getPayment()->getStatus() : false,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);

                return $obj;
            }



        } catch (CieloRequestException $e) {
            // Em caso de erros de integração, podemos tratar o erro aqui.
            // os códigos de erro estão todos disponíveis no manual de integração.

            $cartaoCieloDados->setStatus(self::CIELO_AGUARDANDO);
            $cartaoCieloDados->save();

            $error = $e->getCieloError();

            $errorCode = $error ? $error->getCode() : $e->getCode();
            $errorMessage = $error ? $error->getMessage() : $e->getMessage();

            $pagamento->setStatus('NEGADO');
            $pagamento->save();


            $replay = array(
                "pedido_id" => $carrinho->getId(),
                'id' => '',
                'tid' => '',
                "status" => self::CIELO_AGUARDANDO,
                "erro" => $errorCode ? $errorCode : false,
                'code' => $errorCode,
                'message' => $errorMessage,
            );
            $data = json_decode(json_encode($replay));
            $obj = new Response($data);

            return $obj;
        }
    }

}