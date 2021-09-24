<?php
$con = Propel::getConnection();


/**
 *  Contabiliza a quantiade de itens vendidos por variação
 */
$con->beginTransaction();

try {
    $pedidoItem = PedidoItemQuery::create()

        ->select(array('ProdutoVariacaoId', 'Id', 'QuantidadeVendida'))
        ->filterByEstatisticaProdutoVariacao(0)
        ->withColumn('SUM(QUANTIDADE)', 'QuantidadeVendida')

        ->usePedidoQuery()
        ->filterByStatus(PedidoPeer::STATUS_FINALIZADO)
        ->endUse()

        ->groupByProdutoVariacaoId()
        ->find()
        ->toArray();

    foreach ($pedidoItem as $item) {
        $estatistica = EstatisticaVendaProdutoVariacaoQuery::create()
            ->filterByProdutoVariacaoId($item['ProdutoVariacaoId'])
            ->findOneOrCreate($con);

        $quantidadeTotal = $estatistica->getQuantidadeVendida() + $item['QuantidadeVendida'];
        $estatistica->setQuantidadeVendida($quantidadeTotal);
        $estatistica->save($con);
    }

    PedidoItemQuery::create()
        ->filterById(array_column($pedidoItem, 'Id'))
        ->update(array('EstatisticaProdutoVariacao' => 1));

    $con->commit();
} catch (Exception $e) {
    $con->rollBack();

    $conteudo = array(
        'Site: ' . $container->getRequest()->getHttpHost(),
        'Problema: ' . $e->getMessage()
    );

    \QPress\Mailing\Mailing::send('rafael.cordeiro@qualitypress.com.br', 'CRON NÃO EXECUTADA: contabilizar_quantidade_produtos_vendidos (E1)', implode('<br>', $conteudo));
}


/**
 *  Contabiliza a quantidade de itens vendidos por produto
 */
$con->beginTransaction();

try {
    $produtos = EstatisticaVendaProdutoVariacaoQuery::create()
        ->useProdutoVariacaoQuery()
        ->groupByProdutoId()
        ->endUse()
        ->withColumn('SUM(QUANTIDADE_VENDIDA)', 'QuantidadeVendida')
        ->withColumn('PRODUTO_ID', 'ProdutoId')
        ->select(array('QuantidadeVendida', 'ProdutoId'))
        ->find();

    foreach ($produtos as $produto) {
        $produtoVendaEstatistica = ProdutoVendaEstatisticaQuery::create()
            ->filterByProdutoId($produto['ProdutoId'])
            ->findOneOrCreate($con);

        $produtoVendaEstatistica->setQuantidadeVendida($produto['QuantidadeVendida']);
        $produtoVendaEstatistica->save($con);
    }

    $con->commit();
} catch (Exception $e) {
    $con->rollBack();

    $conteudo = array(
        'Site: ' . $container->getRequest()->getHttpHost(),
        'Problema: ' . $e->getMessage()
    );

    \QPress\Mailing\Mailing::send('rafael.cordeiro@qualitypress.com.br', 'CRON NÃO EXECUTADA: contabilizar_quantidade_produtos_vendidos (E2)', implode('<br>', $conteudo));
}
