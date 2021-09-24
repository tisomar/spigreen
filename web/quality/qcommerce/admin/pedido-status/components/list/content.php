<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Status Inicial</th>
                <th>Status Final</th>
                <th>Mensagem</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object PedidoStatus */
                ?>
                <tr>
                    <td data-title="Status Inicial"><?php echo $object->getLabelPreConfirmacao(); ?></td>
                    <td data-title="Status Final"><?php echo $object->getLabelPosConfirmacao(); ?></td>
                    <td data-title="Mensagem"><?php echo $object->getMensagem(); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
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
        <tfoot>
            <tr>
                <td colspan="20">
                    <?php //echo $pager->showPaginacao(); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
