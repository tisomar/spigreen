<?php
$hasComparadorPreco = Config::get('has_google_shopping') || Config::get('has_buscape');
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="45%">Nome</th>
                <th width="15%">Valor de Venda</th>
                <th width="1%">Publicado?</th>
                <?php if ($hasComparadorPreco) : ?>
                    <th width="1%">Integrações</th>
                <?php endif; ?>
                <th width="1%"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            /* @var $object Produto */
            foreach ($pager->getResult() as $object) {
                $getProdutoTaxa = $object->getTaxaCadastro();
                ?>
                <tr>
                    <td data-title="Produto">
                        <a title="<?php echo escape($object->getNome()); ?>" href="<?php echo $object->getImagemPrincipal()->getUrlImage() ?>" class="open-in-modal text-muted"><i class="icon-camera"></i></a>
                        <span class="hidden-xs"><b>Referência: </b></span><?php echo escape($object->getSku()); ?>
                        <br />
                        <?php echo resumo(escape($object->getNome()), 100); ?>
                    </td>
                    <td data-title="Valor">
                        <?php
                        if ($object->getValorPromocional() > 0) {
                            echo '<span class="text-muted"><small>De R$ ', format_number($object->getValorBase()), '</small></span>';
                            echo '<br />Por ';
                        }
                        echo 'R$ ' . format_number($object->getValor());
                        ?>
                    </td>
                    <td data-title="Publicado?"><?php echo get_toggle_option('ProdutoVariacao', 'Disponivel', $object->getProdutoVariacao()->getId(), $object->getDisponivel()); ?></td>
                    <?php if ($hasComparadorPreco) : ?>
                        <td data-title="Integrações">
                            <ul class="list-unstyled">
                                <?php
                                if ($object->getItemGoogleShopping()) {
                                    echo ' <li><label class="label label-danger">Google Shop</label></li>';
                                }
                                if ($object->getBuscapeShoppingItem()) {
                                    echo ' <li><label class="label label-warning">Buscapé</label></li>';
                                }
                                ?>
                            </ul>
                        </td>
                    <?php endif; ?>

                    <td class="text-right" data-title="Ações">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <li class="divider"></li>
                                <li><a title="Gerenciar imagens"  href="<?php echo get_url_admin() ?>/pmidia/list/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>"><span class="icon-camera"></span> Fotos</a></li>
                                <?php if (!$getProdutoTaxa) : ?>
                                    <li><a title="Gerenciar atributos" href="<?php echo get_url_admin() ?>/produto-atributos/list/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>"><span class="icon-folder-open"></span> Atributos</a></li>
                                    <li><a title="Gerenciar variações" href="<?php echo get_url_admin() ?>/produto-variacoes/list/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>"><span class="icon-list"></span> Variações</a></li>
                                <?php endif; ?>
                                <?php if (Config::get('has_google_shopping')) : ?>
                                    <li><a title="Gerenciar Google Shopping" href="<?php echo get_url_admin() ?>/google-shopping/registration/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>"><span class="icon-shopping-cart"></span> GShop</a></li>
                                <?php endif; ?>
                                <?php if (Config::get('has_buscape')) : ?>
                                    <li><a title="Gerenciar Buscapé" href="<?php echo get_url_admin() ?>/buscape-company/registration/?context=<?php echo $_class ?>&reference=<?php echo $object->getId() ?>"><span class="icon-shopping-cart"></span> Buscapé</a></li>
                                <?php endif; ?>
                                <?php if (!$getProdutoTaxa) : ?>
                                    <li class="divider"></li>
                                    <li><a title="Ver na loja" href="<?php echo $object->getUrlDetalhes() ?>" title="Ver na loja" target="_blank"><i class="icon-external-link"></i> Ver na Loja</a></li>
                                    <li class="divider"></li>
                                    <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                                <?php endif; ?>
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
