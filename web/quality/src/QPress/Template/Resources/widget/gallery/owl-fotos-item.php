<?php
include __DIR__ . '/_config.php';

?>
<?php $title = is_null($foto->getLegenda()) || trim($foto->getLegenda()) == "" ? $foto->getProduto()->getNome() : $foto->getLegenda(); ?>
<?php
$param = array(
    'search' => array(
        $container->getRequest()->getSchemeAndHttpHost(),
        $container->getRequest()->getBasePath(),
        $container->getRequest()->getBaseUrl(),
    ),
    'replace' => ''
);
$url = str_replace($param['search'], $param['replace'], $foto->getUrlImageResize($resizeLgImage));
$fullUrl = $container->getRequest()->getSchemeAndHttpHost() . $container->getRequest()->getBasePath() . $url;
//list($width, $height) = getimagesize($fullUrl);
//list($width, $height) = array(700, 700);
$dataSize = sprintf("%sx%s", 700, 700);
?>

<div class="item text-center">
    <div class="easyzoom easyzoom--overlay">
        <a data-size="<?php echo $dataSize ?>" href="<?php echo $foto->getUrlImageResize($resizeLgImage); ?>">
            <img src="<?php echo $foto->getUrlImageResize($resizeMdImage) ?>" title="<?php echo $title ?>" alt="<?php echo $title ?>" class="img-responsive">
        </a>
    </div>
</div>