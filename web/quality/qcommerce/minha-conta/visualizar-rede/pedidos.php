<?php
use QPress\Template\Widget;
require __DIR__ . '/actions/pedidos.actions.php';
include_once __DIR__ . '/../../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="minha-conta-visualizar-rede">
    
    <?php
        Widget::render('mfp-modal/header', array(
            'title' => $cliente->getNomeCompleto()
        ));
        ?>
    
    <div class="row">
        <div class="col-xs-12">
            <?php  if (count($pedidos) > 0) : ?>
                <table style="padding: 15px 0" class="table table-striped table-rede-sized">
                    <tbody>
                        <tr class="header">
                            <td class="text-center">Número Pedido</td>
                            <td class="text-center">Data Pedido</td>
                            <td class="text-center">Data Pagamento</td>
                            <td class="text-center">Pontos Gerados</td>
                            <td class="text-center">Bônus Usados</td>
                            <td class="text-right">Valor Pedido</td>
                        </tr>
                        <?php
                        $arrTotalPontosUsados = 0;
                        $arrTotalPontosGanhos = 0;
                        $arrTotalValor = 0;

                        foreach ($pedidos as $pedido) : /** @var $pedido Pedido */
                            $queryExtrato = ExtratoQuery::create()
                                ->filterByClienteId($pedido->getCliente()->getId())
                                ->filterByPedidoId($pedido->getId())
                                ->filterByTipo(Extrato::TIPO_INDICACAO_DIRETA)
                                ->findOne();

                            $pontosUsado = $pedido->getDescontoPontos() instanceof DescontoPagamentoPontos ? $pedido->getDescontoPontos()->getValorDesconto() : 0;
                            $pontosGerados = $queryExtrato ? $queryExtrato->getPedido()->getTotalPontosProdutos(Extrato::TIPO_INDICACAO_DIRETA) : 0;

                            $pontosGeradosPedido = $pedido->getValorPontos();
                            // se não tiver valor de desconto, o pagamento pode ter sido feito totalmente com bônus
                            if ($pontosUsado === 0) :
                                if ($pedido->getPedidoFormaPagamento()->getFormaPagamento() === PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS) :
                                    $pontosUsado = $pedido->getValorTotal();
                                endif;
                            endif;

                            ?>
                            <tr>
                                <td class="text-center"><?php echo $pedido->getId() ?></td>
                                <td class="text-center"><?php echo $pedido->getCreatedAt('d/m/Y') ?></td>
                                <td class="text-center"><?php echo $pedido->getDataConfirmacaoPagamento('d/m/Y'); ?></td>
                                <td class="text-center"><?php echo $pontosGeradosPedido ?></td>
                                <td class="text-center">
                                    <?php echo 'R$ ' . formata_pontos($pontosUsado) ?>
                                </td>
                                <td class="text-right">R$ <?php echo number_format($pedido->getValorTotal(), 2, ',', '.') ?></td>

                            </tr>
                            <?php
                            $arrTotalPontosUsados += $pontosUsado;
                            $arrTotalPontosGanhos += $pontosGerados;
                            $arrTotalValor += $pedido->getValorTotal();
                        endforeach; ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <!-- <td class="text-center"><?php echo $arrTotalPontosGanhos ?></td> -->
                            <td class="text-center"><?php echo 'R$ ' . formata_pontos($arrTotalPontosUsados) ?></td>
                            <td class="text-right">R$ <?php echo number_format($arrTotalValor, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>

            <?php  else : ?>
                <h4 class="text-center">Nenhum pedido feito este mês.</h4>
            <?php  endif; ?>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../../includes/footer-lightbox.php' ?>
</body>
</html>
