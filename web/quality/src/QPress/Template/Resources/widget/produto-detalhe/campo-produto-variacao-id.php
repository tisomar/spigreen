<?php if ($objProduto->hasVariacoes() == false): ?>
    <input type="hidden" name="produto_variacao_id[<?php echo $objProduto->getId() ?>]" value="<?php echo $objProduto->getProdutoVariacao()->getId() ?>">
<?php elseif (Config::get('produto_variacao.selecao_automatica') != "" && $objProduto->getVariacaoPadrao()): ?>
    <input type="hidden" name="produto_variacao_id[<?php echo $objProduto->getId() ?>]" value="<?php echo $objProduto->getVariacaoPadrao()->getId() ?>">
<?php else: ?>
    <input type="hidden" name="produto_variacao_id[<?php echo $objProduto->getId() ?>]" value="">
<?php endif; ?>