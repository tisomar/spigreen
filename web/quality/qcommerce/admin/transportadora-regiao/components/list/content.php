<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CEP</th>
                <th>Observação</th>
                <th>Ativo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object TransportadoraRegiao */
                ?>
                <tr>
                    <td data-title="Região"><?php echo $object->getNome(); ?></td>
                    <td data-title="CEP"><?php echo format_cep($object->getCepInicial()); ?> &agrave; <?php echo format_cep($object->getCepFinal()); ?></td>
                    <td data-title="Observação"><?php echo $object->getObservacao(); ?></td>
                    <td data-title="Ativo?"><?php echo get_toggle_option($_class, 'IsAtivo', $object->getId(), $object->getIsAtivo()); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>">
                                        <span class="icon-edit"></span>
                                        Editar
                                    </a>
                                </li>
                                <li><a title="Gerenciar faixas de peso"
                                       href="<?php echo get_url_admin() ?>/faixa-peso/list/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>">
                                        <i class="icon-list"></i>
                                        Gerenciar faixas de peso
                                    </a>
                                </li>
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
