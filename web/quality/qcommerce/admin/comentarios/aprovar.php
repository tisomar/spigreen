<?php

include __DIR__ . '/config/routes.php';

$objProdutoComentario = ProdutoComentarioPeer::retrieveByPK($_GET['id']);
$objProdutoComentario->setStatus(ProdutoComentario::STATUS_APROVADO);
$objProdutoComentario->save();
$objProdutoComentario->getProduto()->updateAvaliacao();

$session->getFlashBag()->add('success', 'Comentário aprovado com sucesso!');

redirectTo($config['routes']['list']);
exit;
