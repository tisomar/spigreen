<?php
use QPress\Template\Widget;

$produtoVariacaoLayout = isset($produtoVariacaoLayout) ? $produtoVariacaoLayout : Config::PRODUTO_LAYOUT_VARIACAO_CAIXA;
?>

<div class="select-variations">

    <?php
    if ($objProdutoDetalhe->hasVariacoes()) {

        // Se for estiver configurado como Grade e não possuir exatamente 2 atributos para montar a matriz,
        // mantém-se o layout por caixa de seleção.
        if ($produtoVariacaoLayout == Config::PRODUTO_LAYOUT_VARIACAO_GRADE && $objProdutoDetalhe->countProdutoAtributos() > 2) {
            $produtoVariacaoLayout = Config::PRODUTO_LAYOUT_VARIACAO_CAIXA;
        }

        switch ($produtoVariacaoLayout) {

            case Config::PRODUTO_LAYOUT_VARIACAO_CAIXA:
            case Config::PRODUTO_LAYOUT_VARIACAO_AMBOS:

                $preSelectedOptions = array();
                $objProdutoVariacaoPadrao = null;
                if (Config::get('produto_variacao.selecao_automatica') != 0) {
                    $objProdutoVariacaoPadrao = $objProdutoDetalhe->getVariacaoPadrao();
                    if ($objProdutoVariacaoPadrao) {
                        $preSelectedOptions = array_column(
                            $objProdutoVariacaoPadrao->getProdutoVariacaoAtributos()->toArray(),
                            'Descricao', 'ProdutoAtributoId'
                        );
                    }
                }

                $arrayOpcoes = ProdutoVariacaoAtributoPeer::getOpcoesDisponiveisToArray($objProdutoDetalhe->getId());

                echo '<div class="row">';
                foreach ($arrayOpcoes as $opcao) {
                    QPress\Template\Widget::render(QCOMMERCE_DIR . '/produtos/components/layout-variacao/caixa.template.php', array(
                        'atributo' => $opcao,
                        'type' => $produtoVariacaoLayout,
                        'preSelectedOptions' => $preSelectedOptions,
                    ));
                }
                echo '</div>';

                $variacaoSelecionada = (!$objProdutoDetalhe->hasVariacoes() ? $objProdutoDetalhe->getProdutoVariacao()->getId() : ($objProdutoVariacaoPadrao ? $objProdutoVariacaoPadrao->getId() : NULL));

                if (Config::PRODUTO_LAYOUT_VARIACAO_AMBOS == $produtoVariacaoLayout) {
                    if ($objProdutoDetalhe->countProdutoAtributos() == 2) :
                        ?>
                        <div class="box-exibir-grade">
                            <div class="form-group">
                                <a
                                    data-lightbox="mpf-iframe"
                                    class='hidden-xs btn btn-default btn-block btn-sm mfp-popup'
                                    href="<?php echo get_url_site() ?>/produtos/grade-popup/?pid=<?php echo $objProdutoDetalhe->getId() ?>"
                                    title="Abrir grade de variações"
                                    >
                                    <span class="<?php icon('th') ?>"></span> Exibir grade
                                </a>
                            </div>
                            <hr>
                        </div>
                        <?php
                    endif;
                }

                if (!is_null($variacaoSelecionada)) {
                    $objProdutoVariacao = ProdutoVariacaoPeer::retrieveByPK($variacaoSelecionada);
                } else {
                    $objProdutoVariacao = new ProdutoVariacao();
                    $objProdutoVariacao->setProdutoId($objProdutoDetalhe->getId());
                }

//                Widget::render('produto_variacao/input_quantidade', array(
//                    'objProdutoVariacao'    => $objProdutoVariacao,
//                    'inputType'             => 'hidden'
//                ));

                break;

            case Config::PRODUTO_LAYOUT_VARIACAO_GRADE:

                Widget::render(QCOMMERCE_DIR . '/produtos/components/layout-variacao/grade.template.php', array(
                    'objProduto' => $objProdutoDetalhe,
                ));
                break;

        }
    }
    ?>
</div>