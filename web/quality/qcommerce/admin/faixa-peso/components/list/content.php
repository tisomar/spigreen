<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Peso</th>
            <th>Valor (R$)</th>
            <th>Prazo</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object TransportadoraFaixaPeso */
            ?>
            <tr>
                <td data-title="Peso">a partir de <b><?php echo $object->getPeso(); ?></b> gramas</td>
                <td data-title="Valor (R$)"><b><?php echo $object->getValorFormatado(); ?></b>
                    <?php echo $object->getTipo() == TransportadoraFaixaPesoPeer::TIPO_PORCENTAGEM ? ' (do valor do pedido)' : ($object->getTipo() == TransportadoraFaixaPesoPeer::TIPO_PRECO_FIXO ? ' (valor fixo)' : ' (por kg)') ?>
                </td>
                <td data-title="Prazo">entrega em até <b><?php echo $object->getPrazoEntrega(); ?></b> dias</td>
                <td data-title="Ações" class="text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '&id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                            <li class="divider"></li>
                            <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" title="Ver na loja"><i class="icon-trash"></i> Excluir</a></li>
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
                    Nenhum registro disponível.</td>
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
