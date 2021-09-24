<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Imagem</th>
            <th>Legenda</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Galeria */
            ?>
            <tr>
                <td data-title="Imagem"><a href="<?php echo $object->getUrlImageResize(''); ?>" class="open-in-modal" title="<?php echo $object->getLegenda() ?>">
                        <?php echo $object->getThumb('width=100&height=100', array('class' => 'thumbnail', 'style' => 'margin-bottom: 5px;')); ?>
                    </a></td>
                <td data-title="Legenda"><p><?php echo edit_inline($object->getLegenda(), get_class($object), 'Legenda', $object->getId()) ?>&nbsp;</p></td>
                <td data-title="Ações" class="text-right">
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
        if (count($pager->getResult()) == 0) {
            ?>
            <tr>
                <td colspan="20">
                    Nenhuma imagem cadastrada! <a href="<?php echo $config['routes']['registration'] ?>">Clique aqui</a> para adicionar as imagens</p>
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
