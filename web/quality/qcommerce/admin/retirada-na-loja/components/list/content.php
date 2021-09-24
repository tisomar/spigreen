<?php

use PFBC\Element;
?>

<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Loja</th>
                <th>Telefone</th>
                <th>Endereço</th>
                <th>Cidade</th>
                <th>Valor</th>
                <th>Prazo</th>
                <th>Disponível</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object RetiradaLoja */
                $cidade = $object->getCidade();
                ?>
                <tr>
                    <td data-title="Loja"><?php echo $object->getNome(); ?></td>
                    <td data-title="Telefone"><?php echo $object->getTelefone(); ?></td>
                    <td data-title="Endereço"><?php echo $object->getEndereco(); ?></td>
                    <td data-title="Cidade"><?php echo !is_null($cidade) ? $cidade->getNome() : ''; ?></td>
                    <td data-title="Valor (R$)"><?php echo format_money($object->getValor(), 'R$&nbsp;'); ?></td>
                    <td data-title="Prazo (dias úteis)"><?php echo $object->getPrazo(); ?> dias úteis</td>
                    <td data-title="Disponível"><?php echo get_toggle_option(get_class($object), 'Habilitado', $object->getId(), $object->getHabilitado()); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
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
