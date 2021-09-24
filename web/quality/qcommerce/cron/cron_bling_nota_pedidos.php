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
use Integrations\Models\Bling\Bling;
use QPress\Container\Container;

/* @var $container Container */
/* @var $integration Bling */
$integration = $container->getIntegrationManager()->get('Bling');
$integration->setService('PedidoBling');

try {
    $arrPedidos = PedidoQuery::create()->select('ID')->where(
        'ID IN (
		SELECT 
			PEDIDO_ID 
		FROM qp1_integracao_pedido
		WHERE 
			qp1_integracao_pedido.CONCLUIDO_COM_SUCESSO = 1
        )
		AND ID NOT IN (
        SELECT 
			PEDIDO_ID 
		FROM qp1_pedido_nota_fiscal_bling
		
        )
	'
    )->find();
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
}

foreach ($arrPedidos as $objPedido) :
    /** @var $objPedido Pedido */

    try {
        echo 'Integrando nota pedido ' . $objPedido->getId();
        /** @var $response Response */
        $response = $integration
            ->consultar(
                null,
                AbstractIntegration::METHOD_TYPE_GET,
                null,
                $objPedido,
                AbstractIntegration::OUTPUT_TYPE_XML
            );

        if (!isset($response->getResult()->retorno->erros)) :
            $xml = simplexml_load_string($response->getResult());
            $result = json_decode(json_encode($xml), true);

            if (isset($result['pedidos']['pedido']['nota']) && $nota = $result['pedidos']['pedido']['nota']) :
                $objPedidoNota = new PedidoNotaFiscalBling();
                $objPedidoNota->setPedidoId($objPedido);
                $objPedidoNota->setSerie($nota['serie']);
                $objPedidoNota->setNumero($nota['numero']);
                $objPedidoNota->setDataEmissao($nota['dataemissao']);
                $objPedidoNota->setSituacao($nota['situacao']);
                $objPedidoNota->setValorNota($nota['valornota']);
                $objPedidoNota->setChaveAcesso($nota['chaveacesso']);
                $objPedidoNota->save();
            endif;
        endif;
    } catch (\PDOException $pe) {
        echo 'Erro integração nota pedido ' . $objPedido->getId() .': ' . $pe->getMessage();
        $logger->error($pe->getMessage());
    } catch (\PropelException $pe) {
        echo 'Erro integração nota pedido ' . $objPedido->getId() .': ' . $pe->getMessage();
        $logger->error($pe->getMessage());
    } catch (Exception $e) {
        echo 'Erro integração nota pedido ' . $objPedido->getId() .': ' . $e->getMessage();
        $logger->error($e->getMessage());
    }
endforeach;

include_once __DIR__ . '/include/cron-stop.inc.php';
