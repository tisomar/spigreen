<?php
require __DIR__ . '/actions/extrato-pontos.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-pontos-recompra';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-pontos-recompra">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Bônus de Equipe Produtividade' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Bônus de Equipe Produtividade'));
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
                            Bônus de equipe produtividade.
                        </h3>
                    </div>
                </div>
                <br>
                <form action="<?php echo get_url_site() .
                    '/minha-conta/extrato-pontos-recompra/' ?>" role="form" method="get" class="form-disabled-on-load">
                    <?php Widget::render(
                        'forms/filtro-extratos',
                        array('dtInicio' => $dtInicio, 'dtFim' => $dtFim)
                    ); ?>
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
                                    <span class="<?php icon('info'); ?>"></span> Total de bônus no período R$
                                    <strong><?php echo formata_pontos($totalPontosPeriodo) ?></strong>.
                                </div>
                            </div>

                            <div class="table-vertical">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <!-- <th class="text-center">Pontos</th> -->
                                        <th class="text-center" nowrap>Bônus (R$)</th>
                                        <th class="text-center">Operação</th>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th class="text-center" nowrap>Perc. (%)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($pager as $extrato) : /** @var $extrato Extrato */
                                    $totalPontos = 0;
                                    $nivel = 0;

                                    if ($extrato->getPedido()) :
                                        $totalPontos = $extrato->getPedido()->getTotalPontosProdutos(Extrato::TIPO_RESIDUAL);

                                        $nivel = $extrato->getPedido()->getCliente()->getTreeLevel() - $extrato->getCliente()->getTreeLevel();

                                        if ($nivel > 0) :
                                            $nivel = " ({$nivel}º nível)";
                                        else:
                                            $nivel = '';
                                        endif;
                                    endif;
                                    ?>
                                        <tr>
                                            <!-- <td class="text-center"><?php echo $totalPontos?></td> -->
                                            <td class="text-center"><?php echo formata_pontos($extrato->getPontos()) ?></td>
                                            <td class="text-center"><?php echo escape($extrato->getOperacao()) ?></td>
                                            <td><?php echo escape($extrato->getData('d/m/Y')) ?></td>
                                            <td><?php echo escape($extrato->getObservacao()) . $nivel; ?></td>
                                            <td><?php echo $totalPontos > 0 ? round($extrato->getPontos() * 100 / $totalPontos, 2) . '%' : round(0, 2) . '%'?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                            Widget::render('components/pagination', array(
                                'pager' => $pager,
                                'href' => get_url_site() . '/minha-conta/extrato-pontos-recompra/',
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
                        <div class="alert alert-success" style='background: #A1E63A'>
                            <h5><strong>Informativo!</strong> <?php echo $mensagemResgatePontosRecompra; ?></h5>
                        </div>
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