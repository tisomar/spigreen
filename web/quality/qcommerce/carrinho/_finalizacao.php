<?php
use QPress\Template\Widget;
$strIncludesKey = 'finalizar-pagamento';
require_once __DIR__ . '/../includes/security.php';
include_once __DIR__ . '/actions/finalizacao.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
    <?php Widget::render('general/header-checkout'); ?>

<main role="main">
    <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Carrinho' => '/carrinho', 'Identificação' => '/finalizar/identificacao','Finalização' => '')));
        Widget::render('components/flash-messages');
    ?>

    <div class="container">

        <div class="col-14">
            <section id="forma-de-entrega" >
                <span class="title-default">Escolha a forma de entrega:</span>
                <hr>
                <div class="frete-container">
                    <div class="hide alert alert-erro" id="frete-error">
                        <p>Você deve selecionar a forma de entrega antes de finalizar seu pedido.</p>
                    </div>

                    <ul class="list-frete container-tertiary">
                        <?php
                        $hasDisponivel = false;
                        // Verifica se tem servicos de frete disponiveis
                        if (count($container->getFreteManager()->getModalidades()) > 0) {
                            /* @var $modalidade \QPress\Frete\FreteInterface */
                            foreach ($container->getFreteManager()->getModalidades() as $modalidade) {
                                $responseFrete = $container->getFreteManager()->consultar($modalidade, $package);
                                if ($responseFrete->isDisponivel()) {
                                    $hasDisponivel = true;
                                    // Carrega o template de cada frete
                                    \QPress\Template\Widget::render('carrinho/finalizar/frete', array(
                                        'responseFrete' => $responseFrete,
                                        'modalidade'    => $modalidade,
                                        'freteSelecionado' => $carrinho->getFrete()
                                    ));
                                }
                            }
                        }
                        if (!$hasDisponivel) {
                            ?>
                            <li class="container container-secondary error">
                                Não há meios de entrega disponíveis nesta loja!
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </section>
            <br>
            <br>

            <span class="title-default">Escolha o meio de pagamento:</span><hr>

            <ul class="nav nav-tabs " role="tablist" id="tab-meio-pagamento">

                <?php
                $bandeirasDisponiveis = PedidoFormaPagamentoPeer::listBandeirasDisponiveis();
                if (count($bandeirasDisponiveis) > 0) :
                    $hasFormaPagamentoDisponivel = true;
                    ?>
                    <li>
                        <a href="#cartao-credito" role="tab" data-toggle="tab" class="title-small">Cartão de Crédito</a>
                    </li>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.boleto')) : ?>
                    <li>
                        <a href="#boleto" role="tab" data-toggle="tab" class="title-default">Boleto Bancário</a>
                    </li>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.pagseguro')) : ?>
                    <li>
                        <a href="#pagseguro" role="tab" data-toggle="tab" class="title-default">PagSeguro</a>
                    </li>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.bcash') && Config::get('meio_pagamento.bcash.modalidade') == 'popup') : ?>
                    <li>
                        <a href="#bcash" role="tab" data-toggle="tab" class="title-default">BCash</a>
                    </li>
                <?php endif; ?>

            </ul>

            <div class="tab-content" id="tab-content-meio-pagamento">

                <?php if (count($bandeirasDisponiveis) > 0) : ?>
                    <div class="tab-pane" id="cartao-credito">
                        <?php include __DIR__ . '/formas-pagamento/cartao-credito.php'; ?>
                    </div>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.boleto')) :
                    $hasFormaPagamentoDisponivel = true; ?>
                    <div class="tab-pane" id="boleto">
                        <?php include __DIR__ . '/formas-pagamento/boleto.php'; ?>
                    </div>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.pagseguro')) :
                    $hasFormaPagamentoDisponivel = true; ?>
                    <div class="tab-pane" id="pagseguro">
                        <?php include __DIR__ . '/formas-pagamento/pagseguro.php'; ?>
                    </div>
                <?php endif; ?>

                <?php if (Config::get('meio_pagamento.bcash') && Config::get('meio_pagamento.bcash.modalidade') == 'popup') :
                    $hasFormaPagamentoDisponivel = true; ?>
                    <div class="tab-pane" id="bcash">
                        <?php include __DIR__ . '/formas-pagamento/bcash.php'; ?>
                    </div>
                <?php endif; ?>

            </div>

            <?php
            if (false == $hasFormaPagamentoDisponivel) {
                include __DIR__ . '/formas-pagamento/_indisponivel.php';
            }
            ?>
        </div>

        <div class="col-10">

            <section class="container container-primary">
                <div class="align-center"  id="meu-pedido">
                    <?php include __DIR__ . '/components/endereco.entrega.php'; ?>
                    <a href="<?php echo get_url_site() ?>/central/meus-enderecos/?isLightbox=1" class="open-lightbox btn-link btn-full btn">Entregar em outro endereço</a>
                </div>
            </section>
            <hr/>

            <section class="container container-primary">
                <?php include __DIR__ . '/components/cupom.php'; ?>
            </section>
            <hr/>

            <section class="container container-primary">
                <?php include __DIR__ . '/components/table.itens.php'; ?>
            </section>

        </div>

    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
