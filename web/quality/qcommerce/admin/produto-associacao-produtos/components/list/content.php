<?php
use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoProdutoPeer;
?>
<?php include __DIR__ . '/../../config/menu.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert">
            <b>Produto:</b> <?php echo $objReference->getNome() ?> &raquo; <b>Associação:</b> <?php echo $objAssociacao->getNome() ?>
        </div>
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="javascript:void(0)"><i class="icon-list"></i> Produtos associados</a>
                        </li>
                        <li>
                            <a href="<?php echo $config['routes']['registration'] ?>"><i class="icon-plus-sign"></i> Associar novos produtos</a>
                        </li>
                    </ul>
                </h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th width="10%">Referência</th>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        /* @var $object AssociacaoProdutoProduto */
                        foreach ($pager as $object) {
                            ?>
                            <tr>
                                <td data-title="Referência"><?php echo $object->getProduto()->getProdutoVariacao()->getSku(); ?></td>
                                <td data-title="Produto"><?php echo $object->getProduto()->getNome(); ?></td>
                                <td data-title="Produto"><?php echo format_money($object->getProduto()->getValor(), 'R$&nbsp;'); ?></td>
                                <td data-title="Ações" class="text-right">
                                    <div class="btn-group">
                                        <?php $urlDelete = delete(AssociacaoProdutoProdutoPeer::getOMClass(), array('AssociacaoId' => $objAssociacao->getId(), 'ProdutoId' => $object->getProduto()->getId())); ?>
                                        <a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo $urlDelete ?>" data-action="delete" >
                                            <i class="icon-trash"></i> Excluir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php if (count($pager) == 0) : ?>
                            <tr>
                                <td colspan="4">
                                    Nenhum produto associado.
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
