<?php
use QPress\Template\Widget;
$strIncludesKey = 'perguntas-frequentes';
include_once __DIR__ . '/actions/index.action.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/AboutPage" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Perguntas Frequentes' => '')));
    Widget::render('general/page-header', array('title' => 'Perguntas Frequentes'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <form role="form" method="get" action="#">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control search" placeholder="Digite sua dúvida..." name="filter" value="<?php echo $busca ?>" autocomplete="off">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary" title="Buscar">
                                <span class="<?php icon('search') ?>"></span>
                            </button>
                            <?php if ($busca != '') : ?>
                                <a href="<?php echo get_url_site() ?>/perguntas-frequentes" class="btn btn-danger" title="Limpar">
                                    <span class="<?php icon('remove') ?>"></span>
                                </a>
                            <?php endif; ?>
                        </span>
                </div>
            </div>
            <hr>
        </form>

        <?php if ($collFaq->count()) : ?>
            <div id="list-faq">
                <ul class="panel-group filter-list list-unstyled" id="faq-questions">

                    <?php foreach ($collFaq as $objFaq) : /* @var $objFaq Faq */ ?>
                        <li class="panel panel-default">
                            <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#faq-questions" data-target="#<?php echo $objFaq->getId() ?>">
                                <div class="row">
                                    <div class="col-xs-10">
                                        <h3 class="panel-title name">
                                            <?php echo $objFaq->getPergunta() ?>
                                        </h3>
                                    </div>
                                    <div class="col-xs-2">
                                        <span class="pull-right <?php icon('chevron-down'); ?>"></span>
                                        <span class="pull-right <?php icon('chevron-up'); ?>"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="<?php echo $objFaq->getId() ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php echo nl2br($objFaq->getResposta()) ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <ul class="pagination"></ul>
            </div>
        <?php elseif ($busca != '') : ?>
            <h2 class="text-cnter h3">Sua pesquisa não retornou resultados.</h2>
            <ul>
                <li>Verifique se não há erro de digitação.</li>
                <li>Tente utilizar uma única palavra.</li>
                <li>Tente buscar por termos menos espefíficos e posteriormente use os filtros da busca.</li>
                <li>Procure utilizar sinônimos do termo desejado.</li>
            </ul>
            <br>
        <?php else : ?>
            <h2 class="text-cnter h3">Nenhuma dúvida foi cadastrada até o momento.</h2>
            <br>
        <?php endif; ?>
    </div>

    <?php
    Widget::render('components/pagination', array(
        'pager' => $collFaq,
        'href'  => get_url_site() . '/perguntas-frequentes/',
        'align' => 'center'
    ));
    ?>

    <div class="bg-default">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <?php Widget::render('forms/enviar-pergunta', array('container' => $container)); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

<script type="text/javascript">$(function(){$("#faq-questions").highlight(<?php echo json_encode($termos) ?>)});</script>
</body>
</html>
