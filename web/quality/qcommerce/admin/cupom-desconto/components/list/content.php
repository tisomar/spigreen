<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Cupom</th>
                <th>Desconto</th>
                <th>Data de Inicio</th>
                <th>Data de Validade</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Cupom */
                ?>
                <tr class="<?php echo $object->isExpired() ? 'text-muted' : '' ?>">
                    <td data-title="Cupom">
                        <b><?php echo $object->getCupom(); ?></b>
                        <?php if ($object->isExpired()) : ?>
                            <br />
                            <i class="icon-ban-circle text-muted" title="Promoção finalizada"></i>
                            <em class="text-muted">promoção encerrada</em>
                        <?php elseif ($object->isActive()) : ?>
                            <br />
                            <i class="icon-time text-success" title="Promoção em andamento"></i>
                            <em class="text-success">promoção em andamento</em>
                        <?php endif; ?>
                    </td>
                    <td data-title="Desconto"><?php echo $object->getValorDescontoFormatado(); ?></td>
                    <td data-title="Início"><?php echo $object->getDataInicial('d/m/Y'); ?></td>
                    <td data-title="Fim"><?php echo $object->getDataFinal('d/m/Y'); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <li class="divider"></li>
                                <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete"><i class="icon-trash"></i> Excluir</a></li>
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
