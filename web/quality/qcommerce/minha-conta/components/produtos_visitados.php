<div id="visitados-table" class="carrinho-table">  
    <table>
        <thead>
            <tr>
                <td>Produto</td>
                <td>Valor Unitário</td>
                <td>Excluir</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arrVisitados as $key => $objVisitado) : ?>                        
                <?php if ($objVisitado->getProduto() instanceof Produto) : ?>
                    <tr>
                        
                        <?php $link = 'href="' . $objVisitado->getProduto()->getUrlDetalhes() . '" title="Clique para visitar o produto: ' . escape($objVisitado->getProduto()->getNome()) . '"'; ?>
                        
                        <td class="first">
                            
                            <div class="img" style="text-align:center;">
                                <a <?php echo $link ?>>
                                    <?php echo $objVisitado->getProduto()->getThumb("width=95&amp;height=100&amp;cropratio=0.95:1"); ?>
                                </a>
                            </div> <!-- /img -->

                            <div class="desc">
                                <h2>
                                    <a <?php echo $link ?>>
                                        <?php echo escape(resumo($objVisitado->getProduto()->getNome(), 100)); ?>
                                    </a>
                                </h2>
                                <p>Código: <?php echo escape($objVisitado->getProduto()->getReferencia()); ?></p>
                                <div class="txt">
                                    <a <?php echo $link ?>>
                                        <?php echo resumo($objVisitado->getProduto()->getDescricao(), 150); ?>
                                    </a>
                                </div> <!-- /txt -->
                            </div> <!-- /desc -->
                            
                        </td>
                        
                        <td class="valor">
                            R$ <?php echo ($objVisitado->getProduto()->isPromocao()) ? $objVisitado->getProduto()->getValorComDescontoFormatado() : $objVisitado->getProduto()->getValorFormatado(); ?>
                        </td>
                        <td>
                            <a href="<?php echo get_url_site() . '/minha-conta/visitados/?remove-visita=' . escape($objVisitado->getId()); ?>" class="btn btn-delete" title="Excluir Produto">
                                <span class="icon-remove"></span>
                            </a>
                        </td>

                    </tr>
                    
                <?php endif; ?>
                    
            <?php endforeach; ?>
        </tbody>
    </table>
</div> <!-- /visitados-table -->

