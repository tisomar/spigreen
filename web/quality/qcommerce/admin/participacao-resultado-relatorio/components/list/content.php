<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Bônus</th>
            <!-- <th>Percentual (%)</th> -->
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object ParticipacaoResultadoCliente */
            ?>
            <tr>
                <td data-title="Nome"><?php echo escape($object->getCliente()->getNomeCompleto()) ?></td>
                <td data-title="Pontos">R$ <?php echo number_format($object->getTotalPontos(), '2', ',', ''); ?></td>
                <!-- <td data-title="Percentual"><?php echo number_format($object->getPercentual(), '2', ',', ''); ?></td> -->
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
    <?php echo $pager->showPaginacao(); ?>
</div>
