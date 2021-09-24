<div class="box-secondary text-right box-price-total clearfix">
    <p class="text-right">
        <span class="price-label">Valor: </span>
        <span class="text-success price">
            <span>R$</span>
            <span><?php echo format_money($value) ?></span>
            <span>+ frete</span><br>
        </span>
    </p>

    <?php 
    $carrinho = $container->getCarrinhoProvider()->getCarrinho(); 
    // INFORMANDO O TOTAL DE PONTOS DA COMPRA
    $pontosKit = 0;
    foreach ($carrinho->getPedidoItems() as $item):
        $kit = in_array($item->getProdutoVariacao()->getProduto()->getId(), [2, 123]);
        if ($kit) :
            $valorPontos = $item->getProdutoVariacao()->getProduto()->getValorPontos();
            $qtdProdutos = $item->getQuantidade();
            $pontosKit += $qtdProdutos * $valorPontos;
        endif;
    endforeach;

    // $confereFrete = $totalPontos - $kit;
    ?>

    <p class="text-right">
        <span class="price-label">Pontos: </span>
        <span class="text-success price">
            <span>Esta compra renderá</span>
            <span> <?php echo $totalPontos + $pontosKit ?> </span>
            <span>pontos</span><br>
        </span>
    </p>
</div>
