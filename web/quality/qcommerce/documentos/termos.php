<?php
$strIncludesKey = '';
$objConteudo = ConteudoPeer::get($container->getRequest()->query->all()['termo']);
include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox">
    <?php
    echo get_contents(
        __DIR__ . '/_template.php',
        array(
            'title' => $objConteudo->getNome(),
            'content' => $objConteudo->getDescricao()
        )
    );

    include_once __DIR__ . '/../includes/footer-lightbox.php'
    ?>
</body>
</html>
