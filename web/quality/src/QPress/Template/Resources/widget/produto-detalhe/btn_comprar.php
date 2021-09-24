<?php //if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>
<!--    <button id="buy-button---><?php //echo $objProduto->getId() ?><!--" type="submit" class="btn btn-success btn-block">-->
<!--        Comprar-->
<!--    </button>-->
<?php //endif; ?>
<?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>
    <!--    <button id="buy-button---><?php //echo $objProduto->getId() ?><!--" type="submit" class="btn btn-success btn-block">-->
    <!--        Comprar-->
    <!--    </button>-->
    <button
        class="add-to-cart btn btn-success btn-block"
        data-product="<?php echo $objProduto->getId() ?>"
        data-product-variation="<?php echo $objProduto->getProdutoVariacao()->getId() ?>"
    >
        Comprar
    </button>
<?php endif; ?>
