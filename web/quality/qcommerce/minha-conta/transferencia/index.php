<?php

require __DIR__ . '/actions/transferencia.actions.php';
use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-transferencia';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";

$pontosMinimos = 0;
?>

    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-transferencia">

    <?php
    include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
    include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';

    Widget::render('general/header');
    ?>

    <main role="main">
        <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Transferência de Pontos' => '')));
        Widget::render('general/page-header', array('title' => 'Transferência'));
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
                                Solicitar Transferência.
                            </h3>
                        </div>
                    </div>
                    <br>
                                        
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span>
                                    Total de bônus disponível <strong>R$ <?php echo format_money($pontosDisponiveis)  ?></strong>.
                                </div>
                            </div>       
                        </div>
                        <?php if ($bloqueiaTransferencia) : ?>
                            <div class="col-xs-12 text-center">
                                Realize a sua ativação mensal para efetuar transferências.
                            </div>
                        <?php elseif ($pontosDisponiveis >= $pontosMinimos) : ?>
                            <div class="col-xs-12">
                                <?php
                                \QPress\Template\Widget::render('forms/transferencia', array(
                                    'pontosDisponiveis' => $pontosDisponiveis,
                                ));
                                ?>
                            </div>
                        <?php else : ?>
                            <div class="col-xs-12 text-center">
                                Você não possui saldo para a transferência.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
    include QCOMMERCE_DIR . '/includes/footer.php';
    include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php';
    ?>

    </body>

</html>
