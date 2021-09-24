<?php 
$total = count($links);
?>
<ol <?php echo $attributes ?>>
    <?php foreach ($links as $pos => $link) : ?>
        <li<?php if ($total == $pos+1) : ?> class="active"<?php endif; ?>>
            <?php if ($link['url'] != null) : ?>
                <a href="<?php echo $link['url'] ?>"><?php echo $link['title']; ?></a>
            <?php else : ?>
                <?php echo $link['title'] ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ol>