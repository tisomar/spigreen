<?php

/**
 * Definição dos tamanhos das imagens
 */
switch (Config::get('produto.proporcao')) {

    case '1:1':

        $aspectRatio    = '1x1';
        $resizeThumb    = "width=150&height=150&cropratio=1.0:1";
        $resizeMdImage  = "width=600&height=600&cropratio=1.0:1";
        $resizeLgImage  = "width=1024&height=1024&cropratio=1.0:1";

        break;

    case '4:3':

        $aspectRatio    = '4x3';
        $resizeThumb    = "width=150&height=112&cropratio=1.333:1";
        $resizeMdImage  = "width=600&height=450&cropratio=1.333:1";
        $resizeLgImage  = "width=1024&height=768&cropratio=1.333:1";

        break;

    case '3:4':

        $aspectRatio    = '3x4';
        $resizeThumb    = "width=150&height=200&cropratio=0.75:1";
        $resizeMdImage  = "width=600&height=800&cropratio=0.75:1";
        $resizeLgImage  = "width=768&height=1024&cropratio=0.75:1";

        break;
}

$disableVerticalGallery = isset($disableVerticalGallery) && $disableVerticalGallery;

/**
 * Busca as fotos.
 */

/* @var $foto Foto */
$cor = !isset($cor) ? null : $cor;

/**
 * Verifica a variação padrão do produto, busca a opção cor e filtra as imagens somente daquela cor.
 */
if (is_null($cor) && Config::get('produto_variacao.selecao_automatica') != 0) {
    $cor = ProdutoVariacaoAtributoQuery::create()
        ->select(array('Descricao'))
        ->useProdutoAtributoQuery()
        ->filterByType(ProdutoAtributoPeer::TYPE_COR)
        ->endUse()
        ->filterByProdutoVariacaoId($objProduto->getVariacaoPadrao()->getId())
        ->findOne();

}

$collFotos = $objProduto->getFotosByCor($cor);
$photoswipe = array();

?>
    <div class="row">

        <?php
        if (Config::get('produto.proporcao') == '3:4' && $disableVerticalGallery == false)
        {
            /**
             * Thumb vertical:
             *  - Mostrar na proporção: 3x4 quando a resolução estiver em MD e LG.
             */
            ?>
            <div class="col-md-3 col-lg-3 hidden-xs hidden-sm">
                <div class="swiper-gallery-products swiper-container" data-gallery-id="<?php echo $objProduto->getId() ?>" style="min-height: 545px">
                    <div class="swiper-wrapper" data-content-id="swiper_item_<?php echo $objProduto->getId() ?>">
                        <?php foreach ($collFotos as $foto): ?>
                            <?php
                            \QPress\Template\Widget::render('gallery/swiper-item', array(
                                'foto' => $foto,
                            ));
                            ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <?php
        }

        /**
         * Imagem grande (zoom)
         */
        ?>
        <div class="col-xs-12 <?php echo Config::get('produto.proporcao') == '3:4' && $disableVerticalGallery == false ? 'col-md-9 col-lg-9' : '' ?>">
            <div class="gallery-product">
                <div data-content-id="owl_fotos_item_<?php echo $objProduto->getId() ?>" class="owl-fotos owl-carousel owl-theme gallery-photo-swipe aspect-ratio-<?php echo $aspectRatio ?>" data-pswp-uid="1" data-gallery-id="<?php echo $objProduto->getId() ?>">
                    <?php foreach ($collFotos as $i => $foto): ?>
                        <?php
                        \QPress\Template\Widget::render('gallery/owl-fotos-item', array(
                            'foto' => $foto,
                        ));
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="javascript:void(0);" class="openGalleryPhotoSwipe text-center center-block" data-gallery-id="<?php echo $objProduto->getId() ?>" data-pswp-classname=".gallery-photo-swipe">
                        <span class="fa fa-search"></span> Clique para ampliar
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php
/**
 * Thumbs horizontais
 * Em 1x1 e 4x3 qualquer dimensão
 * e em 3x4 somente em SM e XS
 */
?>
    <div class="row">
        <div class="col-xs-12 <?php echo Config::get('produto.proporcao') == '3:4' && $disableVerticalGallery == false ? 'hide' : 'hidden-xs hidden-sm' ?>">
            <div data-content-id="owl_miniaturas_item_<?php echo $objProduto->getId() ?>" class="owl-fotos-miniaturas owl-carousel owl-theme" data-gallery-id="<?php echo $objProduto->getId() ?>">
                <?php foreach ($collFotos as $i => $foto): ?>
                    <?php
                    \QPress\Template\Widget::render('gallery/owl-miniaturas-item', array(
                        'foto' => $foto,
                    ));
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php \QPress\Template\Widget::render("produto-detalhe/gallery-photo-swipe"); ?>