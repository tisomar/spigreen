<?php

$pageTitle = 'Carrinhos Abandonados';
$_class = PedidoPeer::OM_CLASS;

$preQuery = PedidoQuery::create()
        ->filterByClassKey(2)
        ->filterByValorItens(0, Criteria::GREATER_THAN)
        ->filterByClienteId(null, Criteria::NOT_EQUAL)
        ->filterByUpdatedAt(array('max' => strtotime('-' . PedidoPeer::CONFIGURACAO_HORAS_CARRINHO_ABANDONADO . ' hours')))
        ->join('Pedido.PedidoItem')
        ->join('PedidoItem.ProdutoVariacao')
        ->join('ProdutoVariacao.Produto')
        ->groupById()
        ->orderByCreatedAt(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
