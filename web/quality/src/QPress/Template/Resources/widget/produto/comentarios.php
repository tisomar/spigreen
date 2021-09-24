<?php
use QPress\Template\Widget;
$colComments = $objProduto->getProdutoComentarioAprovado();
?>
<div>
    <?php if ($colComments->count() > 0): ?>
        <ul class="list-comments list-unstyled">
            <?php foreach ($colComments as $comment): /* @var $comment ProdutoComentario */ ?>
                <li>
                    <?php
                    Widget::render('components/rating', array(
                        'size'      =>  'sm',
                        'value'     =>  $comment->getNota(),
                        'disabled'  =>  true
                    ));
                    ?>
                    <small class="">Avaliado em <?php echo $comment->getData('d/m/Y') ?></small>

                    <h3 class="h4">
                        <?php echo ProdutoComentarioPeer::getNotaDescricao($comment->getNota()) ?>
                    </h3>
                    <blockquote>
                        <p>"<?php echo $comment->getDescricao() ?>".</p>
                        <footer>Por <?php echo $comment->getNome() ?></footer>
                    </blockquote>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php else: ?>

        <h3><span class="<?php icon('frown-o'); ?>"></span> Nenhum comentário sobre o produto.</h3>
        <p>Conte o que achou deste produto para os outros clientes e ajude-os a comprar certo!</p>
    <?php endif; ?>

    <div class="row">
        <div class="col-xs-12 col-sm-3">
            <a href="<?php echo get_url_site() ?>/produtos/avalie/<?php echo $objProduto->getSlug() ?>?token_avaliacao=<?php echo urlencode($container->getRequest()->query->get('token_avaliacao', '')) ?>" class="_avaliacao btn btn-theme btn-block" data-lightbox="iframe" title="Avaliar este produto">Quero avaliar</a>
        </div>
    </div>

</div>