<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Login</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Usuario */
            ?>
            <tr>
                <td data-title="Nome"><?php echo $object->getNome(); ?></td>
                <td data-title="E-mail"><?php echo $object->getEmail(); ?></td>
                <td data-title="Login"><?php echo $object->getLogin(); ?></td>
                <td data-title="Ações" class="text-right">
                    <?php
                    if ($object->isMaster() && UsuarioPeer::getUsuarioLogado()->isMaster()) {
                        ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                            </ul>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <?php if (UsuarioPeer::getUsuarioLogado()->getId() != $object->getId()) : ?>
                                    <li class="divider"></li>
                                    <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
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
