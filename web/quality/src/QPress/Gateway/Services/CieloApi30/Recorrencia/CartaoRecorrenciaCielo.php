<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 18/11/2018
 * Time: 11:22
 */

namespace QPress\Gateway\Services\CieloApi30\Recorrencia;

use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

class CartaoRecorrenciaCielo
{

    public function pagar(\BasePedido $carrinho){

        // Configure o ambiente
        $environment = $environment = Environment::sandbox();

        // Configure seu merchant
        $merchant = new Merchant('MID', 'MKEY');

        // Crie uma instância de Sale informando o ID do pedido na loja
        $sale = new Sale('123');

        // Crie uma instância de Customer informando o nome do cliente
        $customer = $sale->customer('Fulano de Tal');

        // Crie uma instância de Payment informando o valor do pagamento
        $payment = $sale->payment(15700);

        // Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
            ->creditCard("123", CreditCard::VISA)
            ->setExpirationDate("12/2018")
            ->setCardNumber("0000000000000001")
            ->setHolder("Fulano de Tal");

        // Configure o pagamento recorrente
        $payment->recurrentPayment(true)->setInterval(RecurrentPayment::INTERVAL_MONTHLY);

        // Crie o pagamento na Cielo
        try {
            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

            $recurrentPaymentId = $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
        } catch (CieloRequestException $e) {
            // Em caso de erros de integração, podemos tratar o erro aqui.
            // os códigos de erro estão todos disponíveis no manual de integração.
            $error = $e->getCieloError();
        }
    }

}