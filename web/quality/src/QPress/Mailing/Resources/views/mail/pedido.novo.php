<?php $this->start(); ?>
<?php $container = $GLOBALS['container'];
$admin = isset($admin) && $admin;
?>

    <table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
        <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Dados do pedido #<?php echo $pedido->getId(); ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Seguem abaixo as informações do pedido:
                </p>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>

                        <td width="50%" style="margin: 10px">

                            <h4 style="<?php echo $this->style('h4') ?>">
                                INFORMAÇÕES
                            </h4>

                            <p style="<?php echo $this->style('p') ?>">
                                Número do pedido: <strong><?php echo $pedido->getId(); ?></strong><br>
                                Data da compra: <strong><?php echo $pedido->getCreatedAt('d/m/Y'); ?></strong><br>
                                Valor da compra: <strong>R$ <?php echo format_money($pedido->getValorTotal()); ?></strong><br>
                            </p>

                            <br>


                            <h4 style="<?php echo $this->style('h4') ?>">
                                DADOS DO CLIENTE
                            </h4>

                            <p style="<?php echo $this->style('p') ?>">
                                <?php echo $pedido->getCliente()->getNomeCompleto(); ?><br/>
                                <?php echo $pedido->getCliente()->getCodigoFederal() ?><br />
                                <?php echo $pedido->getCliente()->getTelefone() ?><br>
                                <?php echo $pedido->getCliente()->getEmail() ?><br /><br />
                            </p>

                            <?php if ($pedido->getCodigoRastreio()): ?>
                                <br>
                                <h4 style="<?php echo $this->style('h4') ?>">
                                    DADOS DO TRANSPORTE
                                </h4>

                                <p style="<?php echo $this->style('p') ?>">
                                    Você pode acompanhar o envio com o código de rastreamento
                                    <a href="http://www.correios.com.br/sistemas/rastreamento/default.cfm">
                                        <?php echo $pedido->getCodigoRastreio(); ?>.
                                    </a>
                                </p>
                               
                            <?php else: ?>
                                <br>
                                <h4 style="<?php echo $this->style('h4') ?>">
                                    DADOS DO TRANSPORTE
                                </h4>

                                <p style="<?php echo $this->style('p') ?>">
                                    Caso sua transportadora seja TNT, para rastrear <br> seu pedido utilize as informações abaixo.<br> <br> 

                                    Tipo de Cliente: Remetente<br> 
                                    CNPJ/CPF: 31716218000332<br> 
                                    Filial Origem: - Todas -<br> 
                                    Tipo de Documento: Nota Fiscal<br> 
                                    Número Documento: (Número da sua Nota Fiscal)<br> 
                                    <a href="https://radar.tntbrasil.com.br/radar/public/localizacaoSimplificada">
                                        https://radar.tntbrasil.com.br/radar/public/localizacaoSimplificada
                                    </a>
                                </p>
                            <?php endif; ?>

                        </td>

                        <td width="50%" style="margin: 10px">

                            <h4 style="<?php echo $this->style('h4') ?>">
                                FORMA DE PAGAMENTO
                            </h4>

                            <?php if ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamento() ?></strong>
                                    <br>
                                    Status:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                    <br>
                                    Data vencimento:
                                    <strong>
                                        <?php echo $pedido->getPedidoFormaPagamento()->getDataVencimento('d/m/Y') ?>
                                        <?php if ($pedido->isAndamento() && $pedido->getLastPedidoStatus()->getId() == 1): ?>
                                            &minus;
                                            <a href="<?php echo $pedido->getPedidoFormaPagamento()->getUrlAcesso() ?>" target="_blank">(segunda via)</a>
                                        <?php endif; ?>
                                    </strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento: <strong><?php echo $pedido->getPedidoFormaPagamento()->getBandeira() ?></strong>
                                    <br>
                                    Numero de parcelas: <strong><?php echo $pedido->getPedidoFormaPagamento()->getNumeroParcelas() ?>x</strong>
                                    <br>
                                    Status: <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento: <strong>PagSeguro</strong>
                                    <br>
                                    Status: <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento: <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?>
                                        &minus; <?php echo $pedido->getPedidoFormaPagamento()->getFaturamentoDiretoOpcao() ?></strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento: <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?></strong>
                                    <br>
                                    Status: <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?></strong>
                                    <br>
                                    Status:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                    <br>
                                    <strong>
                                        <?php if ($pedido->isAndamento() && $pedido->getLastPedidoStatus()->getId() == 1): ?>
                                            &minus;
                                            <a href="<?php echo $pedido->getPedidoFormaPagamento()->getUrlAcesso() ?>" target="_blank">imprimir</a>
                                        <?php endif; ?>
                                    </strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?></strong>
                                    <br>
                                    Status:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                </p>

                            <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE): ?>

                                <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?></strong>
                                    <br>
                                    Status:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                    <br>
                                    <strong>
                                        <?php if ($pedido->isAndamento() && $pedido->getLastPedidoStatus()->getId() == 1): ?>
                                            &minus;
                                            <a href="<?php echo $pedido->getPedidoFormaPagamento()->getUrlAcesso() ?>" target="_blank">efetuar o pagamento</a>
                                        <?php endif; ?>
                                    </strong>
                                </p>
                                    <?php elseif ($pedido->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB): ?>
                                    <p style="<?php echo $this->style('p') ?>">
                                    Forma de pagamento:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?></strong>
                                    <br>
                                    Status:
                                    <strong><?php echo $pedido->getPedidoFormaPagamento()->getStatusLabel() ?></strong>
                                    <br>
                                    Data vencimento:
                                    <strong>
                                        <?php echo $pedido->getPedidoFormaPagamento()->getDataVencimento('d/m/Y') ?>
                                        <?php if ($pedido->isAndamento() && $pedido->getLastPedidoStatus()->getId() == 1): ?>
                                            &minus;
                                            <a href="<?php echo $pedido->getPedidoFormaPagamento()->getUrlAcesso() ?>" target="_blank">(segunda via)</a>
                                        <?php endif; ?>
                                    </strong>
                                </p>
                            <?php endif; ?>

                            <br>
                        
                    <?php if (!$pedido->isPagamentoMensalidade()):  ?>
                            
                            <h4 style="<?php echo $this->style('h4') ?>">
                                FRETE
                            </h4>

                            <p style="<?php echo $this->style('p') ?>">
                                Tipo de entrega: <strong><?php echo $container->getFreteManager()->getModalidade($pedido->getFrete())->getTitulo() ?></strong>
                                <br>
                                Prazo: <?php echo $pedido->getFretePrazo() ?>
                                <br>
                                Valor da entrega: <strong>R$ <?php echo format_money($pedido->getValorEntrega()) ?></strong>
                            </p>
                        
                            <?php
                            if ($pedido->getPedidoRetiradaLoja()) {
                                ?>
                                <h4 style="<?php echo $this->style('h4') ?>">
                                    LOCAL PARA RETIRADA
                                </h4>
                                <p style="<?php echo $this->style('p') ?>">
                                    <?php
                                    echo sprintf('<b>Loja:</b> %s<br><b>Endereço:</b> %s<br><b>Telefone:</b> %s',
                                        $pedido->getPedidoRetiradaLoja()->getNome(),
                                        nl2br($pedido->getPedidoRetiradaLoja()->getEndereco()),
                                        $pedido->getPedidoRetiradaLoja()->getTelefone()
                                    );
                                    ?>
                                </p>
                                <?php
                            } else {
                                ?>
                                <p style="<?php echo $this->style('p') ?>">
                                    <?php echo $pedido->getEndereco()->sprintf('%cep<br>%logradouro, %numero. %complemento<br />%bairro, %cidade/%uf'); ?>
                                </p>
                                <?php
                            }
                            ?>
                    <?php endif ?>

                        </td>
                    </tr>
                </table>
                <?php /* ?>
                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                
                <h4 style="text-align: center; <?php echo $this->style('h4') ?>">
                    STATUS DO PEDIDO
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="<?php echo $this->style('background-site') ?>">
                            <table align="" border="0" cellpadding="0" cellspacing="0" width="<?php echo $percent; ?>%">
                                <tr>
                                    <td style="background: <?php echo $this->style('color-principal') ?>;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <?php foreach ($allStatus as $pedidoStatus): ?>
                            <td style="<?php echo $this->style('p') ?> width: 25%; text-align: center">
                                <strong><?php
                                    echo str_replace(' ', '<br />', in_array($pedidoStatus->getId(), $pedidoStatusHistorico) ? $pedidoStatus->getLabelPosConfirmacao() : $pedidoStatus->getLabelPreConfirmacao())
                                    ?></strong>
                            </td>
                        <?php endforeach; ?>


                    </tr>
                </table>
                <?php */ ?>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>


                <h4 style="<?php echo $this->style('h4') ?>">
                    ITENS COMPRADOS
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <td style="padding: 10px 0px; "><strong>Produto</strong></td>
                                    <td style="text-align: center; padding: 10px 0px; "><strong>Quantidade</strong></td>
                                    <td style="text-align: right; padding: 10px 0px; "><strong>Valor Unitário</strong></td>
                                    <td style="text-align: right; padding: 10px 0px; "><strong>Valor Total</strong></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pedido->getPedidoItems() as $pedidoItem): /* @var $pedidoItem PedidoItem */ ?>
                                    <tr>
                                        <td style="<?php echo $this->style('td') ?>">
                                            <?php echo $pedidoItem->getProdutoVariacao()->getProdutoNomeCompleto() ?><br>
                                            Ref.: <?php echo $pedidoItem->getProdutoVariacao()->getSku() ?>
                                        </td>
                                        <td style="text-align: center; <?php echo $this->style('td') ?>">
                                            <?php echo $pedidoItem->getQuantidade() ?>
                                        </td>
                                        <td style="text-align: right; <?php echo $this->style('td') ?>">
                                            R$ <?php echo format_money($pedidoItem->getValorUnitario()) ?>
                                        </td>
                                        <td style="text-align: right; <?php echo $this->style('td') ?>">
                                            R$ <?php echo format_money($pedidoItem->getValorTotal()) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <tr>
                                    <td style="<?php echo $this->style('td') ?>">&nbsp;</td>
                                    <td style="<?php echo $this->style('td') ?>">&nbsp;</td>
                                    <td style="text-align: right; <?php echo $this->style('td') ?>; text-align: right;">
                                        <strong>Subtotal:</strong>
                                    </td>
                                    <td style="<?php echo $this->style('td') ?> text-align: right;">R$ <?php echo format_money($pedido->getValorItens()) ?></td>
                                </tr>
                                <?php if ($pedido->getValorDesconto() > 0): ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="<?php echo $this->style('td') ?> text-align: right;"><strong>Desconto:</strong></td>
                                        <td style="<?php echo $this->style('td') ?> text-align: right;">&minus;R$ <?php echo format_money($pedido->getValorDesconto()) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td style="<?php echo $this->style('td') ?> text-align: right;"><strong>Valor do Frete:</strong></td>
                                    <td style="<?php echo $this->style('td') ?> text-align: right;">R$ <?php echo format_money($pedido->getValorEntrega()) ?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td style="<?php echo $this->style('td') ?> text-align: right;"><strong><big>Valor Total:</big></strong></td>
                                    <td style="<?php echo $this->style('td') ?> text-align: right;"><strong><big>R$ <?php echo format_money($pedido->getValorTotal()) ?></big></strong></td>
                                </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>

                <h4 style="text-align: center; <?php echo $this->style('h4') ?>">
                    STATUS DO PEDIDO
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="<?php echo $this->style('background-site') ?>">
                            <table align="" border="0" cellpadding="0" cellspacing="0" width="<?php echo $percent; ?>%">
                                <tr>
                                    <td style="background: <?php echo $this->style('color-principal') ?>;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <?php foreach ($allStatus as $pedidoStatus): ?>
                            <td style="<?php echo $this->style('p') ?> width: 25%; text-align: center">
                                <strong><?php
                                    echo str_replace(' ', '<br />', in_array($pedidoStatus->getId(), $pedidoStatusHistorico) ? $pedidoStatus->getLabelPosConfirmacao() : $pedidoStatus->getLabelPreConfirmacao())
                                    ?></strong>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr height="20">
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p style="<?php echo $this->style('p') ?>">
                                <?php echo $pedido->getLastPedidoStatus()->getMensagem() ?>
                            </p>
                            <?php if ($pedido->getCodigoRastreio()): ?>
                                <p style="<?php echo $this->style('p') ?>">
                                    Você pode acompanhar o envio com o código de rastreamento
                                    <a href="http://www.correios.com.br/sistemas/rastreamento/default.cfm"><?php echo $pedido->getCodigoRastreio(); ?></a>.
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>

                <h4 style="<?php echo $this->style('h4') ?>">
                    OUTRAS INFORMAÇÕES
                </h4>

                <p style="<?php echo $this->style('p') ?>">Em caso de dúvidas, entre em contato conosco através de nosso <a href="<?php echo get_url_site() ?>/contato/">formulário de contato</a>.</p>

            </td>
        </tr>
        </tbody>
    </table>

<?php
$this->end('content');
$this->extend('mail/_layout');
