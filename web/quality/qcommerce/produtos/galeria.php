<?php
/* @var $objComentario ProdutoComentario */
/* @var $objCliente Cliente */

use QPress\Template\Widget;
$strIncludesKey = 'produto-detalhes-galeria';
$foto_id = $container->getRequest()->query->get('p');
$foto = FotoPeer::retrieveByPK($foto_id);
$outrasFotos = $foto->getProduto()->getFotos();

if (Config::get('produto.proporcao') == '1:1') :
    $aspectRatio = '1:1';
    $img = 'width=400&height=400&cropratio=1:1';
    $imgZoom = 'width=1024&height=1024&cropratio=1:1';
    $thumb = 'width=80&height=80&cropratio=1:1';
elseif (Config::get('produto.proporcao') == '4:3') :
    $aspectRatio = '4:3';
    $img = 'width=400&height=300&cropratio=4:3';
    $imgZoom = 'width=1024&height=768&cropratio=1.33:1';
    $thumb = 'width=80&height=60&cropratio=4:3';
elseif (Config::get('produto.proporcao') == '3:4') :
    $aspectRatio = '3:4';
    $img = 'width=555&height=740&cropratio=3:4';
    $imgZoom = 'width=1536&height=2048&cropratio=0.75:1';
    $thumb = 'width=60&height=80&cropratio=3:4';
endif;

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey ?>">
<header>
</header>

<main role="main">
    <div class="container">

        <div id="image-product" data-aspect-ratio="<?php echo $aspectRatio; ?>">
            <img class="cloudzoom img-responsive center-block" src="<?php echo $foto->getUrlImageResize($img); ?>"
                 data-cloudzoom="
                    zoomImage:' <?php echo $foto->getUrlImageResize('width=768&height=1024&cropratio=0.75:1'); ?>',
                    captionSource:'none',
                    zoomPosition: 'inside',
                    zoomOffsetX: 0,
                    zoomOffsetY: 0,
                    lazyLoadZoom: true
                ">
        </div>

        <?php if ($outrasFotos->count() > 1) : ?>
            <div id="gallery-product">
                <ul class="list-unstyled clearfix">
                    <?php foreach ($outrasFotos as $_foto) : /* @var $foto Foto */  ?>
                        <li>
                            <picture>
                                <img class="cloudzoom-gallery img-responsive" src="<?php echo $_foto->getUrlImageResize($thumb); ?>"
                                    data-cloudzoom="useZoom:'.cloudzoom',
                                        image: '<?php echo $_foto->getUrlImageResize($imgZoom); ?>'
                                ">
                            </picture>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif;  ?>

        <p class="small visible-xs"> Arraste o dedo na imagem para ampliar!</p>
        <p class="small hidden-xs"> Passe o mouse sobre a imagem para ampliar!</p>
    </div>

</main>


<?php include_once QCOMMERCE_DIR . '/includes/footer-lightbox.php' ?>

</body>
</html> 
