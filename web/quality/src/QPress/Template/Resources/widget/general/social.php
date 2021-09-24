<nav class="social">
    <?php foreach (RedePeer::getAtivos() as $rede) : /* @var $rede Rede */ ?>
        <a target="_blank" href="<?php echo $rede->getLink(); ?>" title="<?php echo htmlspecialchars($rede->getNome()); ?>" data-midia="<?php echo $rede->getIcon(); ?>">
            <span class="<?php echo icon($rede->getIcon()); ?>"></span>
        </a>
    <?php endforeach; ?>
</nav>