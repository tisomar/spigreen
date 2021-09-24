<?php

$getTaxa = false;

if ($request->query->get('context') == ProdutoPeer::OM_CLASS
    && $request->query->get('reference') == ProdutoPeer::PRODUTO_TAXA_ID) {
    $getTaxa = true;
}

\QPress\Template\Widget::render(QCOMMERCE_DIR . '/admin/_2015/widget/menu.produtos.php', array(
    'context' => $container->getRequest()->query->get('context'),
    'reference' => $container->getRequest()->query->get('reference'),
    'module' => $router->getModule(),
    'getProdutoTaxa' => $getTaxa,
));
