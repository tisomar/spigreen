<?php use QPress\Template\Widget; ?>

<div class="row">
    <div class="col-xs-12 col-sm-4">
        <div data-content-id="produto_detalhe_galeria_fotos_<?php echo $objProduto->getId() ?>">
            <?php
            Widget::render('produto-detalhe/galeria-fotos', array(
                'objProduto' => $objProduto,
                'disableVerticalGallery' => true,
            ));
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8">
        <div class="row">
            <div class="col-sm-12">
                <h3><?php echo escape($objProduto->getNome()); ?></h3>
            </div>
        </div>
        <div>
            <?php
            if ($objProduto->hasVariacoes()) {
                Widget::render('produto-detalhe/variacao-block', array(
                    'objProdutoDetalhe' => $objProduto,
                ));
            } else {
                echo resumo($objProduto->getDescricao(), 400);
                Widget::render('produto_variacao/input_quantidade', array(
                    'objProdutoVariacao'    => $objProduto->getProdutoVariacao(),
                    'inputType'             => 'hidden'
                ));
            }
            ?>
        </div>
        <div data-content-id="disponibilidade_<?php echo $objProduto->getId() ?>" class="text-danger"></div>
    </div>
</div>
<hr>
<script>
    $(function() {
        console.info($('[name*="quantidade_pv"]'));
    });
</script>