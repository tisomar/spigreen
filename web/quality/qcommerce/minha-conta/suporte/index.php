<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-suporte';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/suporte.actions.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-meu-plano">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Suporte' => '')));
    Widget::render('general/page-header', array('title' => 'Suporte'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>

            <?php if ($textoIntrodutorio) : ?>
                <div class="col-xs-12 col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php echo $textoIntrodutorio ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div class="row">
            <?php if (count($suportes) > 0) : ?>
                <div class="col-xs-12 col-md-9 col-md-offset-3">
                    <?php foreach ($suportes as $tipo => $suportesTipo) : ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4><?php echo escape($suportesTipo[0]->getTipoDesc()) ?></h4>
                            </div>
                            <div class="panel-body">
                                <ul class="panel-group filter-list list-unstyled" id="suporte-<?php echo $tipo ?>">
                                    <?php foreach ($suportesTipo as $suporte) : /* @var $suporte Suporte */ ?>
                                        <li class="panel panel-default">
                                            <div class="panel-heading collapsed" data-toggle="collapse"
                                                 data-parent="#suporte-<?php echo $tipo ?>"
                                                 data-target="#<?php echo $suporte->getId() ?>">
                                                <div class="row">
                                                    <div class="col-xs-10">
                                                        <h3 class="panel-title name">
                                                            <?php echo escape($suporte->getTitulo()) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-xs-2">
                                                        <span class="pull-right <?php icon('chevron-down'); ?>"></span>
                                                        <span class="pull-right <?php icon('chevron-up'); ?>"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="<?php echo $suporte->getId() ?>" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <?php echo nl2br(escape($suporte->getDescricaoResumida())) ?>
                                                    <br><br>
                                                    <?php if (Suporte::TIPO_TEXTO === $tipo) : ?>
                                                        <a href="<?php echo get_url_site() . '/minha-conta/suporte/textos/' . $suporte->getId() ?>"
                                                           data-lightbox="iframe"
                                                           title="<?php echo escape($suporte->getTitulo()) ?>">Leia o
                                                            texto completo...</a>
                                                    <?php elseif (Suporte::TIPO_VIDEO === $tipo || Suporte::TIPO_VIDEO_AULA === $tipo) : ?>
                                                        <a href="<?php echo $suporte->getVideo() ?>"
                                                           data-lightbox="iframe"
                                                           title="<?php echo escape($suporte->getTitulo()) ?>">Assista
                                                            ao vídeo...</a>
                                                    <?php elseif (!empty($suporte->getLinkArquivoS3())) : ?>
                                                        <a href="<?php echo $suporte->getLinkArquivoS3() ?>"
                                                           target="_blank">
                                                            Acesso o arquivo...
                                                        </a>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>
