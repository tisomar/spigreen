<?php if ($arrComentarios->count()) : ?>
    <ul class="list-secondary list-comments">
        <?php foreach ($arrComentarios as $key => $objComentario) : /* @var $objComentario ProdutoComentario */ ?>
        <li>
            <div class="pull-left">
                <span class="icon-star-small mask-icon">
            </div>
            <div class="comment pull-right">
                <h2 class="title-small">7.0 Eu recomendaria este produto</h2>
                <h3 class="subtitle title-mini clear-margin">
                    Avaliado em:  <?php echo escape($objComentario->getData('d/m/Y')); ?> às <?php echo escape($objComentario->getData('H:m')); ?>h  no produto : 
                    <?php if ($objComentario->getProduto() instanceof Produto) :?>
                        <a href="<?php echo $objComentario->getProduto()->getUrlDetalhes(); ?>">
                            <?php echo escape($objComentario->getProduto()->getNome()); ?>
                        </a>
                    <?php else : ?>
                        Este produto não está mais disponível
                    <?php endif; ?>
                </h3>
                <p class="clear-margin">
                    <strong>Status:
                    <?php
                    switch ($objComentario->getStatus()) {
                        case ProdutoComentario::STATUS_APROVADO:
                            echo 'Aprovado (Seu comentário está sendo exibido na página deste produto)';
                            break;
                        case ProdutoComentario::STATUS_PENDENTE:
                        case ProdutoComentario::STATUS_REPROVADO: // Não dizemos ao cliente que o comentário dele foi reprovado para evitar problemas
                        default:
                            echo 'Pendente (Os administradores estão avaliando seu comentário)';
                    } ?>
                    </strong>
                </p>
                <p> 
                    <?php echo nl2br(escape($objComentario->getDescricao())); ?>
                </p>
                <a href="<?php echo get_url_site() . '/minha-conta/avaliacoes/?remove-avaliacao=' . escape($objComentario->getId()); ?>" class="btn btn-rounded icon-close btn-delete-comment" title="Excluir avaliação"></a>
            </div>
            <?php /*
                <p>
                    <span class="title">Sua avaliação do produto: </span><?php echo escape(ProdutoComentarioPeer::getNotaDescricao($objComentario->getNota())); ?>
                    (<?php echo ($objComentario->getNota() == 1) ? $objComentario->getNota() .' estrela' : $objComentario->getNota() .' estrelas'; ?>)
                    <br />
                    <span class="title">Status: </span>
                </p>
 * */ ?>
            </li> <!-- /comentario -->
            
        <?php endforeach; ?>
    </ul>

    
<?php else : ?>
    <div id="info"> 
        Você não comentou produtos até o momento.
    </div>
    
<?php endif; ?>
