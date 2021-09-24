<form method="post" action="<?php echo $root_path; ?>/carrinho/actions/adicionar/" name="form-vc">

    <?php $arrProdutoVendaCasada = $objVendaCasada->getProdutoVendaCasadas(); ?>

    <?php foreach ($arrProdutoVendaCasada as $key => $objProdutoVendaCasada) : ?>
            <?php $objProduto = $objProdutoVendaCasada->getProduto(); ?>

            <div class="produto-vc-detalhe">

                    <!-- Nome do produto -->
                    <div class="titulo">
                        <a href="<?php echo $objProduto->getUrlDetalhes(); ?>" title="<?php echo escape($objProduto->getNome()); ?>">
                            <?php echo escape($objProduto->getNome()); ?>
                        </a>
                    </div>

                    <?php if (!is_empty($objProduto->getReferencia())) : ?>
                        <div class="subtitulo">Ref. <?php echo escape($objProduto->getReferencia()); ?></div>
                    <?php endif; ?>

                    <div class="preco"><?php echo $objProduto->exibePreco(); ?></div>

                    <div class="img">
                        <?php echo $objProduto->getThumb('width=375&height=500'); ?>
                    </div>

                    <?php if ($objProduto->hasVariacoes()) : ?>
                            <?php include __DIR__ . '/../layouts/' . $objProduto->getModelo()->getLayout()->getArquivo() . '-vc.php'; ?>
                    <?php endif ?>

            </div>
            <?php if (($arrProdutoVendaCasada->count() - 1) !== $key) : ?>
                <div class="produto-vc-detalhe-ico-mais"></div>
            <?php endif ?>
            <input type="hidden" name="variacao[<?php echo $objProduto->getId(); ?>]" id="variacao_<?php echo $objProduto->getId(); ?>" value="" />

    <?php endforeach; ?>

    

    <input type="hidden" name="venda_casada" value="<?php echo $args[1]; ?>" />

    <a href="javascript:history.back()" class="btn btn-large">Retornar ao produto</a>

    <button type="submit" class="btn btn-large btn-primary">Adicionar ao carrinho</button>

</form>
