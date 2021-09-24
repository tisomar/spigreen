<?php
$strIncludesKey = '';
include_once __DIR__ . '/../../includes/head.php';
require __DIR__ . '/actions/textos.actions.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox">
    <?php echo get_contents(
        __DIR__ . '/_template.php',
        array(
                'title' => $suporte->getTitulo(),
                'content' => $suporte->getDescricao())
    );
?>
    <?php include_once __DIR__ . '/../../includes/footer-lightbox.php' ?>
</body>
</html>
