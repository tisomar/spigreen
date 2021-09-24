<?php
use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-bonus-lideranca';

include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";

require_once 'actions/extrato-bonus-lideranca.actios.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-bonus-lideranca">
<?php
include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';

Widget::render('general/header');
?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Extrato de bônus liderança' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Extrato de bônus liderança'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h3>
                    Distribuições
                </h3>
                <br>
                <div class="table-vertical">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Código</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">Valor Liderança</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($pager as $distribuicaoCliente):
                        $distribuicao = $distribuicaoCliente->getDistribuicao();
                        ?>
                        <tr>
                            <th class="text-center"><?= $distribuicao->getId() ?></th>
                            <th class="text-center"><?= $distribuicao->getData('d/m/Y') ?></th>
                            <th class="text-center"><?= $distribuicaoCliente->getTotalPontosLideranca() ?></th>
                            <th class="text-right">
                                <a
                                    class="btn btn-sm btn-primary"
                                    style="padding: 4px 10px;"
                                    href="<?= get_url_site() ?>/minha-conta/extrato-bonus-lideranca-detalhes/<?= $distribuicao->getId() ?>"
                                >
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </th>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </div>

            <?php
            Widget::render('components/pagination', [
                'pager' => $pager,
                'href' => get_url_site() . '/minha-conta/extrato-bonus-lideranca/',
                'queryString' => $queryString,
                'align' => 'center',
            ]);
            ?>
        </div>
    </div>
</main>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>