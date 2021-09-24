<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Possui resposta?</th>
                <th width="60%">Pergunta</th>
                <th>Disponivel no site</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Faq */
                ?>
                <tr class="<?php echo is_null($object->getResposta()) ? 'text-warning' : '' ?>">
                    <td data-title="Tem resposta?">
                        <?php if (is_null($object->getResposta())) : ?>
                            <h4><label class="label label-warning"><i class="icon-exclamation-sign"></i> NÃO</label></h4>
                        <?php else : ?>
                            <h4><label class="label label-success"><i class="icon-ok"></i> Sim</label></h4>
                            <?php if ($object->getDataResposta('d/m/Y') != '') :
                                ?><small>Respondido em: <?php echo $object->getDataResposta('d/m/Y') ?></small><?php
                            endif; ?>
                        <?php endif; ?>
                    </td>
                    <td data-title="Pergunta"><?php echo $object->getDataPergunta('d/m/Y') ?> &minus; <?php echo $object->getPergunta(); ?></td>
                    <td data-title="Disponível"><?php echo get_toggle_option($_class, 'Mostrar', $object->getId(), $object->getMostrar()); ?></td>
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
