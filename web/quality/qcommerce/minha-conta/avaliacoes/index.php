<?php
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-avaliacoes';
include QCOMMERCE_DIR . "/includes/security.php";
include __DIR__ . '/actions/index.actions.php';
include QCOMMERCE_DIR . "/includes/head.php";
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-avaliacoes">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha conta' => '/minha-conta/pedidos', 'Minhas Avaliações' => '')));
    Widget::render('general/page-header', array('title' => 'Minhas Avaliações'));
    Widget::render('components/flash-messages');
    ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h3>Acompanhe aqui suas avaliações.</h3>

                <?php $urlDelete = get_url_site() . '/minha-conta/avaliacoes/?remove-avaliacao=%d'; ?>

                <?php if ($arrComentarios->count()) : ?>
                    <?php foreach ($arrComentarios as $key => $objComentario) : /* @var $objComentario ProdutoComentario */ ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="pull-left">
                                    <span class="pull-left">
                                        <?php
                                        Widget::render('components/rating', array(
                                            'name'      =>  'avaliacao[NOTA]',
                                            'size'      =>  'xs',
                                            'value'     =>  $objComentario->getNota(),
                                            'disabled'  =>  true
                                        ));
                                        ?>
                                    </span>
                                    <span class="pull-left"><?php echo resumo($objComentario->getTitulo(), 40) ?></span>
                                </h4>
                                <div class="pull-right">
                                    <a data-action="delete" href="<?php echo sprintf($urlDelete, $objComentario->getId()) ?>" class="btn btn-danger btn-sm">
                                        <span class="<?php icon('times') ?>"></span> <span class="hidden-xs">Deletar comentário</span>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <time class="text-right" datetime="<?php echo $objComentario->getData('Y-m-d'); ?>">
                                    Em <?php echo $objComentario->getData('d/m/Y à\s H\hi'); ?> você comentou:
                                </time>
                                <blockquote><?php echo nl2br($objComentario->getDescricao()) ?></blockquote>
                                <b>Produto avaliado:</b>
                                <a class="h4" href="<?php echo $objComentario->getProduto()->getUrlDetalhes(); ?>" title="<?php echo escape($objComentario->getProduto()->getNome()); ?>">
                                    <?php echo escape(resumo($objComentario->getProduto()->getNome(), 25)); ?>
                                </a>
                                <?php if ($objComentario->getStatus() == ProdutoComentario::STATUS_PENDENTE) : ?>
                                    <?php
                                    Widget::render('components/alert', array(
                                        'type' => 'warning',
                                        'message' => 'Este comentário está sendo analisado, ele aparecerá no site assim que for aprovado por nossos administradores.'
                                    ));
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php
                    Widget::render('components/pagination', array(
                        'pager' => $arrComentarios,
                        'href'  => get_url_site() . '/minha-conta/avaliacoes/',
                        'align' => 'center'
                    ));
                    ?>
                <?php else : ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span> Você não efetuou nenhum comentário sobre os produtos até este momento.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>
</html>
