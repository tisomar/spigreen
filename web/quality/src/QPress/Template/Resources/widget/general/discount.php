<?php if ($value > 0): ?>
    <div class="box-secondary box-secondary-first box-discount bg-default">
        <p class="clearfix">
            <span class="pull-left">Total de descontos</span>
            <span class="pull-right"><small>&minus; R$&nbsp;</small><?php echo format_money($value) ?></span>
        </p>
    </div>
<?php endif; ?>