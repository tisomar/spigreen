<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Cadastro Vago</th>
            <th>Tipo</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Data da Ativação</th>
            <th>Data da Inativação</th>
        </tr>
        </thead>
        <tbody>
        <?php

        $arrTotalPontos = array();
        foreach ($pager as $object) : /* @var $object ClienteInativado */
           $cliente = $object->getClienteRelatedByClienteId();  
           ?>
            <tr>
                <td data-title="Nome">
                    <?php echo $object->getNome() ?>
                </td>
                <td data-title="E-mail">
                    <?php echo $cliente->getNome() ?>
                </td>
                <td data-title="E-mail">
                    <?php echo ClientePeer::getTipoCliente($cliente->getId()) ?>
                </td>
                <td data-title="E-mail">
                    <?php echo $object->getEmail() ?>
                </td>
                <td data-title="Telefone">
                    <?php echo $object->getTelefone() ?>
                </td>
                <td data-title="Inativacao">
                    <?php echo $cliente->getDataAtivacao('d/m/Y H:i:s')?>
                </td>
                <td data-title="Inativacao">
                    <?php echo $object->getCreatedAt('d/m/Y H:i:s')?>
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

    <!-- <div class="pull-right">
        <?php $totalGeral = 0; ?>
        <?php foreach ($arrTotalPontos as $tipoPonto => $totalPonto) : ?>
            <?php $totalGeral += $totalPonto; ?>
            <p class="text-right"><?php echo ($tipoPonto == 'RESIDUAL') ? 'RECOMPRA' : $tipoPonto ; ?>: <strong><?php echo number_format($totalPonto, 2, ',', '.');?></strong></p>

        <?php endforeach;?>
        <hr>
        <h3 class="text-right well-mini"><small>Total Bônus: </small> <?php echo number_format($totalGeral, 2, ',', '.'); ?></h3>

    </div> -->

</div>

<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
