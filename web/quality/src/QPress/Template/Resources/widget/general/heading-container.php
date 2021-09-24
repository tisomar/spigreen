<?php ob_start(); ?>

<h1 class="h4 tit col-xs-10 col-sm-8 col-md-9">
    <?php if (isset($icon)): ?>
        <span class="<?php icon($icon); ?>"></span>
    <?php endif; ?>
    <?php echo resumo($title, 30); ?>
</h1>
<a title="<?php echo $urlTitle; ?>" class="col-xs-2 col-sm-4 col-md-3 text-right" href="<?php echo $link; ?>">
    <span class="hidden-xs"><?php echo !isset($urlTitle) ? 'Ver mais produtos' : $urlTitle; ?></span>
    <span class="<?php icon('chevron-right'); ?>"></span>
</a>

<?php $content = ob_get_clean(); ?>


<div class="hidden-md hidden-lg heading-container">
    <div class="container">
        <div class="row">
            <?php echo $content; ?>
        </div>
    </div>
</div>

<div class="visible-md visible-lg">
    <div class="container">
        <div class="heading-container clearfix">
            <?php echo $content; ?>
        </div>
    </div>
</div>