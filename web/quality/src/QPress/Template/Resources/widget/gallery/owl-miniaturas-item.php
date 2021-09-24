<?php
include __DIR__ . '/_config.php';
?>
<?php $title = is_null($foto->getLegenda()) || trim($foto->getLegenda()) == "" ? $foto->getProduto()->getNome() : $foto->getLegenda(); ?>
<div class="item text-center">
    <a href="javascript:void(0);">
        <img src="<?php echo $foto->getUrlImageResize($resizeThumb) ?>" title="<?php echo $title ?>" alt="<?php echo $title ?>" class="img-responsive">
    </a>
</div>