<?php

/**
 * Updates table integracao cliente.
 */

date_default_timezone_set("America/Sao_Paulo");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '2048M');

require_once __DIR__ . '/../includes/include_config.inc.php';
$cronFile = __FILE__;
include_once __DIR__ . '/include/cron-init.inc.php';

use Integrations\AbstractIntegration;
use Integrations\Response\Response;

/* @var $container \QPress\Container\Container */
/* @var $integration Integrations\Models\Bling\Bling */
$integration = $container->getIntegrationManager()->get('Bling');
$integration->setService('ClienteBling');

try {
    $arrClientes = ClienteQuery::create()
        ->leftJoinIntegracaoCliente()
        ->filterByTreeLeft(null, Criteria::NOT_EQUAL)
        ->joinEndereco()
        ->where('(qp1_cliente.updated_at > qp1_integracao_cliente.DATA_ATUALIZACAO OR qp1_integracao_cliente.CLIENTE_ID is null)')
        ->find();
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
}

foreach ($arrClientes as $objCliente) :
    try {
        /** @var $objCliente Cliente */
        $integracaoCliente = ClientePeer::formatterClienteIntegrationBling($objCliente);
        $codigoCliente = null;
        $objIntegracaoCliente = IntegracaoClienteQuery::create()
            ->filterByCliente($objCliente)
            ->filterByTipoIntegracao('ClienteBling')
            ->findOneOrCreate();

        if ($objIntegracaoCliente->isNew()) :
            $response = $integration
                ->gravar(
                    $integracaoCliente,
                    AbstractIntegration::METHOD_TYPE_POST,
                    'object',
                    AbstractIntegration::OUTPUT_TYPE_XML
                );
        else :
            $codigoCliente = $objCliente->getId();
            $response = $integration
                ->alterar(
                    $integracaoCliente,
                    AbstractIntegration::METHOD_TYPE_PUT,
                    'object',
                    $codigoCliente,
                    AbstractIntegration::OUTPUT_TYPE_XML
                );
        endif;

        /** @var $response Response */

        if ($response->isSuccessful()) :
            echo 'here';
            $objIntegracaoCliente->setCliente($objCliente);
            $objIntegracaoCliente->setTipoIntegracao('ClienteBling');
            $objIntegracaoCliente->setConcluidoComSucesso(1);
            $objIntegracaoCliente->setResult(serialize($response->getResult()));
            $objIntegracaoCliente->setDataAtualizacao(date('Y-m-d H:i:s'));
            $objIntegracaoCliente->save();
        else :
            echo 'here';
            $objIntegracaoCliente->setCliente($objCliente);
            $objIntegracaoCliente->setTipoIntegracao('ClienteBling');
            $objIntegracaoCliente->setConcluidoComSucesso(0);
            $objIntegracaoCliente->setResult(serialize($response->getError()));
            $objIntegracaoCliente->setDataAtualizacao(date('Y-m-d H:i:s'));
            $objIntegracaoCliente->save();
            echo 'test';

        endif;
    } catch (\PDOException $pe) {
        $logger->error($pe->getMessage());
    } catch (\PropelException $pe) {
        $logger->error($pe->getMessage());
    } catch (Exception $e) {
        $logger->error($e->getMessage());
        $objIntegracaoCliente->setCliente($objCliente);
        $objIntegracaoCliente->setTipoIntegracao('PedidoBling');
        $objIntegracaoCliente->setConcluidoComSucesso(0);
        $objIntegracaoCliente->setResult(serialize(array($e->getMessage())));
        $objIntegracaoCliente->setDataAtualizacao(date('Y-m-d H:i:s'));
        $objIntegracaoCliente->save();
    }
endforeach;

include_once __DIR__ . '/include/cron-stop.inc.php';
