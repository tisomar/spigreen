<?php
/*
 * Cron que verifica o resultado da avaliaação do pedido pela ClearSale.
 * Caso o resultado seja aprovado o pedido avançará para o próximo passo.
 * Caso seja reprovado o pedido passará a situação "ALTERACAO" e um email será enviado ao cliente do pedido.
 */

include __DIR__ . '/../includes/include_config.inc.php';

set_time_limit(0);
ini_set('memory_limit', -1);

//Seleciona os pedidos que ja tiveram seus dados enviados a Clear Sale, mas que não possuem o resultado definido.
$query = PedidoQuery::create()
                ->filterByIntegrouClearSale(1)
                ->filterBySituacaoClearSale(null);

//Evita selecionar pedidos muito antigos.
//Se os pedidos anteriores a esta data ainda estão sem resultado, provavelmente ocorreu algum erro desconhecido.
$dataLimite = new DateTime();
$dataLimite->modify('-5 months');
$query->filterByCreatedAt($dataLimite, Criteria::GREATER_THAN);

foreach ($query->find() as $pedido) {
    /* @var $pedido Pedido */
    
    try {
        if (ClearSale::isPedidoAprovado($pedido)) {
            //Pedido foi aprovado.
            echo "Pedido {$pedido->getId()} foi aprovado.<br>";

            $pedido->aprovaClearSale();

            //todo: verificar se é necessario fazer a captura
        } elseif (ClearSale::isPedidoReprovado($pedido)) {
            //Pedido foi reprovado.
            echo "Pedido {$pedido->getId()} foi reprovado.<br>";

            $pedido->reprovaClearSale();
        }
    } catch (RuntimeException $ex) {
        echo $ex->getMessage(), "<br>";
    }
}
