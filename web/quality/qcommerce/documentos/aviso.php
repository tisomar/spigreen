<?php
$strIncludesKey = '';
$objConteudo = ConteudoPeer::get('pg_seguranca');
include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox">
    <?php echo get_contents(
        __DIR__ . '/_template.php',
        array(
                'title' => $_SESSION['MODAL_AVISO_TITULO'],
                'content' => $_SESSION['MODAL_AVISO'])
    );
?>
    <?php include_once __DIR__ . '/../includes/footer-lightbox.php';
    unset($_SESSION['MODAL_AVISO_TITULO']);
    unset($_SESSION['MODAL_AVISO']);
    ?>
</body>
</html>
