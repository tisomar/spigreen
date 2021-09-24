<?php
$dadosCartao = CartaoCieloDadosQuery::create()->orderById(Criteria::DESC)->findOneByCieloPaymentId($_REQUEST['PaymentId']);

$aditionalParameters = array();

$gateway = $container->getGatewayManager()->get('CieloApi');
$gateway->initialize($aditionalParameters);

$objResponse = $gateway->autenticatePaymentOrderDebit($dadosCartao->getPedido(), $dadosCartao);

redirect('/checkout/confirmacao/' . md5($dadosCartao->getPedido()->getId()));
exit();
