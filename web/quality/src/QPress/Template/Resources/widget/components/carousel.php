<?php if ($collImages->count() > 0): ?>
    <div id="<?php echo $id; ?>" class="owl-carousel owl-theme carousel-default">
        <?php foreach ($collImages as $imagem): ?>
            <div class="item">
                <a href="<?php echo $imagem->getSrcImagem(); ?>" data-lightbox="photo" title="<?php echo htmlspecialchars($imagem->getLegenda()); ?>">
                    <img class="img-responsive center-block" alt="<?php echo htmlspecialchars($imagem->getLegenda()); ?>" src="<?php echo $imagem->getUrlImageResize('width=290&height=193&cropratio=1.5:1'); ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>