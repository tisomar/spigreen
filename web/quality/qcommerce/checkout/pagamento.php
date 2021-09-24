<?php

use QPress\Template\Widget;

$strIncludesKey = 'checkout-pagamento';
ClearSaleMapper\Manager::set('page', 'checkout');

$clienteLogado = ClientePeer::getClienteLogado();
$gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/actions/pagamento.actions.php';

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="checkout-pagamento">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

<main role="main">
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.payment.tracking.php'; ?>
    <?php
    Widget::render('general/steps-checkout', array('active' => 3, 'progress' => '100'));
    Widget::render('general/page-header', array('title' => 'Pagamento'));
    Widget::render('components/flash-messages');
    Widget::render('carrinho/buttonContinuaCompra');

    // INFORMANDO O TOTAL DE PONTOS DA COMPRA
    $somaTotalPontos = 0;
    foreach ($carrinho->getPedidoItems() as $item) {
        $valorPontos = $item->getProdutoVariacao()->getProduto()->getValorPontos();
        $qtdProdutos = $item->getQuantidade();
        $somaTotalPontos += $qtdProdutos * $valorPontos;
    }
    ?>

    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-md-7">
                <h3>1. Resumo da compra</h3>
                <?php
                // Tabela de produtos
                Widget::render('general/table-products', array(
                    'itens' => $carrinho->getPedidoItemsJoinProdutoVariacao()
                ));
                
                // Subtotal da compra dos itens
                Widget::render('general/subtotal', array('value' => $carrinho->getValorItens()));

                if (!$carrinho->isPagamentoTaxaCadastro()) {
                    // Cupom de desconto
                    include __DIR__ . '/components/cupom.php';

                    // Desconto pagamento por pontos
                    include __DIR__ . '/components/desconto_pontos.php';
                }

                // Valor dos itens
                Widget::render('general/discount', array('value' => $carrinho->getValorDesconto()));

                $mostratFrete = true;

                if (!$franqueado && !$isNewReseller && $carrinho->isPagamentoTaxaCadastro()) {
                    $mostratFrete = false;
                }

                if (!$carrinho->isPagamentoMensalidade() && $mostratFrete) {
                    // Frete selecionado e valor
                    $freteSelecionado = $container->getFreteManager()->getModalidade($carrinho->getFrete());
                    $titulo = $freteSelecionado->getTitulo();
                    if ($carrinho->getFrete() == 'retirada_loja') {
                        $titulo .= '<br><i>' . nl2br($carrinho->getPedidoRetiradaLoja()->getEndereco()) . '</i>';
                    }

                    $prazo = "<br><i>Prazo: " . $carrinho->getFretePrazo() . ' após a confirmação do pagamento.</i>';
                    
                    Widget::render('general/shipping', array(
                        'value'             => $carrinho->getValorEntrega(),
                        'shippingName'      => $titulo,
                        'isEditable'        => true,
                        'prazo'             => $prazo,
                        'estoqueRetirada'   => $estoqueRetirada,
                        'codigoRastreio'    => '',
                    ));
                }

                // Total geral do pedido
                Widget::render('general/total', array('value' => $carrinho->getValorTotal(false), 'total_pontos' => $somaTotalPontos, 'bonus_produtos' => $bonusProdutosDisponíveis));

                // Endereço selecionado, caso não seja retirar em loja
                if ($carrinho->getFrete() != 'retirada_loja' && !$carrinho->isPagamentoMensalidade() && !$carrinho->isPagamentoTaxaCadastro()) {
                    Widget::render('general/delivery-address', array(
                        'editable'          => true,
                        'address'           => $carrinho->getEndereco(),
                        'isCart'            => true
                    ));
                }
                ?>
            </div>
            
            <?php /* se o pedido exige um patrocinador e não temos um patrocinador de hotsite, exibe o formulário de escolha de patrocinador */  ?>
            <?php if ($carrinho->precisaPatrocinador() && (!$container->getSession()->get('PATROCINADOR_HOTSITE_ID') || !ClienteQuery::create()->findPk($container->getSession()->get('PATROCINADOR_HOTSITE_ID')))) :  ?>
                <div class="col-xs-12 col-md-5">
                    <h3>2. Informe o código ou e-mail do patrocinador</h3>

                    <div class="panel">
                        <div class="panel-body bg-default accordion-payment-type">
                            <?php include __DIR__ . '/components/patrocinador.php';  ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="col-xs-12 col-md-5">

                <h3><?php echo ($carrinho->precisaPatrocinador()) ? '3' : '2' ?>. Escolha a forma de pagamento</h3>

                <div class="panel">
                    <div class="panel-body bg-default accordion-payment-type">
                    
                    <?php
                    $req = $request->request;
                    if ($req->has('valor_pagamento')):
                        function showPaymentInfo($formaPagamento, $valorPagamento, $extra) {
                            $formasPagamento = PedidoFormaPagamentoPeer::getFormasPagamento();
                            ?>
                            <h4>
                                <?= $formasPagamento[$formaPagamento] ?? '' ?>
                            </h4>
                            
                            <?php
                            if (!empty($extra['cartao'])):
                                $cartao = $extra['cartao'] ?? [];
                                $bandeira = strtoupper($extra['BANDEIRA'] ?? '');
                                $numero = $cartao['numero'] ?? '';
                                $parcelas = $extra['numero_parcelas'] ?? '';
                                ?>
                                
                                <p style="margin-bottom: 5px;">
                                    Bandeira: <?= $formasPagamento[$bandeira][$bandeira] ?? '' ?>
                                </p>
                                <p style="margin-bottom: 5px;">
                                    Número Cartão: <?= substr($numero, 0, 6) . ' ***** ' . substr($numero, -4) ?>
                                </p>
                                <p style="margin-bottom: 5px;">
                                    Titular: <?= $cartao['titular'] ?? '' ?>
                                </p>
                                <p style="margin-bottom: 5px;">
                                    Validade: <?= sprintf('%02d/%d', $cartao['validade_mes'] ?? '', $cartao['validade_ano'] ?? '') ?>
                                </p>

                                <?php if (!empty($parcelas)): ?>
                                    <p style="margin-bottom: 5px;">
                                        Nº parcelas: <?= "{$parcelas}x" ?>
                                    </p>
                                <?php endif ?>
                                
                                <?php
                            endif;
                            ?>
                            
                            <p>
                                Valor: R$ <?=  number_format($valorPagamento, '2', ',', '.') ?>
                            </p>
                            <?php
                        }

                        ?>
                        <h5><strong>Formas de pagamento selecionadas: </strong></h5>

                        <?php
                        
                        $pagamentoDividido = $request->request->get('pagamento_dividido', []);

                        $pagamentoDividido[] = [
                            'forma_pagamento' => $req->get('forma_pagamento', ''),
                            'valor_pagamento' => $req->get('valor_pagamento', ''),
                            'BANDEIRA' => $req->get('BANDEIRA', ''),
                            'cartao' => $req->get('cartao', ''),
                            'numero_parcelas' => $req->get('numero_parcelas', ''),
                        ];

                        foreach ($pagamentoDividido as $pag):
                            $valorPagamento = (float) str_replace(['.', ','], ['', '.'],
                                preg_replace('/[^\d\,\.]/', '', $pag['valor_pagamento'])
                            );

                            if($pag['forma_pagamento'] === 'PONTOS') {
                                if( $valorPagamento > $valorBonusUtilizados) {
                                    $valorPagamento = $valorBonusUtilizados;
                                }
                            }
                          
                            showPaymentInfo($pag['forma_pagamento'], $valorPagamento, [
                                'BANDEIRA' => $pag['BANDEIRA'] ?? false,
                                'cartao' => $pag['cartao'] ?? false,
                                'numero_parcelas' => $pag['numero_parcelas'] ?? false,
                            ]);
                        endforeach;

                        ?>
                        <?php
                    endif;
                    ?>

                    <input type="hidden" class="valor-restante-pagamento" value="<?= $valorRestanteDividido ?>"/>

                        <?php
                        /**
                         * @TODO
                         * - Trabalhar com a mesma classe para o pagseguro (será resolvido com o onmypay)
                         * - Conflitos de js entre formulário de cartão do pagseguro e do superpay
                         */

                        /**
                         * true = opened
                         * false = closed
                         */

                        $formasPagamentoSelecionado = array_column($pagamentoDividido, 'forma_pagamento');

                        $isOpenedPanel = false;

                        if ($pagamentoBoleto == 'cielo' && !in_array('CIELO_CARTAO_CREDITO', $formasPagamentoSelecionado)) {
                            include_once __DIR__ . '/components/pagamento/cielo-boleto.php';
                        }

                        if (!$isPagamentoDividido):
                            # Boleto PHP
                            if ($pagamentoBoleto == 'boleto_php') :
                                include_once __DIR__ . '/components/pagamento/boleto-php.php';
                            endif;

                            # Pagseguro (Checkout Transparente)
                            if (Config::get('pagseguro.opcao_pagamento') == "transparente") {
                                $gatewayPagSeguro = $container->getGatewayManager()->get('PagSeguroTransparente');
                                $gatewayPagSeguro->initialize();
                                try {
                                    include_once __DIR__ . '/components/pagamento/pagseguro-transparente-js.php';

                                    if ($pagamentoBoleto == 'pagseguro_transparente') {
                                        include_once __DIR__ . '/components/pagamento/pagseguro-transparente-boleto.php';
                                    }

                                    if ($pagamentoCartaoCredito == 'pagseguro_transparente') {
                                        include_once __DIR__ . '/components/pagamento/pagseguro-transparente-cartao.php';
                                    }

                                    if ($pagamentoCartaoDebito == 'pagseguro_transparente') {
                                        include_once __DIR__ . '/components/pagamento/pagseguro-transparente-debito.php';
                                    }
                                } catch (Exception $e) {
                                    echo $e->getMessage();
                                }
                            }

                            # Itau Shopline

                            if ($pagamentoBoleto == 'shopline') {
                                include_once __DIR__ . '/components/pagamento/itau-shopline.php';
                            }

                            # SuperPay - Cartão de Crédito
                            if ($endereco && $pagamentoCartaoCredito == 'yapay') {
                                include_once __DIR__ . '/components/pagamento/superpay-cartao-credito.php';
                            }
                        endif;

                        if (
                            $pagamentoCartaoCredito == 'cielo' &&
                            !in_array('CIELO_BOLETO_BB', $formasPagamentoSelecionado) &&
                            (array_count_values($formasPagamentoSelecionado)['CIELO_CARTAO_CREDITO'] ?? 0) < 2
                        ) {
                            include_once __DIR__ . '/components/pagamento/cielo-cartao-credito.php';
                        }

                        if ($pagamentoCartaoDebito == 'cielo') {
                            include_once __DIR__ . '/components/pagamento/cielo-cartao-debito.php';
                        }



                        $totalBonusDiretoDisponivel = $gerenciador->getTotalPontosDisponiveisParaResgate(
                            $clienteLogado,
                            null,
                            null,
                            Extrato::TIPO_INDICACAO_DIRETA,
                            true
                        );

                        // Bônus diretos
                        if (!$carrinho->isPagamentoTaxaCadastro() && $totalBonusDiretoDisponivel > 0) :
                            include_once __DIR__ . '/components/pagamento/pontos.php';
                        endif;

                        if (!$isPagamentoDividido) :
                            include_once __DIR__ . '/components/pagamento/transferencia.php';
                        endif;

                        if (!$isPagamentoDividido):
                            # PagSeguro - Redirecionamento
                            include_once __DIR__ . '/components/pagamento/pagseguro-redirecionamento.php';

                            # PayPal - Redirecionamento
                            include_once __DIR__ . '/components/pagamento/paypal-redirecionamento.php';

                            # Faturamento Direto
                            include_once __DIR__ . '/components/pagamento/faturamento-direto.php';
                        endif;

                        // Bônus diretos
                        if (!$carrinho->isPagamentoTaxaCadastro() && $totalBonusDiretoDisponivel > 0) :
                            include_once __DIR__ . '/components/pagamento/pontos.php';
                        endif;
                        
                        // Bônus frete
                        $totalBonusFreteDisponivel = $gerenciador->getTotalBonusFrete($clienteLogado);
                        if (!$carrinho->isPagamentoTaxaCadastro() && $totalBonusFreteDisponivel > 0 && (empty($carrinho->getDescontoPontos()) || !$carrinho->getDescontoPontos()->getPagamentoBonusFrete())) :
                            include_once __DIR__ . '/components/pagamento/bonus-frete.php';
                        endif;
                        
                        if($carrinho->getPedidoRetiradaLoja() != null && $carrinho->getPedidoRetiradaLoja()->getLojaId() != 9) :
                            if (Config::get('sistema.pagamento_em_loja') == 1):
                                include_once __DIR__ . '/components/pagamento/pagamento-em-loja.php';
                            endif;
                        endif;

                        if ($carrinho->getCliente()->isClientePreferencial()) :
                            include_once __DIR__ . '/components/pagamento/pontos-cliente-prerefencial.php';
                        endif;
                        ?>

                        <small style="color: #d35400;">
                            Obs.: Pedidos criados pelo horário de Brasília.
                        </small>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    form.form-payment strong {
        font-weight: 700;
    }
