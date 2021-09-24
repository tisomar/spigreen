<?php
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-pedido-detalhes';
/* @var $objPedido Pedido */
require_once QCOMMERCE_DIR . '/includes/security.php';
include_once __DIR__ . '/actions/detalhes.actions.php';
include_once QCOMMERCE_DIR . '/includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-pedido-detalhes">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>


<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Meus Pedidos' => '/minha-conta/pedidos', 'Pedido ' . $objPedido->getId() => '')));
    Widget::render('general/page-header', array('title' => 'Pedido Nº ' . $objPedido->getId()));
    Widget::render('components/flash-messages');
    
    // INFORMANDO O TOTAL DE PONTOS DA COMPRA
    $somaTotalPontos = 0;
    foreach ($objPedido->getPedidoItems() as $item) {       
        $valorPontos = $item->getProdutoVariacao()->getProduto()->getValorPontos();
        $qtdProdutos = $item->getQuantidade();
        $somaTotalPontos += $qtdProdutos * $valorPontos;
    }

    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <p>Data da compra: <?php echo $objPedido->getCreatedAt('d/m/Y') ?></p>

                <?php if ($objPedido->isAndamento()) : ?>
                    <p>Status: <strong><span class="text-warning"><?php echo $objPedido->getLastPedidoStatus()->getLabelPreConfirmacao() ?></span></strong></p>
                <?php elseif ($objPedido->isFinalizado()) : ?>
                    <p>Status: <strong><span class="text-success">Pedido Finalizado</span></strong></p>
                <?php elseif ($objPedido->isCancelado()) : ?>
                    <p>Status: <strong><span class="text-danger">Pedido Cancelado</span></strong></p>
                <?php endif; ?>
                
                <hr>

                <?php
                Widget::render('general/table-products', array('itens' => $objPedido->getPedidoItems()));
                Widget::render('general/subtotal', array('value' => $objPedido->getValorItens()));

                if ($objPedido->getCupom()) {
                    ?>
                    <div class="box-secondary box-secondary-first bg-default">
                        <span class="fa fa-ticket"></span> Cupom de desconto:
                        <label class="label label-default"><?php echo $objPedido->getCupom()->getCupom() ?></label>
                        <label
                            class="label label-success"><?php echo $objPedido->getCupom()->getValorDescontoFormatado() ?></label>
                        &minus;<small>
                            R$&nbsp;</small><?php echo format_money($objPedido->getValorDescontoBy(CupomPeer::OM_CLASS)) ?>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                }

                Widget::render('general/discount', array('value' => $objPedido->getValorDesconto()));

                // Frete selecionado e valor
                $freteSelecionado = $container->getFreteManager()->getModalidade($objPedido->getFrete());
                $titulo = $freteSelecionado->getTitulo();
                if ($objPedido->getFrete() == 'retirada_loja') {
                    $titulo .= '<br><i>' . nl2br($objPedido->getPedidoRetiradaLoja()->getEndereco()) . '</i>';
                }

                $prazo = "<br><i>Prazo: " . $objPedido->getFretePrazo() . ' após a confirmação do pagamento.</i>';
                
                $codigoRastreio = '';
                if($objPedido->getFrete() != 'retirada_loja') :
                    $cod = $objPedido->getCodigoRastreio() ?? 'N/I';

                    $codigoRastreio = "
                    <br>
                    <i>
                        Código de rastreio:
                        <strong>{$cod}</strong>
                    </i>
                    <br/>";

                    $linkRastreioCompleto = !empty($objPedido->getLinkRastreio()) ||  $objPedido->getLinkRastreio() !== '' ? 
                        "<a href=\"{$objPedido->getLinkRastreio()}\" style=\"color: #56af50;\" target=\"_blank\">
                            Rastrear meu pedido
                        </a>": '';

                    switch($objPedido->getTransportadoraNome()): 
                        case 'PAC':
                        case 'SEDEX':
                            $codigoRastreio .= "
                                <a href=\"https://linketrack.com/?utm_source=sidenav/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o correios
                                </a>
                                <br/>". $linkRastreioCompleto;
                            break;
                        case 'TNT':
                            $codigoRastreio .= "
                                <a href=\"https://radar.tntbrasil.com.br/radar/public/localizacaoSimplificada/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o radar tntbrasil
                                </a>
                                <br/>". $linkRastreioCompleto;
                            break;
                        case 'Gollog':
                            $codigoRastreio .= "
                                <a href=\"https://servicos.gollog.com.br/app/main/tracking/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o Gollog
                                </a>
                                <br/>". $linkRastreioCompleto;
                            break;
                        case 'Carvalima':
                            $codigoRastreio .= "
                                <a href=\"https://www.carvalima.com.br/rastreamento/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o carvalima
                                </a>
                                <br/>". $linkRastreioCompleto;
                            break;
                        case 'ALT Brasil':
                            $codigoRastreio .= "
                                <a href=\"http://altbrasil.com.br/rastreamento.php#/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o altbrasil
                                </a>". $linkRastreioCompleto;
                            break;
                        case 'TRX':
                            $codigoRastreio .= "
                                <a href=\"http://www.trxtransportadora.com.br/\" style=\"color: #56af50;\" target=\"_blank\">
                                    Ir para o TRX
                                </a><br>". $linkRastreioCompleto;
                            break;
                    endswitch;
                endif;

                Widget::render('general/shipping', array(
                    'value'         => $objPedido->getValorEntrega(),
                    'shippingName'  => $titulo,
                    'prazo'         => $prazo,
                    'codigoRastreio'=> $codigoRastreio,
                    'estoqueRetirada'=> '',
                    'isEditable'    => false,
                ));
                Widget::render('general/total', array(
                    'value'         => $objPedido->getValorTotal(),
                    'installment'   => $objPedido->getPedidoFormaPagamento()->getNumeroParcelas(),
                    'total_pontos'  => $somaTotalPontos
                ));
              
                ?>

                <div class="header-session box-secondary bg-default">
                    <h3 class="tit h4">Forma de Pagamento</h3>
                </div>

                <?php
                foreach ($objPedido->getPedidoFormaPagamentos() as $objFormaPagamento):
                    $formaPagamento = $objFormaPagamento->getFormaPagamento();
                    $descricaoPagamento = $objFormaPagamento->getFormaPagamentoDescricao();
                    $valorPagamento = $objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal();
                    $lastStatusId = $objPedido->getLastPedidoStatus()->getId();
                    ?>
                    <div class="box-secondary box-payment">
                        <?php if ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-boleto-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    Vencimento: <strong><?= $objFormaPagamento->getDataVencimento('d/m/Y') ?></strong>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1) : ?>
                                        <span class="small">&minus;</span>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">imprimir</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-boleto-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1) : ?>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">imprimir</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-itau-shopline-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1) : ?>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">imprimir</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-<?= strtolower($objFormaPagamento->getBandeira()) ?>-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <span class="small">em <?= $objFormaPagamento->getNumeroParcelas() ?>x
                                        de <?= format_money($objFormaPagamento->getValorPorParcela()) ?></span>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-<?= strtolower($objFormaPagamento->getBandeira()) ?>-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <span class="small">em <?= $objFormaPagamento->getNumeroParcelas() ?>x
                                        de <?= format_money($objFormaPagamento->getValorPorParcela()) ?></span>
                                </span>
                            </div>
                            <div class="clearfix"></div>

                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-<?= strtolower($objFormaPagamento->getBandeira()) ?>-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-paypal-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1 && $objFormaPagamento->getTransacaoId() == null) : ?>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">Efetuar o pagamento</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-pagseguro-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1 && $objFormaPagamento->getTransacaoId() == null) : ?>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">Efetuar o pagamento</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-faturamento_direto-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?> &minus;
                                    <?= $objFormaPagamento->getFaturamentoDiretoOpcao() ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-pagseguro-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                    TID: <strong><?= $objFormaPagamento->getTransacaoId() ?></strong>
                                    <?php if ($objPedido->isAndamento() && $lastStatusId == 1) : ?>
                                        <span class="small">&minus;</span>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small"><span class="<?= icon('print'); ?>"></span> imprimir</span></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-pagseguro-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-boleto-32"></span>
                                </span>
                                <span class="pull-left">
                                    <?= $descricaoPagamento ?><br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                        <a href="<?= $objFormaPagamento->getUrlAcesso() ?>" target="_blank"><span class="small">efetuar pagamento</span></a>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-equal"></span>
                                </span>
                                <span class="pull-left">
                                    Bônus<br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-equal"></span>
                                </span>
                                <span class="pull-left">
                                    Pagamento em Loja<br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                </span>
                            </div>
                        <?php elseif ($formaPagamento == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA): ?>
                            <div>
                                <span class="pull-left" style="margin-right: 15px;">
                                    <span class="icon-equal"></span>
                                </span>
                                <span class="pull-left">
                                    Transferência<br>
                                    Valor: <strong>R$ <?= format_money($valorPagamento) ?></strong><br>
                                </span>
                            </div>
                        <?php endif ?>

                        <div class="clearfix"></div>
                    </div>
                    <?php
                endforeach
                ?>

                <?php
                if (!$container->getFreteManager()->getModalidade($objPedido->getFrete()) instanceof \QPress\Frete\Services\RetiradaLoja\RetiradaLoja) {
                    Widget::render('general/delivery-address', array('address' => $objPedido->getEndereco()));
                }
                ?>

                <?php if ($objPedido->getCodigoRastreio()) : ?>
                    <?php if (false !== strpos($objPedido->getFrete(), 'correios_')) { ?>
                        <div class="header-session box-secondary bg-default">
                            <h3 class="tit h4">CORREIOS - Histórico do Objeto (<?php echo $objPedido->getCodigoRastreio() ?>)</h3>
                        </div>
                        <div class="box-secondary box-payment">
                            <p class="text-muted">
                                <i>
                                    * O horário não indica quando a situação ocorreu, mas sim quando os dados foram recebidos pelo sistema,
                                    exceto no caso do SEDEX 10 e do SEDEX Hoje, em que ele representa o horário real da entrega.
                                </i>
                            </p>
                            <?php
                            try {
                                //Cria o objeto
                                $rastreamento = new \QPress\Correios\CorreiosRastreamento('ECT', 'SRO');
                                $rastreamento->setTipo(\QPress\Correios\Correios::TIPO_RASTREAMENTO_LISTA);
                                $rastreamento->setResultado(\QPress\Correios\Correios::RESULTADO_RASTREAMENTO_TODOS);
                                $rastreamento->addObjeto($objPedido->getCodigoRastreio());
                                if ($rastreamento->processaConsulta()) {
                                    $retorno = $rastreamento->getRetorno();
                                    if ($retorno->getQuantidade() > 0) {
                                        foreach ($retorno->getResultados() as $resultado) {
                                            $_eventos = array();
                                            foreach ($resultado->getEventos() as $eventos) {
                                                $_eventos[] = $eventos;
                                            }
                                            $_eventos = array_reverse($_eventos);
                                            ?>
                                            <table class="table table-bordered">
                                                <tbody>
                                                <?php
                                                $last_i = key(array_slice($_eventos, -1, 1, true));
                                                foreach ($_eventos as $i => $eventos) {
                                                    ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <b><?php echo $eventos->getData() . '<br>' . $eventos->getHora() ?></b>
                                                            <br>
                                                            <?php
                                                            if ($eventos->getStatus() == 1 &&
                                                                (
                                                                    $eventos->getTipo() == \QPress\Correios\Correios::TIPO_EVENTO_BDE
                                                                    || $eventos->getTipo() == \QPress\Correios\Correios::TIPO_EVENTO_BDI
                                                                    || $eventos->getTipo() == \QPress\Correios\Correios::TIPO_EVENTO_BDR
                                                                )
                                                            ) {
                                                                ?>
                                                                <i class="text-success fa fa-ok fa-2x"></i>
                                                                <?php
                                                            } elseif ($last_i == $i) {
                                                                ?>
                                                                <i class="text-muted fa fa-stop fa-2x"></i>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <i class="text-muted fa fa-arrow-down fa-2x"></i>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <b><?php echo $eventos->getDescricaoTipo() ?></b>
                                                            <br/>
                                                            <span><?php echo $eventos->getLocalEvento() . ' &minus; ' . $eventos->getCidadeEvento() . '/' . $eventos->getUfEvento() . ' &minus; ' . $eventos->getCodigoEvento(); ?></span>
                                                            <br/>
                                                            <b><?php echo $eventos->getDescricaoStatus() ?>
                                                                <?php
                                                                if ($eventos->getPossuiDestino()) {
                                                                    ?>
                                                                    <?php
                                                                    echo $eventos->getLocalDestino()
                                                                    , ' (' . $eventos->getCidadeDestino()
                                                                    , ' - ' . $eventos->getBairroDestino()
                                                                    , ', '
                                                                    , $eventos->getUfDestino()
                                                                    , ' - '
                                                                    , $eventos->getCodigoDestino() . ')';
                                                                    ?>

                                                                    <?php
                                                                }
                                                                ?>
                                                            </b>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                    }
                                } else {
                                    echo 'Nenhum rastreamento encontrado.';
                                }
                            } catch (Exception $e) {
                                echo 'Ocorreu um erro ao processar sua solicitação. Erro: ' . $e->getMessage() . '<br>';
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <!--                        <p>-->
                    <!--                            Você pode acompanhar o envio com o código de rastreamento:-->
                    <!--                            <a href="http://www.correios.com.br/sistemas/rastreamento/default.cfm" target="_blank">-->
                    <!--                                --><?php //echo $objPedido->getCodigoRastreio(); ?>
                    <!--                            </a>-->
                    <!--                        </p>-->
                <?php endif; ?>

            </div>


        </div>

    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

</body>
</html>
