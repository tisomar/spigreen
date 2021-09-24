<h1 class="h4 product-name">
    <?php echo escape($objProdutoVariacao->getProduto()->getNome()); ?>
    <br>
    <small><abbr title="Referência">Ref.:</abbr> <span class="update-referencia produto_<?php echo $objProdutoVariacao->getProdutoId() ?>"><?php echo $objProdutoVariacao->getSku() ?></span></small>
</h1>