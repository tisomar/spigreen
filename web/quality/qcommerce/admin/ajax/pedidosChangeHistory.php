<?php
include('../includes/config.inc.php');
include('../includes/security.inc.php');

header('Content-Type: text/html; charset=UTF-8');

$pedi1_cod = $_GET["id"];
$change = $_GET["acao"];
$pedi1_cod_ras = $_GET["codigo_rastreio"];

$pedido = PedidoPeer::retrieveByPK($pedi1_cod);

if ($pedido instanceof Pedido) {
    if ($change == 'voltar') {
        $pedido->voltaHistorico();
    }

    if ($change == 'avancar') {
        $pedido->avancaHistorico();
    }

    if (!empty($pedi1_cod_ras)) {
        $pedido->gravarCodigoRastreio($pedi1_cod_ras);
    }

    $c1 = new Criteria();
    $c1->add(HistoricoPeer::PEDIDO_ID, $pedi1_cod, Criteria::EQUAL);
    $c1->addAscendingOrderByColumn(HistoricoPeer::DATA);
    $historicos = HistoricoPeer::doSelect($c1);
    $contaHistorico = count($historicos);
    ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">
        <tr>
            <td class="header" colspan="3"><?php echo'Histórico do Pedido'; ?></td>
            <td class="header" colspan="2"></td>
        </tr>
        <?php
        $i = 1;

        if (Pagamento::consultarCielo($pedido->getTid()) !== "Negada") {
            if (($pedido->getSituacao() == Pedido::CANCELADO)) {
                ?>
                <tr>
                    <td colspan="5" class="center" style="color:#FF0000;font-weight:bold;"><?php echo $pedido->getDescSituacao(); ?></td>
                </tr>
                <?php
            } else {
                foreach ($historicos as $historico) {
                    /* @var $historicoPedido Situacao */
                    $historicoPedido = $historico->getSituacao();
                    ?>
                    <tr>
                        <td class="icone center"><?php echo $i; ?></td>
                        <td style="width:150px;" class="center"><?php echo $historico->getData('d/m/Y'); ?></td>
                        <td><?php echo $historicoPedido->getNome(); ?></td>
                        <td class="center icone"></td>
                        <td class="center icone"><?php
                        if (($i == $contaHistorico) && ($pedido->getSituacao() == Pedido::ANDAMENTO)) {
                            ?><a href="javascript:changeHistory('<?php echo $pedi1_cod; ?>','avancar','<?php echo $historicoPedido->getCodigoRastreio(); ?>')"><img src="../img/icones/check.png" class="tooltip" alt="Finalizar este processo" /></a><?php
                        }
                        ?></td>
                    </tr>
                    <?php
                    $i++;
                }
                if ($pedido->getSituacao() == Pedido::FINALIZADO) {
                    ?>
                    <tr>
                        <td colspan="5" class="center" style="color:#3BCF0C;font-weight:bold;"><?php echo $pedido->getDescSituacao(); ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
    <?php
}?>
