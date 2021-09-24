<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th colspan="2">Módulo</th>
                <th>Url</th>
                <th>Disponível para o cliente?</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object PermissaoModulo */
                ?>
                <tr>
                    <td data-title="Módulo">
                        <span class="text-muted hidden-xs"><?php echo str_repeat('- - - - ', $object->getTreeLevel() - 1) ?></span>
                        <i class="<?php echo escape($object->getIcon()); ?>"></i> <?php echo escape($object->getNome()); ?>
                    </td>
                    <td data-title="Módulo Pai">
                        <span  class="text-muted">
                            <?php echo $object->getParent() && $object->getParent()->getLevel() > 0 ? $object->getParent()->getNome() : '&nbsp;'; ?>
                        </span>
                    </td>
                    <td data-title="Url">
                        <?php echo escape($object->getUrl()); ?>
                    </td>
                    <td data-title="Disponível">
                        <?php echo get_toggle_option($_class, 'Mostrar', $object->getId(), $object->getMostrar()); ?>
                    </td>
                    <td data-title="Ações" class="text-right">
                        <?php
                        if (!$object->isRoot()) {
                            ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    Ações <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                    <li class="divider"></li>
                                    <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                                </ul>
                            </div>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>
</div>
