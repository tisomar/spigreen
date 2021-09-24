<?php
$count = $container->getCarrinhoProvider()->getCarrinho()->countQuantidadeTotal();
echo plural($count, '%s item', '%s itens');
