<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CEP</th>
                <th>Pedido <abbr title="mínimo">Mín.</abbr></th>
                <th>Prazo de entrega</th>
                <th>Ativo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object FreteGratisRegiao */
                ?>
                <tr>
                    <td data-title="Região"><?php echo $object->getNome(); ?></td>
                    <td data-title="CEP"><?php echo format_cep($object->getCepInicial()); ?> &agrave; <?php echo format_cep($object->getCepFinal()); ?></td>
                    <td data-title="Pedido Mínimo"><?php echo 'R$ ' . format_money($object->getValorMinimo()); ?></td>
                    <td data-title="Prazo de entrega"><?php echo $object->getPrazoEntrega() . ' dias'; ?></td>
                    <td data-title="Ativo?"><?php echo get_toggle_option($_class, 'IsAtivo', $object->getId(), $object->getIsAtivo()); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <li class="divider"></li>
                                <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete"><i class="icon-trash"></i> Excluir</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
if (count($pager->getResult()) == 0) {
    echo 'Nenhum registro disponível';
}
?>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
