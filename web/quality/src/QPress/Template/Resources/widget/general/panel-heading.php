<div class="panel-product">
    <div class="panel">
        <div class="panel-heading collapsed" data-toggle="collapse" data-target="<?php echo $dataTarget; ?>">
            <h2 class="panel-title">
                <span class="icons-container pull-left">
                    <span class="<?php icon($icon); ?>"></span>
                </span>
                <span class="visible-xs-inline"><?php echo (isset($panelTitleMobile) && $panelTitleMobile != null) ? $panelTitleMobile : '...'; ?></span>
                <span class="hidden-xs"><?php echo (isset($panelTitleDesktop) && $panelTitleDesktop != null) ? $panelTitleDesktop : '...'; ?></span>

                <span class="pull-last pull-right <?php icon('chevron-up'); ?> visible-xs-inline"></span>
                <span class="pull-last pull-right <?php icon('chevron-down'); ?> visible-xs-inline"></span>
            </h2>
        </div>
    </div>
</div>