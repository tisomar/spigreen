<?php
/* @var $container \QPress\Container\Container */
$container->getCarrinhoProvider()->getCarrinho()->getPedidoItems()->delete();
redirect('/carrinho');
exit;
