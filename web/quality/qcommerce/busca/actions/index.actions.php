<?php
$strIncludesKey = 'busca';

$url = get_url_site() . '/busca/index/';

// Define a página dos produtos
$page = isset($args[0]) && is_numeric($args[0]) && $args[0] > 0 ? $args[0] : 1;

$busca = $request->query->get('buscar-por');
$ordenacao = $request->query->get('ordenar-por');

$arguments = array(
    'ordenar-por'   => $ordenacao,
    'max-per-page'  => $session->get('produtos-por-pagina'),
    'page'          => $page
);

$busca = str_replace(array("%", "_", "!"), "", escape($busca));

$criteria = ProdutoQuery::create()->search($busca);
$collProdutos = ProdutoPeer::findAll($criteria, $arguments);
