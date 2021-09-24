<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <?php if (UsuarioPeer::getUsuarioLogado()->isMaster()) : ?>
                <th>Rede Social</th>
                <th><abbr title="Coluna disponível somente para usuário qpress">*</abbr> Icon</th>
                <th>Link</th>
                <th>Ordem</th>
                <th>Ativo</th>
                <th><abbr title="Coluna disponível somente para usuário qpress">*</abbr> Ações</th>
            <?php else : ?>
                <th>Rede Social</th>
                <th>Link</th>
                <th>Ordem</th>
                <th>Ativo</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Rede */
            ?>
            <tr>
                <?php if (UsuarioPeer::getUsuarioLogado()->isMaster()) : ?>
                    <td data-title="Nome"><?php echo $object->getNome(); ?></td>
                    <td data-title="* Icon">fa-<?php echo edit_inline($object->getIcon(), $_class, 'Icon', $object->getId()); ?></td>
                    <td data-title="Link"><?php echo edit_inline($object->getLink(), $_class, 'Link', $object->getId()); ?></td>
                    <td data-title="Ordem"><?php echo edit_inline($object->getOrdem(), $_class, 'Ordem', $object->getId(), 'number'); ?></td>
                    <td data-title="Ativo?"><?php echo get_toggle_option($_class, 'Ativo', $object->getId(), $object->getAtivo()); ?></td>
                    <td data-title="* Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li>
                                    <a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete">
                                        <i class="icon-trash"></i> Excluir
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                <?php else : ?>
                    <td data-title="Nome"><?php echo $object->getNome(); ?></td>
                    <td data-title="Link"><?php echo edit_inline($object->getLink(), $_class, 'Link', $object->getId()); ?></td>
                    <td data-title="Ordem"><?php echo edit_inline($object->getOrdem(), $_class, 'Ordem', $object->getId(), 'number'); ?></td>
                    <td data-title="Ativo?"><?php echo get_toggle_option($_class, 'Ativo', $object->getId(), $object->getAtivo()); ?></td>
                <?php endif; ?>
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
