<?php
/**
 * Esta rotina envia um e-mail aos clientes que se cadastraram no produto para
 * receber um alerta quando o produto ficar disponível.
 *
 * Esta rotina pode ser executada 3x ao dia, indiferente do horário.
 */
include __DIR__ . '/../includes/include_config.inc.php';

$collProdutoInteresse = ProdutoInteresseQuery::create()
        ->filterByEnviarAviso(true)
        ->find();

if (count($collProdutoInteresse) > 0) {
    /* @var $objProdutoInteresse ProdutoInteresse */
    foreach ($collProdutoInteresse as $objProdutoInteresse) {
        \QPress\Mailing\Mailing::enviarAvisoProdutoInteresse($objProdutoInteresse->getClienteNome(), $objProdutoInteresse->getClienteEmail(), $objProdutoInteresse->getProdutoVariacaoId());
        $objProdutoInteresse->delete();
    }
}
