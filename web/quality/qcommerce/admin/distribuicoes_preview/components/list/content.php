<?php
$totalPontos = 0;
if (isset($_GET['distribuicao_id'])) {
    $totalPontos = DistribuicaoClienteQuery::create()
                        ->select(['total_pontos'])
                        ->withColumn('SUM(TOTAL_PONTOS)', 'total_pontos')
                        ->filterByDistribuicaoId($_GET['distribuicao_id'])
                        ->findOne();
}
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Indicação Indireta</th>
            <th>Recompra</th>
            <th>Liderança</th>
            <th>Total R$</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) : /* @var $object DistribuicaoCliente */
            ?>
            <tr>
                <td data-title="Nome"><?= $object->getCliente()->getNomeCompleto() ?></td>
                <td data-title="PontosAdesao"><?= number_format($object->getTotalPontosAdesao(), 2, ',', '.'); ?></td>
                <td data-title="PontosRecompra"><?= number_format($object->getTotalPontosRecompra(), 2, ',', '.'); ?></td>
                <td data-title="PontosLideranca"><?= number_format($object->getTotalPontosLideranca(), 2, ',', '.'); ?></td>
                <td data-title="Pontos"><?= number_format($object->getTotalPontos(), 2, ',', '.'); ?></td>
            </tr>
        <?php
        endforeach;

        if (!$pager->count()) :
            ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>

    </table>
</div>
<div class="col-xs-12">
    <p><strong>Total de Bônus: <?php echo number_format($totalPontos, 2, ',', '.') ?></strong></p>
    <?php echo $pager->showPaginacao(); ?>
</div>
