<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Descrição</th>
            <th>CEP</th>
            <th>Ativo</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) : /* @var $object CentroDistribuicao */
            ?>
            <tr>
                <td>
                    <?= $object->getDescricao() ?>
                </td>
                <td>
                    <?= $object->getCep() ?>
                </td>
                <td data-title="Add no menu?">
                    <?= get_toggle_option($_class, 'Status', $object->getId(), $object->getStatus()); ?>
                </td>
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <li>
                                <a title="Editar" href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach;

        if ($pager->count() == 0) {
            ?>
            <tr>
                <td colspan="4">Nenhum registro encontrado</td>
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
