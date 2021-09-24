<?php

date_default_timezone_set("America/Sao_Paulo");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '2048M');

require_once __DIR__ . '/../includes/include_config.inc.php';
$cronFile = __FILE__;
include_once __DIR__ . '/include/cron-init.inc.php';

use Integrations\AbstractIntegration;
use QPress\Container\Container;

/* @var $container Container */
/* @var $integration Integrations\Models\Bling\Bling */
$integration = $container->getIntegrationManager()->get('Bling');
$integration->setService('ProdutoBling');

$dataIntegracao = new DateTime();

try {
    $arrProdutos = ProdutoQuery::create()->find();
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
}

foreach ($arrProdutos as $objProduto) :
    /** @var $objProduto Produto */

    try {
        $objIntegracaoProduto = IntegracaoProdutoQuery::create()
            ->filterByProduto($objProduto)
            ->filterByTipoIntegracao('ProdutoBling')
            ->findOneOrCreate();

        // $objIntegracaoProdutoService = IntegracaoProdutoQuery::create()
        //     ->filterByProdutoId($objProduto->getId())
        //     ->filterByTipoIntegracao('ProdutoServiceBling')
        //     ->findOneOrCreate();

        if ($objIntegracaoProduto->getConcluidoComSucesso() && $objIntegracaoProduto->getDataAtualizacao(null) >= $objProduto->getDataAtualizacao()):
            continue;
        endif;

        echo 'Integrando produto ' . $objProduto->getId();

        if ($objProduto->hasVariacoes()) :
            $integracaoProduto = ProdutoPeer::formatterProdutoWithVariationsIntegrationBling($objProduto);
        /* Comentado por solicitação do cliente em 07/08/2019 para evitar a parte fiscal */
        /*$integracaoProdutoServico = ProdutoPeer::formatterProdutoServiceIntegrationBling($objProduto);*/
        else :
            $integracaoProduto = ProdutoPeer::formatterProdutoIntegrationBling($objProduto);
            /* Comentado por solicitação do cliente em 07/08/2019 para evitar a parte fiscal */
            /*$integracaoProdutoServico = ProdutoPeer::formatterProdutoServiceIntegrationBling($objProduto);*/
        endif;

        if ($objIntegracaoProduto->isNew()) :
            $response = $integration
                ->gravar(
                    $integracaoProduto,
                    AbstractIntegration::METHOD_TYPE_POST,
                    'object',
                    AbstractIntegration::OUTPUT_TYPE_XML
                );
            //$responseService = $integration->gravar($integracaoProduto, AbstractIntegration::METHOD_TYPE_POST,'object', AbstractIntegration::OUTPUT_TYPE_XML);
        else :
            $codigoProduto = $objProduto->getId();
            $response = $integration
                ->alterar(
                    $integracaoProduto,
                    AbstractIntegration::METHOD_TYPE_POST,
                    'object',
                    $codigoProduto,
                    AbstractIntegration::OUTPUT_TYPE_XML
                );
            //$responseService = $integration->alterar($integracaoProduto, AbstractIntegration::METHOD_TYPE_POST,'object', $codigoProduto,AbstractIntegration::OUTPUT_TYPE_XML);
        endif;

        /** @var $response Integrations\Response\Response */
        if ($response->isSuccessful()) {
            $objIntegracaoProduto->setProduto($objProduto);
            $objIntegracaoProduto->setTipoIntegracao('ProdutoBling');
            $objIntegracaoProduto->setConcluidoComSucesso(1);
            $objIntegracaoProduto->setResult(serialize($response));
            $objIntegracaoProduto->setDataAtualizacao($dataIntegracao);
            $objIntegracaoProduto->save();
        } else {
            $objIntegracaoProduto->setProduto($objProduto);
            $objIntegracaoProduto->setTipoIntegracao('ProdutoBling');
            $objIntegracaoProduto->setConcluidoComSucesso(0);
            $objIntegracaoProduto->setResult(serialize($response->getError()));
            $objIntegracaoProduto->setDataAtualizacao($dataIntegracao);
            $objIntegracaoProduto->save();
        }
    } catch (\PDOException $pe) {
        echo 'Erro integração produto ' . $objProduto->getId() . ': ' . $pe->getMessage();
        $logger->error($pe->getMessage());
    } catch (\PropelException $pe) {
        echo 'Erro integração produto ' . $objProduto->getId() . ': ' . $pe->getMessage();
        $logger->error($pe->getMessage());
    } catch (Exception $e) {
        echo 'Erro integração produto ' . $objProduto->getId() . ': ' . $e->getMessage();
        $logger->error($e->getMessage());
        $objIntegracaoProduto->setProduto($objProduto);
        $objIntegracaoProduto->setTipoIntegracao('ProdutoBling');
        $objIntegracaoProduto->setConcluidoComSucesso(0);
        $objIntegracaoProduto->setResult(serialize(array($e->getMessage())));
        $objIntegracaoProduto->setDataAtualizacao($dataIntegracao);
        $objIntegracaoProduto->save();
    }
endforeach;

include_once __DIR__ . '/include/cron-stop.inc.php';
