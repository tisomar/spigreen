<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Bônus</th>
            <th>Descrição</th>
        </tr>
        </thead>
        <tbody>
        <?php

        $arrTotalPontos = array();
        foreach ($pager as $object) : /* @var $object Extrato */
            if (!isset($arrTotalPontos[$object->getTipo()])) :
                $arrTotalPontos[$object->getTipo()] = $object->getPontos();
            else :
                $arrTotalPontos[$object->getTipo()] += $object->getPontos();
            endif;

            ?>
            <tr>
                <td data-title="Data">
                    <?php echo $object->getData('d/m/Y') ?>
                </td>
                <td data-title="Nome">
                    <?php echo $object->getCliente()->getNomeCompleto() ?>
                </td>
                <td data-title="Tipo">
                    <?php echo $object->getTipoDesc() ?>
                </td>
                <td data-title="Bônus">
                    <?php echo 'R$ ' . number_format($object->getPontos(), 2, ',', '.') ?>
                </td>
                <td data-title="Descriçao">
                    <?php
                        if ($object->getPedido() !== null) :
                            $nivel = $object->getPedido()->getCliente()->getTreeLevel() - $object->getCliente()->getTreeLevel();
                            echo $object->getObservacao() . ' (' . $nivel . 'º nível)';
                        else:
                            echo $object->getObservacao();
                        endif;
                    ?>
                </td>
            </tr>
            <?php
        endforeach;

        if ($pager->count() == 0) :
            ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>


    </table>

    <div class="pull-right">
        <?php $totalGeral = 0; ?>
        <?php foreach ($arrTotalPontos as $tipoPonto => $totalPonto) : ?>
            <?php $totalGeral += $totalPonto; ?>
            <p class="text-right"><?php echo ($tipoPonto == 'RESIDUAL') ? 'RECOMPRA' : $tipoPonto ; ?>: <strong><?php echo number_format($totalPonto, 2, ',', '.');?></strong></p>

        <?php endforeach;?>
        <hr>
        <h3 class="text-right well-mini"><small>Total Bônus: </small> <?php echo number_format($totalGeral, 2, ',', '.'); ?></h3>
        
    </div>
 
</div>
<div class="col-xs-12 pull-right">
    <?php echo $pager->showPaginacao(); ?>
</div>