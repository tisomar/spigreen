<?php
ini_set('max_execution_time', 600);

$oTabelaPreco = TabelaPrecoPeer::retrieveByPK($container->getRequest()->query->get('id'));
TabelaPrecoPeer::updateProdutoVariacao($oTabelaPreco);

$container->getSession()->getFlashBag()->set('success', 'Produtos atualizados com sucesso!');

redirect('/admin/tabela-preco/registration?id=' . $container->getRequest()->query->get('id'));
exit;
