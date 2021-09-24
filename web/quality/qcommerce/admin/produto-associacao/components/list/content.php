<?php
include __DIR__ . '/../../config/menu.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="javascript:void(0)"><i class="icon-list"></i> Associações criadas</a>
                        </li>
                        <li>
                            <a href="<?php echo $config['routes']['registration'] ?>"><i class="icon-plus-sign"></i> Nova associação</a>
                        </li>
                    </ul>
                </h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Nome da Associação</th>
                            <th>Disponível</th>
                            <th>Total de produtos associados</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($pager as $object) { /** @var $object AssociacaoProduto */
                            ?>
                            <tr>
                                <td data-title="Nome da Associação"><?php echo sprintf('[%sº] %s', $object->getOrdem(), $object->getNome()) ?></td>
                                <td data-title="Disponível"><?php echo get_toggle_option(AssociacaoProdutoPeer::getOMClass(), 'Disponivel', $object->getId(), $object->getDisponivel()); ?></td>
                                <td data-title="Total de produtos associados">
                                    <?php echo plural($object->countAssociacaoProdutoProdutos(), '<label class="label label-default">%s PRODUTO ASSOCIADO</label> ', '<label class="label label-default">%s PRODUTOS ASSOCIADOS</label>'); ?>
                                    <br>
                                    <a href="<?php echo get_url_admin() . '/produto-associacao-produtos/list/?context=' . $context . '&reference=' . $reference . '&associacao_id=' . $object->getId() ?>">
                                        [ ver produtos associados ]
                                    </a>
                                </td>
                                <td data-title="Ações" class="text-right">
                                    <a href="<?php echo $config['routes']['registration'] . '&id=' . $object->getId() ?>" class="btn btn-default">
                                        <i class="icon-edit"></i> Editar
                                    </a>
                                    <?php $urlDelete = delete(AssociacaoProdutoPeer::getOMClass(), $object->getId()); ?>
                                    <a class="btn btn-danger text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo $urlDelete ?>" data-action="delete" >
                                        <i class="icon-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php if (count($pager) == 0) : ?>
                            <tr>
                                <td colspan="4">
                                    Nenhuma associação criada. <a href="<?php echo $config['routes']['registration'] ?>">Desejo criar uma associação</a>.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?php echo $pager->showPaginacao(); ?>
    </div>
</div>
