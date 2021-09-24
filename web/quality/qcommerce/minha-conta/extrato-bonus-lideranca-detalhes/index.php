<?php
use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-bonus-lideranca';

include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";

require_once 'actions/extrato-bonus-lideranca-detalhes.actions.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-bonus-lideranca-detalhes">
<?php
include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';

Widget::render('general/header');
?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Detalhes bônus liderança' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Detalhes bônus liderança'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h3>
                    Detalhes Bônus Liderança - Distribuição <?= $distribuicaoId ?>
                </h3>
                <br>
                <div class="table-vertical">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-left">Rede</th>
                            <th class="text-center">Valor Liderança</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($pager as $bonusLiderança):
                        $cliente = $bonusLiderança->getClienteRelatedByFilhoDiretoId();
                        ?>
                        <tr>
                            <th class="text-left"><?= $cliente ? $cliente->getNomeCompleto() : 'Bônus Liderança Pessoal' ?></th>
                            <th class="text-center"><?= number_format($bonusLiderança->getValor(), 2, ',', '.') ?></th>
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