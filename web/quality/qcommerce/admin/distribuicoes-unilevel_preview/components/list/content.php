<?php
if (isset($_GET['distribuicao_id'])) {
    $totalPontos = DistribuicaoClienteQuery::create()
                        ->select('total_pontos')
                        ->withColumn('SUM(qp1_distribuicao_cliente.TOTAL_PONTOS)', 'total_pontos')
                        ->filterByDistribuicaoId(18)
                        ->findOne();
}


use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Valor à receber</th>
            <th>Nível atingido</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object DistribuicaoUnilevelPreview */
            ?>
            <tr>
                <td data-title="Nome"><?php echo escape($object->getNome()) ?></td>
                <td data-title="Pontos"><?php echo escape($object->getNivel()); ?></td>
                <td data-title="Nv"><?php echo escape($object->getClassificacao()); ?></td>
            </tr>
        <?php } ?>
        <?php
        if ($pager->count() == 0) {
            ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
            <?php
        }
        ?>
        </tbody>

    </table>
</div>
<div class="col-xs-12">
    <p><strong>Total de Pontos: <?php echo format_number($totalPontos) ?></strong></p>
    <?php echo $pager->showPaginacao(); ?>
</div>
