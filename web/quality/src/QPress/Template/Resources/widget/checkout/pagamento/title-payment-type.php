<div class="panel-heading">
    <h4 class="panel-title <?php echo !$isOpenedPanel ? 'collapsed' : '' ?>" data-toggle="collapse" href="#<?php echo $paymentTypeId ?>">
        <div class="row">
            <div class="col-xs-8">
                <i class="fa fa-chevron-down"></i><i class="fa fa-chevron-up"></i>&nbsp;<?php echo $paymentTypeName ?>
            </div>
            <div class="col-xs-4 text-right">
                <?php echo format_money($paymentTypeValue, 'R$&nbsp;') ?>
            </div>
        </div>
    </h4>
</div>