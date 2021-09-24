<?php
$porcentagemDesconto = isset($porcentagemDesconto) ? $porcentagemDesconto : 0;
?>

<div class="text-center">
    <div class="page-header">
        <?php if ($porcentagemDesconto > 0): ?>
            <del class="text-muted"><?php echo format_money($valorTotalSemDesconto, 'R$&nbsp;') ?></del>
        <?php endif; ?>
        <h2 class="text-success">
            <small class="text-success">R$&nbsp;</small><?php echo format_money($valorTotal); ?>
            <?php if ($porcentagemDesconto > 0): ?>
                <br><small class="text-success"><?php echo $porcentagemDesconto; ?>% de desconto</small>
            <?php endif; ?>
        </h2>
    </div>
</div>