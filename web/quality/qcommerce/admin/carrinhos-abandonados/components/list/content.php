<?php

use PFBC\Element;
?>

<div class="table-responsive">
    <h5>
        * São considerados carrinhos abandonados todos os carrinhos que não sofreram alterações nas últimas 48 horas.
    </h5>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th class="text-right">Valor Total</th>
                <th class="text-center">Último envio</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Pedido */
                ?>
                <tr>
                    <td data-title="Data"><?php echo $object->getCreatedAt('d/m/Y'); ?></td>
                    <td data-title="Cliente"><?php echo $object->getCliente() ? $object->getCliente()->getNomeCompleto() : '--'; ?></td>
                    <td data-title="Valor" class="text-right">R$ <?php echo format_money($object->getValorTotal()); ?></td>
                    <td data-title="Último envio" class="text-center"><?php echo $object->getDataAvisoAbandono('d/m/Y H:i:s'); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '/?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <li class="divider"></li>
                                <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php
            if (count($pager->getResult()) == 0) {
                ?>
                <tr>
                    <td colspan="20">
                        Nenhum registro disponível
                    </td>
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
