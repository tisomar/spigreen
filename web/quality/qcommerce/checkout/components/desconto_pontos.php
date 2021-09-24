<?php $tipoDesconto = $carrinho->getCliente()->isClientePreferencial() ? 'pontos' : 'bônus' ?>

<form role="form" action="<?php echo get_url_site() . '/checkout/actions/desconto_pontos.actions' ?>" method="post" id="discount-points" class="form-disabled-on-load">
    <?php if ($carrinho->getDescontoPontos()) : ?>
        <input type="hidden" name="action" value="remove">
        <div class="box-secondary box-secondary-first bg-default">
            <span class="fa fa-ticket"></span> Utilização de <?php echo $tipoDesconto ?>:
            &minus;<small>R$&nbsp;</small><?php echo format_money($carrinho->getValorDescontoBy(DescontoPagamentoPontosPeer::OM_CLASS)) ?>
            <a href="javascript:void(0);"  data-text="Você realmente deseja não utilizar bônus como forma de pagamento?" data-action="delete" data-type="submit" data-form="#discount-points">
                <span class="<?php icon('close'); ?>"></span>
                <span class="hidden-xs">não utilizar <?php echo $tipoDesconto ?></span>
            </a>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
</form>
