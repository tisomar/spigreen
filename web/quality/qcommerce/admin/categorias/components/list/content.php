<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Categoria</th>
            <th>Mostrar na Barra<br> de Categorias?</th>
            <th>Mostrar como destaque<br>na Página inicial?</th>
            <th>Mostrar no site?</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object ProdutoCategoria */
            ?>
            <tr>
                <td data-title="Categoria">
                    <?php if ($object->getLevel() > 1 && $parent = $object->getParent()) : ?>
                        <span class="text-muted">
                            <?php echo $parent->getNome() ?> <span class="icon-angle-right" style="margin: auto 7px;"></span>
                        </span>
                    <?php endif; ?>
                    <?php echo escape($object->getNome()); ?>
                </td>
                <td data-title="Add no menu?"><?php echo get_toggle_option($_class, 'MostrarBarraMenu', $object->getId(), $object->getMostrarBarraMenu()); ?></td>
                <td data-title="Destaque?"><?php echo get_toggle_option($_class, 'MostrarPaginaInicial', $object->getId(), $object->getMostrarPaginaInicial()); ?></td>
                <td data-title="Mostrar no site?"><?php echo get_toggle_option($_class, 'Disponivel', $object->getId(), $object->getDisponivel()); ?></td>
                <td class="text-right" data-title="Ações">
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
