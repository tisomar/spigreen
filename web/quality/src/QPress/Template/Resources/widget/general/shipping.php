<?php
$isEditable = isset($isEditable) && $isEditable;
$shippingName = isset($shippingName) ? $shippingName : false;
$prazo = isset($prazo) ? $prazo : false;
?>
<div class="box-secondary box-secondary-first bg-default box-shipping">
    <div class="row">
        <div class="col-xs-8">
            <?php if ($isEditable): ?>
                <a href="<?php echo get_url_site() ?>/checkout/frete" class="" title="Alterar forma de entrega">
                    <span class="small <?php icon('edit'); ?>" title="Editar"></span>
                </a>
            <?php endif; ?>
            Frete: <?php echo $shippingName ?: ''; ?>
            
            <br><br>
            
            <?php if( $estoqueRetirada != '') : ?>
                <small style="color: #d35400;">
                    <?php echo $estoqueRetirada ?>
                </small>

            <?php else : ?>
                <small style="color: #d35400;">
                    <?php echo $prazo ?>
                </small>
            <?php endif ?>

            <?php echo $codigoRastreio ?>
        </div>
        <div class="col-xs-4 text-right">
            <small>&plus; R$&nbsp;</small><?php echo format_money($value) ?>
        </div>
    </div>
</div>