<?php $installment = isset($installment) && $installment > 0 ? $installment : null; ?>

<div class="box-secondary text-left box-price-total clearfix">
    <p class="text-left" style="color: #d35400"> <?= $bonus_produtos ?></p>
</div>

<div class="box-secondary text-right box-price-total clearfix">
    <p class="text-right">
        <span class="price-label">Pontos: </span>
        <span class="text-success price">
            <span>Esta compra renderá</span>
            <span> <?php echo($total_pontos) ?></span>
            <span>pontos</span><br>
        </span>
    </p>
    <p class="text-right">
        <span class="price-label">Valor total</span>
        <span class="text-success price">
            <span>R$</span>
            <span><?php echo format_money($value) ?></span>
        </span>
    </p>

    <?php if ($strIncludesKey != 'minha-conta-pedido-detalhes'): ?>
        <p>em até <span class="text-success"><?php echo get_descricao_valor_parcelado($value); ?></span> sem juros</p>
    <?php endif; ?>
</div>