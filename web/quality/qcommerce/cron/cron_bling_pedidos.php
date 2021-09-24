<?php

date_default_timezone_set("America/Sao_Paulo");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '2048M');

require_once __DIR__ . '/../includes/include_config.inc.php';
$cronFile = __FILE__;
include_once __DIR__ . '/include/cron-init.inc.php';

use Integrations\AbstractIntegration;
use Integrations\Response\Response;
use QPress\Container\Container;

/* @var $container Container */
/* @var $integration Integrations\Models\Bling\Bling */
$integration = $container->getIntegrationManager()->get('Bling');
$integration->setService('PedidoBling');

try {
    $arrIntegracaoPedidos = IntegracaoPedidoQuery::create()
        ->filterByConcluidoComSucesso(0)
        ->usePedidoQuery()
        ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
        ->endUse()
        ->find();
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
}

foreach ($arrIntegracaoPedidos as $objIntegracaoPedido) :
    /** @var $objIntegracaoPedido IntegracaoPedido */
    try {
    /** @var $objPedido Pedido */
        $objPedido = $objIntegracaoPedido->getPedido();

        echo 'Integrando pedido ' . $objPedido->getId();

        $integracaoPedido = PedidoPeer::formatterPedidoIntegrationBling($objPedido, $container);
        /* Comentado por solicitação do cliente em 07/08/2019 para evitar a parte fiscal */
    /*$integracaoPedidoService = PedidoPeer::formatterPedidoServiceIntegrationBling($objPedido, $container);*/

        $response = $integration
            ->gravar(
                $integracaoPedido,
                AbstractIntegration::METHOD_TYPE_POST,
                'object',
                AbstractIntegration::OUTPUT_TYPE_XML
            );
        /** @var $response Response */
        $objIntegracaoPedido->setPedido($objPedido);
        $objIntegracaoPedido->setTipoIntegracao('PedidoBling');
        $objIntegracaoPedido->setConcluidoComSucesso($response->isSuccessful());
        $objIntegracaoPedido->setResult(serialize($response));
        $objIntegracaoPedido->setDataAtualizacao(date('Y-m-d H:i:s'));
        $objIntegracaoPedido->save();
    } catch (\PDOException $pe) {
        echo '<br>Erro integração pedido ' . $objPedido->getId() . ': ' . $pe->getMessage() . '<br><br>';
        $logger->error($pe->getMessage());
    } catch (\PropelException $pe) {
        echo '<br>Erro integração pedido ' . $objPedido->getId() . ': ' . $pe->getMessage() . '<br><br>';
        $logger->error($pe->getMessage());
    } catch (Exception $e) {
        echo '<br>Erro integração pedido ' . $objPedido->getId() . ': ' . $e->getMessage() . '<br><br>';
        $logger->error($e->getMessage());
        $objIntegracaoPedido->setPedidoId($objPedido->getId());
        $objIntegracaoPedido->setTipoIntegracao('PedidoBling');
        $objIntegracaoPedido->setConcluidoComSucesso(false);
        $objIntegracaoPedido->setResult(serialize(array($e->getMessage())));
        $objIntegracaoPedido->setDataAtualizacao(date('Y-m-d H:i:s'));
        $objIntegracaoPedido->save();
    }catch (\Throwable $th) {
        var_dump($th->getMessage());die;
    }
endforeach;

include_once __DIR__ . '/include/cron-stop.inc.php';
