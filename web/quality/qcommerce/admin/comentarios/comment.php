<?php $produtoComentario = ProdutoComentarioQuery::create()->findOneById($_GET['id']); ?>
<h3><?php echo $produtoComentario->getTitulo() ?></h3>
<blockquote>
    <p><?php echo $produtoComentario->getDescricao(); ?></p>
</blockquote>