</style>

<script src="<?= asset('/admin/assets/plugins/form-maskmoney/jquery.maskMoney.min.js') ?>"></script>

<script>
    $(function() {
        $('.link-dividir-pagamento').click(function() {
            $(this)
                .parent()
                .next('.form-group')
                .toggle()
                .find('input[name="valor_pagamento"]')
                .prop('disabled', function(value) {
                    return !this.disabled;
                });
        });
    });

    $('form.form-payment').on('submit', function(e) {
        var $this = $(this),
            $inputValorPagamento = $this.find('input[name="valor_pagamento"]')

        if ($inputValorPagamento.length > 0 && !$inputValorPagamento.is(':disabled')) {
            var valorRestante = Number($('.valor-restante-pagamento').val())
            var valor = $inputValorPagamento.maskMoney('unmasked')[0]

            if (valor > valorRestante) {
                alert('O valor informado é maior que o valor total do pagamento.')

                e.preventDefault()
                e.stopPropagation()

                setTimeout(function() {
                    $this
                        .find('.confirm-payment')
                        .prop('disabled', false)
                        .find('.fa-spinner')
                        .remove()
                }, 1000);
            }

            if (valor == 0) {
                alert('O valor informado é insuficiente.')

                e.preventDefault()
                e.stopPropagation()

                setTimeout(function() {
                    $this
                        .find('.confirm-payment')
                        .prop('disabled', false)
                        .find('.fa-spinner')
                        .remove()
                }, 1000);
            }
            
        }
    })
</script>

<?php
include QCOMMERCE_DIR . '/includes/footer-checkout.php';
?>
</body>
</html>