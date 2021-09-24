<?php
use QPress\Template\Widget;
?>

<h3>Atenção!</h3>
<p>Algumas variações podem conter valor de venda diferenciados.</p>
<p>Se atente ao valor do item no carrinho de compras.</p>

<div id="variacao">
    <div class="grade">

        <table class="table table- table-striped table-bordered">

            <?php
            /**
             * Busca todas as descrições das variações agrupados pela descrição do atributo e monta 1 ou 2 arrays
             * conforme a quantidade de atributos.
             * Se o produto possuir apenas 1 atributo, geramos 1 array com o nome "Quantidade".
             * Ex.:
             * "Cor" => Preto, Vermelho, Amarelo
             * "Tamanho" => P, M, G, GG
             */
            $collProdutoVariacaoAtributo = ProdutoVariacaoAtributoQuery::create()
                ->groupByDescricao()
                ->groupByProdutoAtributoId()
                ->useProdutoAtributoQuery()
                ->orderByOrdem()
                ->endUse()
                ->useProdutoVariacaoQuery()
                ->filterByProduto($objProduto)
                ->endUse()
                ->find()
            ;

            $opcoes = array();

            /* @var $objProdutoVariacaoAtributo ProdutoVariacaoAtributo */
            foreach ($collProdutoVariacaoAtributo as $objProdutoVariacaoAtributo) :
                $opcoes[ $objProdutoVariacaoAtributo->getProdutoAtributoId() ][ $objProdutoVariacaoAtributo->getDescricao() ] = $objProdutoVariacaoAtributo;
            endforeach;

            /**
             * Se houver apenas 1 atributo, a coluna da matriz ficará com o campo de quantidade sem atributo.
             */
            if (count($opcoes) == 1) {
                $atributoLinha = array_shift($opcoes);
                $atributoColuna = array('Quantidade' => null);
            }
            /**
             * Se hourer 2 atributos, é montada uma matriz N x N sendo que o atributo que possuir mais opções
             * será renderizado na linha da matriz.
             */
            elseif (count($opcoes) == 2) {
                $atributoLinha = array_shift($opcoes);
                $atributoColuna = array_shift($opcoes);
                if (count($atributoColuna) > count($atributoLinha)) {
                    list ($atributoColuna, $atributoLinha) = array($atributoLinha, $atributoColuna);
                }
            }

            ?>
            <thead>
            <tr>
                <th></th>
                <?php // Gera o cabeçalho da coluna ?>
                <?php foreach ($atributoColuna as $descricao => $objProdutoVariacaoAtributo) : ?>
                    <th class="text-center">
                        <?php
                        if ($objProdutoVariacaoAtributo instanceof ProdutoVariacaoAtributo && $objProdutoVariacaoAtributo->getProdutoAtributo()->isCor()) {
                            ?>
                            <div class="box-color">
                                <?php echo $objProdutoVariacaoAtributo->getPropriedade()->getBoxColor(32, 32, false); ?>
                            </div>
                            <?php
                        } else {
                            echo "<b>$descricao</b>";
                        }
                        ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>

            <tbody>
            <?php // Abre as linhas da matriz ?>
            <?php foreach ($atributoLinha as $descricaoLinha => $objProdutoVariacaoAtributo) : ?>
                <tr>
                    <td class="text-center">
                        &nbsp;
                        <?php
                        if ($objProdutoVariacaoAtributo->getProdutoAtributo()->isCor()) {
                            ?>
                            <div class="box-color"><?php echo $objProdutoVariacaoAtributo->getPropriedade()->getBoxColor(32, 32, false); ?></div>
                            <?php
                        } else {
                            echo "<h3>$descricaoLinha</h3>";
                        }
                        ?>
                    </td>

                    <?php
                    // Insere as colunas da matriz para cada linha
                    foreach ($atributoColuna as $descricaoColuna => $objProdutoVariacaoAtributoColuna) :
                        $isDisponivel = true;

                        if (!is_null($objProdutoVariacaoAtributoColuna)) {
                            $produtoVariacaoId = ProdutoVariacaoPeer::retrieveByCombinacao(array($descricaoLinha, $descricaoColuna), $objProduto->getId());
                        } else {
                            $produtoVariacaoId = ProdutoVariacaoPeer::retrieveByCombinacao(array($descricaoLinha), $objProduto->getId());
                        }

                        if ($produtoVariacaoId == false) {
                            $isDisponivel = false;
                        }

                        $objProdutoVariacao = ProdutoVariacaoPeer::retrieveByPK($produtoVariacaoId);
                        if ($isDisponivel) {
                            $isDisponivel = $objProdutoVariacao->isDisponivel();
                        }

                        if ($isDisponivel) :
                            ?>
                            <td style="min-width: 70px;" data-title="<?php echo $descricaoColuna; ?>" class="text-center">
                                <small>
                                    <?php echo format_money($objProdutoVariacao->getValor(), 'R$&nbsp;') ?>
                                </small>
                                <?php
                                Widget::render('produto_variacao/input_quantidade', array(
                                    'objProdutoVariacao'    => $objProdutoVariacao,
                                    'inputType'             => 'text',
                                    'quantidade'            => '',
                                ));
                                ?>
                            </td>
                            <?php
                        else :
                            ?>
                            <td>
                                &nbsp;
                                <input type="text" disabled placeholder="X" class="text-center form-control input-sm">
                            </td>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
