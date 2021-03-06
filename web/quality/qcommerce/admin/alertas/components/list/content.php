<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Título</th>
            <th>Data de envio</th>
            <th>Tipo de mensagem</th>
            <th>Usuário</th>
            <th>Só leitura</th>
            <th>Ordem</th>
            <th>Visualizar prévia e detalhes</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object DocumentoAlerta */
            ?>
            <tr>
                <td data-title="Título">
                    <?php echo $object->getTitulo(); ?>
                </td>
                <td data-title="Data de envio">
                    <?php echo $object->getDataEnvio('d/m/Y'); ?>
                </td>
                <td data-title="Tipo de mensagem">
                    <?php echo $object->getTipoDesc(); ?>
                </td>
                <td data-title="Usuario">
                    <?php echo $object->getUsuario()->getNome() ?>
                </td>
                <td data-title="Só leitura">
                    <?php echo $object->getSomenteLeituraDesc() ?>
                </td>
                <td data-title="Ordem">
                    <?php echo $object->getOrdem() ?>
                </td>
                <td data-title="Visualizar prévia e detalhes">
                    <a href="<?php echo $config['routes']['ver-detalhes'] . '?id=' . $object->getId() ?>" target="_self">
                        Visualizar
                    </a>
                </td>
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