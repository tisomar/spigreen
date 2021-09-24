<?php
/* @var $item PedidoItem */
/* @var $container \QPress\Container\Container */

$editable = isset($editable) && $editable == true;

switch (Config::get('produto.proporcao')) {

    case '1:1':
        $img = 'width=100&height=100&cropratio=1';
        break;

    case '4:3':
        $img = 'width=100&height=75&cropratio=1.333:1';
        break;

    case '3:4':
        $img = 'width=75&height=100&cropratio=0.75:1';
        break;

}

?>

<div class="table-vertical">
    <table class="table table-striped table-carrinho-produtos <?php echo $editable ? 'editable' : ''; ?>">

        <thead>
        <tr>
            <th class="text-left">Produtos</th>
            <th class="text-left">Qtde</th>
            <th class="text-right">Preço</th>
            <th class="text-right">Subtotal</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach($itens as $key => $item): ?>
            <?php
            $objProdutoVariacao     = $item->getProdutoVariacao();
            $objProduto             = $objProdutoVariacao->getProduto();

            $isTaxaCadastro = (bool)$objProduto->getTaxaCadastro();

            $attributtesLinkDelete = array(
                'data-action'   => 'delete',
                'href'          => get_url_site() . '/carrinho/actions/remover?id=' . $item->getId(),
                'class'         => 'product-remove remove-item',
                'title'         => 'Clique aqui para retirar este item do carrinho',
            );
            $linkToDelete = '<a ' . get_atributes_html($attributtesLinkDelete) . '>%s</a>';

            $produtosKit = null;

            if (!empty($objProduto->getPlanoId())):
                $produtosKit = $item->getPedido()->getPedidoItemsAll($objProduto->getPlanoId());
            endif;
            ?>
            <tr>
                <td data-title="Produtos">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-3">
                            <?php
                            echo $objProduto->getImagemPrincipal()->getThumb($img, array(
                                'class' => 'img-responsive img-thumbnail img-produto',
                            ));
                            ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-9">
                            <p><?php echo $objProdutoVariacao->getProdutoNomeCompleto("<br><br><i>", "</i>", "<br>") ?></p>
                            <?php if($objProduto->getMarcaId()): ?>
                                <p>Marca: <?php echo $objProduto->getMarca()->getNome(); ?></p>
                            <?php endif; ?>
                            <p>Pontos: <?php echo $objProduto->getValorPontos("<br><br><i>", "</i>", "<br>") ?></p>
                            <p class="small">Código: <?php echo $objProdutoVariacao->getSku() ? $objProdutoVariacao->getSku() : $objProduto->getSku() ?></p>
                            </br>
                            <?php if (!empty($produtosKit)): ?>
                                <strong>Produtos do kit</strong>

                                <?php foreach ($produtosKit as $itemKit): ?>
                                    <div>
                                        <?= $itemKit->getQuantidade() ?>
                                        <?= $itemKit->getProdutoVariacao()->getProdutoNomeCompleto() ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($editable && !$isTaxaCadastro): ?>
                        <div class="visible-xs visible-sm small" style="position: absolute; top: 5px; right: 5px;">
                            <?php echo sprintf($linkToDelete, '<span class="fa fa-trash fa-2x"></span>'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </td>
                <td data-title="Quantidade" class="quantity">
                    <?php if ($editable && !$isTaxaCadastro): ?>

                        <?php if ($item->getQuantidade() > 0): ?>

                            <div class="row">
                                <div class="col-xs-8 col-sm-6">
                                    <input
                                        autocomplete="off"
                                        type="number"
                                        data-item-id="<?php echo $item->getId(); ?>"
                                        min="1"
                                        title="Digite a quantidade desejada"
                                        name="item[<?php echo $item->getId(); ?>]"
                                        value="<?php echo $item->getQuantidade(); ?>"
                                        class="touch-spin product-qtd input-sm"
                                        />

                                    <div class="hidden-xs hidden-sm text-center small">
                                        <?php echo sprintf($linkToDelete, 'remover'); ?>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="text-left">
                                <p class="text-danger">
                                    <span class="<?php icon('exclamation-circle') ?>"></span> item indisponível no momento
                                </p>

                                <p class="">
                                    <?php if (Config::get('has_aviseme')): ?>
                                        <a data-lightbox="iframe"
                                           href="<?php echo get_url_site() . '/carrinho/produto-avise-me/?pvid=' . $item->getProdutoVariacaoId() ?>">
                                            adicionar à lista de desejos
                                        </a>
                                        ou
                                    <?php endif; ?>
                                    <?php echo sprintf($linkToDelete, 'remover'); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php echo $item->getQuantidade(); ?>x
                    <?php endif; ?>
                </td>
                <td data-title="Preço unitário" class="text-right">
                    <?php if ($objProdutoVariacao->isPromocao()): ?>
                        <del class="small text-muted"><small>R$&nbsp;</small><?php echo format_money($objProdutoVariacao->getValorBase()) ?></del>
                        <br class="hidden-xs hidden-sm">
                    <?php endif; ?>
                    <small>R$&nbsp;</small><?php echo format_money($item->getValorUnitario()) ?>
                </td>
                <td data-title="Preço total" class="text-right">
                    <span class="text-success h3">
                        <small>R$&nbsp;</small><?php echo format_money($item->getValorTotal()) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>