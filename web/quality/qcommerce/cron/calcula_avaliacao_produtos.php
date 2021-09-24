<?php

/**
 * Esta rotina calcula a média de avaliação total do produto com base nos comentários aprovados
 * pelo administrador da loja.
 *
 * Esta rotina pode ser executada 1x ao dia �s 0h. - * 0 0 * * * /path_to_script
 */

include __DIR__ . '/../includes/include_config.inc.php';

$collProdutos = ProdutoQuery::create()
    ->useProdutoComentarioQuery()
    ->filterByStatus('APROVADO')
    ->endUse()
    ->find();

foreach ($collProdutos as $objProduto) { /* @var $objProduto Produto */
    $objProduto->updateAvaliacao();
}
