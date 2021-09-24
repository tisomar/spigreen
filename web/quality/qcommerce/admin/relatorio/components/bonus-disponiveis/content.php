<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Total bônus disponíveis</th>
            <th>Toral transferência enviadas</th>
            <th>Toral transferência recebida</th>
            <th>Total resgate</th>
            <th>Total pagamento com bônus</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $arrTotalPontos = array();
   
        foreach ($pager as $object) : /* @var $object Resgate */
            $clienteNome = $object->getNomeCompleto();
            $clientId = $object->getId();

            $totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($object, $dataFiltro['min'], $dataFiltro['max'], 'INDICACAO_DIRETA');

            // ENVIADAS
            $transferenciasEnviadas = TransferenciaQuery::create()
                ->select(['somaenviadas'])
                ->filterByClienteRemetenteId($clientId)
                ->filterByData($dataFiltro)
                ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somaenviadas')
                ->findOne();

            // RECEBIDAS
            $transferenciasRecebidas = TransferenciaQuery::create()
                ->select(['somarecebidas'])
                ->filterByClienteDestinatarioId($clientId)
                ->filterByData($dataFiltro)
                ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somarecebidas')
                ->findOne();

            // TotalResgate 
            $totalResgate = ResgateQuery::create()
                ->select(['somaResgate'])
                ->filterByClienteId($clientId)
                ->filterByData($dataFiltro)
                ->filterBySituacao('EFETUADO')
                ->withColumn("SUM(VALOR)", 'somaResgate')
                ->findOne();

            // Total Pagamento de pedidos com bônus
            $pagementoPedidosComBonus = ExtratoQuery::create()
            ->select(['somaPagamentos'])
            ->filterByTipo('PAGAMENTO_PEDIDO')
            ->filterByClienteId($clientId)
            ->withColumn("SUM(PONTOS)", 'somaPagamentos')
            ->filterByData($dataFiltro)
            ->filterByOperacao('-')
            ->findOne();

           ?>
            <tr>
                <td data-title="Nome">
                    <?php echo $clienteNome ?>
                </td>
                <td data-title="TotalBonusDisponiveis">
                    <?php echo 'R$ ' . number_format($totalPontosDisponiveis, '2', ',', '.') ?? 0?>
                </td>
                <td data-title="TransferenciasEnviadas">
                    <?php echo 'R$ ' . number_format($transferenciasEnviadas , '2', ',', '.')?? 0 ?>
                </td>
                <td data-title="TransferenciasRecebidas">
                    <?php echo 'R$ ' . number_format($transferenciasRecebidas, '2', ',', '.') ?? 0?>
                </td>
                <td data-title="TotalResgate">
                    <?php echo 'R$ ' . number_format($totalResgate, '2', ',', '.') ?? 0?>
                </td>
                <td data-title="PagementoPedidosComBonus">
                    <?php echo 'R$ ' . number_format($pagementoPedidosComBonus, '2', ',', '.') ?? 0?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($pager->count() == 0) : ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
   
</div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>