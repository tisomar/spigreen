<?php
require __DIR__ . '/actions/extrato-bonus-frete.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-bonus-frete';

include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-bonus-frete">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Extrato de bônus frete' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Extrato de bônus frete'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Extrato de bônus frete.
                        </h3>
                    </div>
                </div>
                <br>
                <form action="<?php echo get_url_site() .
                    '/minha-conta/extrato-bonus-frete/' ?>" role="form" method="get" class="form-disabled-on-load">
                    <?php
                    Widget::render(
                        'forms/filtro-extratos',
                        [
                            'dtInicio' => $dtInicio,
                            'dtFim' => $dtFim
                        ]
                    );

                    ?>
                    <div class="col-xs-12" style="margin-bottom: 15px;">
                        <div class="row">
                            <small class="text-muted">
                                Obs.: Datas dos extratos são referentes ao horário de Brasília.
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                    </div>
                </form>

                <div class="row">
                    <div class="col-xs-12">
                        <?php if (count($pager) > 0) : ?>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span> Total de bônus
                                    <strong><?php echo number_format($totalbonusPeriodo, 2, ',', '.') ?></strong>.
                                </div>
                            </div>

                            <div class="table-vertical">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Bônus</th>
                                        <th class="text-center">Operação</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Observação</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    /**
                                     * @var $extrato Extrato
                                     */
                                    foreach ($pager as $extrato):  ?>
                                        <tr>
                                            <td class="text-center"><?= number_format($extrato->getPontos(), 2, ',', '.') ?></td>
                                            <td class="text-center"><?= $extrato->getOperacao() ?></td>
                                            <td class="text-center"><?= $extrato->getData('d/m/Y') ?></td>
                                            <td class="text-center"><?= $extrato->getObservacao() ?></td>
                                        </tr>
                                    <?php
                                    endforeach;
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                            Widget::render('components/pagination', array(
                                'pager' => $pager,
                                'href' => get_url_site() . '/minha-conta/extrato-bonus-frete/',
                                'queryString' => $queryString,
                                'align' => 'center'
                            ));
                            ?>

                        <?php else : ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <span class="<?php icon('info'); ?>"></span> Nenhum extrato encontrado!
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>