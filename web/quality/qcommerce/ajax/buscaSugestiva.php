<?php
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_GET['term'])) {
    die();
}

$arguments = array(
    'ordenar-por' => $session->get('ordenar-por'),
);

$busca = str_replace(array("%", "_", "!"), "", escape($request->query->get('term')));

if ($busca == '') {
    $collProdutos = null;
} else {
    $criteria = ProdutoQuery::create()->filterByNome('%' . $busca . '%', Criteria::LIKE)->limit(5);
    $collProdutos = ProdutoPeer::findAll($criteria, $arguments);
}

foreach ($collProdutos as $objProduto) { /* @var $objProduto Produto */
    $arrResultado[] = array(
        'nome' => $objProduto->getNome(),
        'url' => $objProduto->getUrlDetalhes()
      );
}

echo json_encode($arrResultado);
