<form role="form" action="<?php echo get_url_site() . '/checkout/actions/cupom.actions' ?>" method="post" id="discount-coupon" class="form-disabled-on-load">
    <?php if (is_null($carrinho->getCupom())) : ?>
        <input type="hidden" name="action-cupom" value="add">
        <div class="box-secondary box-secondary-first text-left">
            <button type="button" data-toggle="collapse" data-target="#discount-coupon-form">
                <span class="<?php icon('ticket'); ?> pull-first"></span>
                Incluir vale presente e cupom
            </button>

            <div id="discount-coupon-form" class="collapse">
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="input-group input-group-sm">
                            <input type="hidden" name="action-cupom" value="add">
                            <input placeholder="Insira aqui o seu cupom de desconto." autocomplete="off" class="form-control" type="text" value="<?php echo $container->getRequest()->request->get('cupom_desconto') ?>" name="cupom_desconto" required>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit" title="Validar cupom">Validar</button>
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <input type="hidden" name="action-cupom" value="remove">
        <div class="box-secondary box-secondary-first bg-default">
            <span class="fa fa-ticket"></span> Cupom de desconto:
            <label class="label label-default"><?php echo $carrinho->getCupom()->getCupom() ?></label>
            <label class="label label-success"><?php echo $carrinho->getCupom()->getValorDescontoFormatado() ?></label>
            &minus;<small>R$&nbsp;</small><?php echo format_money($carrinho->getValorDescontoBy(CupomPeer::OM_CLASS)) ?>
            <a href="javascript:void(0);"  data-text="Você realmente deseja retirar este cupom da sua compra?" data-action="delete" data-type="submit" data-form="#discount-coupon">
                <span class="<?php icon('close'); ?>"></span>
                <span class="hidden-xs">remover cupom</span>
            </a>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
</form>
