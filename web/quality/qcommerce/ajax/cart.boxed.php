<?php

use QPress\Template\Widget;

$count = $container->getCarrinhoProvider()->getCarrinho()->countItems();
if ($count > 0) : ?>
    <a href="<?php echo get_url_site(); ?>/carrinho/">
        <div class="row">
            <div class="shopping-cart-header col-md-12">
                <div class="col-md-12">
                    <i style="margin-top:5px;" class="fa fa-shopping-cart cart-icon"></i>
                    <h4 style="display: inline-block;padding-top: 4px;">Meu Carrinho</h4>
                </div>
            </div> <!--end shopping-cart-header -->
        </div>
    </a>
    <div class="shopping-cart-items scrollable">
        <div class="shopping-cart-items-view">
            <?php
            Widget::render('general/table-products', array(
                'itens' => $container->getCarrinhoProvider()->getCarrinho()->getPedidoItemsJoinProdutoVariacao()
            ));
            ?>


        </div>
    </div>
    <div class="shopping-cart-total">
        <div class="row" style="margin: 0 0">
            <div class="col-md-12 shopping-cart-total-span">
                <p>
                    Total:
                    R$ <?php echo format_money($container->getCarrinhoProvider()->getCarrinho()->getValorItens())?>
                </p>
            </div>
        </div>
        <div class="row clearfix" style="margin: 0 0">
            <div class="col-md-6" style="padding: 0 5px 0 0;">
                <a href="<?php echo get_url_site(); ?>/carrinho/" class="btn btn-block btn-default ">
                    Carrinho
                    <i class="fa fa-cart-arrow-down shopping-cart-btn-icons"></i>
                </a>
            </div>
            <div class="col-md-6" style="padding: 0 0 0 5px;">
                <a href="<?php echo get_url_site(); ?>/checkout/endereco/"
                   class="finalizar btn btn-clean btn-block btn-success">
                    Finalizar Compra
                    <i class="fa fa-lock shopping-cart-btn-icons"></i>
                </a>
            </div>
        </div>
    </div>

<?php else : ?>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center bloco">
            <h1 class="title">Ops! Seu carrinho est?? vazio.</h1>
        </div>
    </div>
<?php endif; ?>
