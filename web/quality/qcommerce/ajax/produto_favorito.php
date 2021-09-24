<?php

include_once '../includes/include_propel.inc.php';
header('Content-Type: text/html; charset=UTF-8');

if (isset($_POST)) {
    $produtoId = filter_var(trim($_GET['produtoId']), FILTER_SANITIZE_STRING);

    $objProduto = ProdutoPeer::retrieveByPK($produtoId);

    if ($objProduto instanceof Produto) {
        if (ClientePeer::isAuthenticad()) {
            $cliente = ClientePeer::getClienteLogado();

            $objFavorito = ProdutoFavoritoQuery::create()->add(ProdutoFavoritoPeer::CLIENTE_ID, $cliente->getId())->add(ProdutoFavoritoPeer::PRODUTO_ID, $objProduto->getId())->findOne();
            
            if (!$objFavorito) {
                $favorito = new ProdutoFavorito();
                $favorito->setdata(date('Y-m-d'));
                $favorito->setProdutoId($objProduto->getId());
                $favorito->setClienteId($cliente->getId());
                if ($favorito->validate()) {
                    $favorito->save();
                    echo 'Produto adicionado com sucesso!';
                } else {
                    echo 'Não foi possível adicionar! Tente novamente!';
                }
            } else {
                echo 'Este produto já está em seus favoritos';
            }
        } else {
            echo 'Você precisa estar logado para adicionar aos seus favoritos!';
        }
    } else {
        echo 'Produto não encontrado!';
    }
} else {
    redirectTo('/pagina-nao-encontrada/');
    exit;
}
