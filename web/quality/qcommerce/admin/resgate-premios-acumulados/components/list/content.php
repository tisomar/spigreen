<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Premio</th>
            <th>Data</th>
            <th>Situação</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($pager->getResult() as $object) { /* @var $object Resgate */
            ?>
            <tr>
                <td data-title="Cliente">
                    <?php echo escape($object->getCliente()->getNomeCompleto()); ?>
                </td>
                <td data-title="Pontos"><?= $object->getPremio() ?></td>
                <td data-title="Data"><?php echo escape($object->getData('d/m/Y')) ?></td>
                <td data-title="Situações"><?php echo escape($object->getSituacao()) ?></td>
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                            <li class="divider"></li>
                            <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="/admin/resgate-premios-acumulados/delete?id=<?= $object->getId()?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                        </ul>
                    </div>
                </td>
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