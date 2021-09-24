<div class="col-xs-6">
    <?php
    \QPress\Template\Widget::render('produto_variacao/input_quantidade', array(
        'objProdutoVariacao'    => $objProdutoVariacao,
        'inputType'             => 'number'
    ));
    ?>
</div>
<div class="col-xs-6">
    <div class="security-seal">
        <img src="<?php echo asset('/img/min/security.png') ?>" alt="Compra 100% segura" class="img-responsive">
    </div>
</div>
<script id="#__initTouchSpin">$(function() { initTouchSpin(); $('#__initTouchSpin').remove(); });</script>