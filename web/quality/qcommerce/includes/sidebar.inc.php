<?php
$sidebarCategoriesHTML = CategoriaPeer::getHtmlTree();
$collBannerLateral = BannerQuery::findBannerByType(BannerPeer::LATERAL, 5);
?>

<div class="container container-quaternary">
    <h3 class="title clear-margin">Categorias</h3>
</div>
<aside>
    <nav class="nav-aside">
        <?php echo $sidebarCategoriesHTML; ?>
    </nav>

    <?php
    foreach ($collBannerLateral as $objBannerLateral) { /* @var $objBannerLateral Banner */
        echo $objBannerLateral->getThumbLink("width=230", array('class' => 'thumb-top'));
    }
    ?>
</aside>
