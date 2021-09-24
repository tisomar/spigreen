<?php

date_default_timezone_set("America/Sao_Paulo");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '2048M');

require_once __DIR__ . '/../includes/include_config.inc.php';
$cronFile = __FILE__;
include_once __DIR__ . '/include/cron-init.inc.php';

use Integrations\AbstractIntegration;

/* @var $container \QPress\Container\Container */
/* @var $integration Integrations\Models\Bling\Bling */
$integration = $container->getIntegrationManager()->get('Bling');
$integration->setService('ContasReceberBling');

try {
    $arrFluxoCaixa = FluxoCaixaQuery::create()
        ->where('(PEDIDO_ID not in (select flu.PEDIDO_ID from qp1_integracao_contas_receber icr 
        join qp1_fluxo_caixa flu 
        on icr.FLUXO_ID = flu.ID GROUP BY flu.PEDIDO_ID) or PEDIDO_ID 
        in (select flu.PEDIDO_ID from qp1_integracao_contas_receber icr join qp1_fluxo_caixa flu 
        on icr.FLUXO_ID = flu.ID where icr.CONCLUIDO_COM_SUCESSO = 0 GROUP BY flu.PEDIDO_ID))')
        ->groupByPedidoId()
        ->orderById()
        ->find();
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
}

foreach ($arrFluxoCaixa as $objFluxo) :
    try {
        /** @var $objFluxo FluxoCaixa */
        $integracaoFluxoCaixa = FluxoCaixaPeer::formatterFluxoCaixaIntegrationBling($objFluxo);
        $objIntegracaoContasReceber = IntegracaoContasReceberQuery::create()
            ->filterByFluxoCaixa($objFluxo)
            ->filterByTipoIntegracao('ContasReceberBling')
            ->findOneOrCreate();

        if ($objIntegracaoContasReceber->isNew()) :
            $response = $integration
                ->gravar(
                    $integracaoFluxoCaixa,
                    AbstractIntegration::METHOD_TYPE_POST,
                    'object',
                    AbstractIntegration::OUTPUT_TYPE_XML
                );
        else :
            if ($objIntegracaoContasReceber->getConcluidoComSucesso() == 0) :
                $response = $integration
                    ->gravar(
                        $integracaoFluxoCaixa,
                        AbstractIntegration::METHOD_TYPE_POST,
                        'object',
                        AbstractIntegration::OUTPUT_TYPE_XML
                    );
            else :
                continue;
            endif;
        endif;

        /** @var $response Integrations\Response\Response */
        $objIntegracaoContasReceber->setFluxoCaixa($objFluxo);
        $objIntegracaoContasReceber->setTipoIntegracao('ContasReceberBling');

        if (isset($response->getResult()->retorno->erros)) :
            $objIntegracaoContasReceber->setConcluidoComSucesso(0);
        else :
            $objIntegracaoContasReceber->setConcluidoComSucesso(1);
        endif;

        $objIntegracaoContasReceber->setResult(serialize($response));
        $objIntegracaoContasReceber->setDataAtualizacao(date('Y-m-d H:i:s'));
        $objIntegracaoContasReceber->save();
    } catch (\PDOException $pe) {
        $logger->error($pe->getMessage());
    } catch (\PropelException $pe) {
        $logger->error($pe->getMessage());
    } catch (Exception $e) {
        $logger->error($e->getMessage());
        $objIntegracaoContasReceber->setFluxoCaixa($objFluxo);
        $objIntegracaoContasReceber->setTipoIntegracao('ContasReceberBling');
        $objIntegracaoContasReceber->setConcluidoComSucesso(0);
        $objIntegracaoContasReceber->setResult(serialize(array($e->getMessage())));
        $objIntegracaoContasReceber->setDataAtualizacao(date('Y-m-d H:i:s'));
        $objIntegracaoContasReceber->save();
    }
endforeach;

include_once __DIR__ . '/include/cron-stop.inc.php';
