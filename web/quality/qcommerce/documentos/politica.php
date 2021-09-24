<?php
$strIncludesKey = '';
$objConteudo = ConteudoPeer::get('pg_politica_de_privacidade');

include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox">
    <?php echo get_contents(
        __DIR__ . '/_template.php',
        array(
                'title' => $objConteudo->getNome(),
                'content' => $objConteudo->getDescricao())
    );
?>
    <?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>
</body>
</html>
