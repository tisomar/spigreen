<?php
if (isset($_GET['reference'])) {
    $objProduto = ProdutoQuery::create()->findOneById($_GET['reference']);
    if ($objProduto instanceof BaseProduto) { /* @var $objProduto Produto */
        $objProduto->redefinirAtributos();
        $container->getSession()->getFlashBag()->set('success', 'Atributos e variações zerados com sucesso.');
    }
}


header('HTTP/1.1 200 OK');
redirectTo(get_url_admin() . '/produto-atributos/list/?context=Produto&reference=' . $_GET['reference']);
exit;
