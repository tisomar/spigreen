<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-8 col-lg-9">
        <a href="<?php echo get_url_site(). '/produtos/produtos/'?>"  class="btn btn-default btn-block visible-xs">Continuar comprando</a>
        <a href="<?php echo get_url_site(). '/produtos/produtos/'?>" class="btn btn-default hidden-xs">Continuar comprando</a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        <?php if ($container->getCarrinhoProvider()->getCarrinho()->countItems() > 0): ?>
            <a href="<?php echo get_url_site(); ?>/checkout/endereco" class="btn btn-success btn-block form-disabled-on-load">
                <i class="fa fa-lock"></i> Avan&ccedil;ar<i class="fa fa-angle-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
