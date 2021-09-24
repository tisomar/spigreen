<?php
header('Content-Type: application/json');

$arguments = array(
    'ordenar-por' => $session->get('ordenar-por'),
    'max-per-page' => 6
);

$busca = str_replace(array("%", "_", "!"), "", escape($request->query->get('term')));

if ($busca == '') {
    $collProdutos = null;
} else {
    #$criteria = ProdutoQuery::create()->filterByNome('%' . $busca . '%', Criteria::LIKE);
    $criteria = ProdutoQuery::create()->search($busca);
    $collProdutos = ProdutoPeer::findAll($criteria, $arguments);
}

$responseProduto = array(array(
    'name'  => 'Nenhum produto encontrado',
    'url'   => 'javascript:void(0);',
    'price' => '',
    'image' => '',
));

if (!is_null($collProdutos) && $collProdutos->count() > 0) {
    foreach ($collProdutos as $i => $objProduto) { /* @var $objProduto Produto */
        $responseProduto[$i]['name']    = resumo($objProduto->getNome(), 90, '');
        $responseProduto[$i]['url']     = $objProduto->getUrlDetalhes();
        $responseProduto[$i]['image']   = $objProduto->getImagemPrincipal()->getThumb('width=70&height=70&cropratio=1:1', array('class' => 'img-responsive'));
        if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0) {
            $responseProduto[$i]['price']   = 'R$&nbsp;' . format_money($objProduto->getProdutoVariacao()->getValor());
        } else {
            $responseProduto[$i]['price']   = '';
        }
    }
} else {
}

echo json_encode($responseProduto);
