<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Pedido Id</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Produtos</th>
            <th>Qtd</th>
            <th>Valor</th>
            <th>Filial de entrega</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $totalVendas = 0;
            $totalPontos = 0;
            $totalPedidos = 0;
            $totalCusto = 0;

            $dataInicial = DateTime::createFromFormat('d/m/Y H:i:s', $filters['DataDe'] . ' 00:00:00');
            $dataAtual = DateTime::createFromFormat('Y-m-d H:i:s', '2018-08-18 00:00:00');

            /* @var $object Pedido */
            foreach ($pager as $object) :  
                $CD = '';
                switch($object->getCentroDistribuicao()->getDescricao()) :
                    case 'Cuiabá':
                        $CD = 'Retirada em loja MT ( Cuiabá )';
                        break;
                    case 'Espírito Santo':
                        $CD = 'Retirada em loja ES';
                        break;
                    case 'Goiânia':
                        $CD = 'Retirada em loja GO ( Goiânia )';
                        break;
                endswitch;
                $centroDistribuicao = $object->getFrete() !== 'retirada_loja' ? ($object->getCentroDistribuicao()->getDescricao() == 'Espírito Santo' ? 'Filial ES' : 'Filial MT') : $CD;

                $totalVendas += $object->getValorTotal();
                $totalPontos += $object->getTotalPontos();
                $totalPedidos++;

            ?>
                <tr>
                    <td data-title="Pedido">
                        <?php echo $object->getId(); ?>
                    </td>
                    <td data-title="Data">
                        <?php echo $object->getCreatedAt('d/m/Y'); ?>
                    </td>
                    <td data-title="Nome" class="align-center">
                        <?php echo $object->getCliente()->getNomeCompleto() ?>
                    </td>                 
                    <td data-title="Produtos">
                        <?php
                        $itensPedido = '';
                        $itensPedidoKit = '';
                        $qtdItens = count($object->getPedidoItems());
                        $contador = 0;
                        $contadorItemsKit = 0;

                        $custo = 0;

                        foreach ($object->getPedidoItems() as $objItemPedido):

                            $itensPedido .= $objItemPedido->getProdutoVariacao()->getProduto()->getNome() . ' ( referência: ' . $objItemPedido->getProdutoVariacao()->getSku() . ' )';
                            $contador++;
                            if ($contador < $qtdItens) :
                                $itensPedido .= '<br />';
                            endif;

                            if ($dataInicial->getTimestamp() < $dataAtual->getTimestamp()):
                                $custo += (double)$objItemPedido->getProdutoVariacao()->getProduto()->getValorCusto() * $objItemPedido->getQuantidade();
                            else: 
                                $custo += (double)$objItemPedido->getValorCusto() * $objItemPedido->getQuantidade();
                            endif;

                            // Items do kit
                            if (!empty($objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId())):
                                $planoId = $objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId();
                                $produtosKit = $object->getPedidoItemsAll($planoId);
                                
                                $checkEmpty = empty($produtosKit->toArray());
                                if(!$checkEmpty) :
                                    $qtdItensKit = count($produtosKit);
                                    $itensPedidoKit .= '<br>Itens do kit<br>';
                                    foreach ( $produtosKit as $objPedidoItem):
                                        $variacao = $objPedidoItem->getProdutoVariacao();
                                        $produto = $variacao->getProduto();
        
                                        $variacao->getProdutoNomeCompleto();
                                        $variacao->getSku();
                                        $produto->getSku();
        
                                        if($produto->getPlanoId() === null) :
                                            $itensPedidoKit .= $contadorItemsKit + 1 . ' - ' .$variacao->getProdutoNomeCompleto() . ' ( referência: ' . $variacao->getSku() . ' ) ';
                                        endif;
        
                                        $contadorItemsKit++;
                                        if ($contadorItemsKit < $qtdItensKit) :
                                            $itensPedidoKit .= '<br />';
                                        endif;
                                    endforeach;
                                else:
                                    $produtoId = $objItemPedido->getProdutoVariacao()->getProduto()->getId();
                                    $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produtoId);
                                    $qtdItensKit = count($arrProdutoCompostos);
                                    $itensPedidoKit .= '<br>Itens do kit<br>';
                                    foreach($arrProdutoCompostos as $productList) :
                                        $itensPedidoKit .= $contadorItemsKit + 1 . ' - ' . $productList->getProdutoVariacao()->getProdutoNomeCompleto() . ' ( referência: ' . $productList->getProdutoVariacao()->getSku()  . ' )';
                                        
                                        $contadorItemsKit++;
                                        if ($contadorItemsKit < $qtdItensKit) :
                                            $itensPedidoKit .= '<br />';
                                        endif;
                                    endforeach;
                                endif;
                            endif;

                        endforeach;
                        $totalCusto += $custo;
                        echo $itensPedido . '  <br>' .  $itensPedidoKit;
                        ?>
                    </td>
                    
                    <td data-title="Quantidade">
                    <?php
                        $quantidade = '';
                        $quantidadeItems = '';
                        $qtdItens = count($object->getPedidoItems());
                        $contador = 0;
                        $contadorItemsKit = 0;

                        $catProduto = CategoriaPeer::retrieveByPK(3);

                        foreach ($object->getPedidoItems() as $objItemPedido):
                            $quantidade .=  $objItemPedido->getQuantidade() . 'x ( referência: ' . $objItemPedido->getProdutoVariacao()->getSku() . ' )';
                            $contador++;
                            if ($contador < $qtdItens) :
                                $quantidade .= '<br />';
                            endif;

                            // Items do kit
                            if (!empty($objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId())):

                                $planoId = $objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId();
                                $produtosKit = $object->getPedidoItemsAll($planoId);
                                
                                $checkEmpty = empty($produtosKit->toArray());
                                if(!$checkEmpty) :
                                    $qtdItensKit = count($produtosKit);
                                    $quantidadeItems .= '<br><br>';

                                    foreach ( $produtosKit as $objPedidoItem ):
                                        $variacao = $objPedidoItem->getProdutoVariacao();
                                        $produto = $variacao->getProduto();
        
                                        if($produto->getPlanoId() === null) :
                                            $quantidadeItems .= $objPedidoItem->getQuantidade() . 'x  ( referência: ' . $variacao->getSku() . ' ) ';
                                        endif;
        
                                        $contadorItemsKit++;
                                        if ($contadorItemsKit < $qtdItensKit) :
                                            $quantidadeItems .= '<br />';
                                        endif;
                                    endforeach;
                                else: 
                                    $produtoId = $objItemPedido->getProdutoVariacao()->getProduto()->getId();
                                    $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produtoId);
                                    $qtdItensKit = count($arrProdutoCompostos);
                                    $quantidadeItems .= '<br><br>';
                                    foreach($arrProdutoCompostos as $productList) :
                                        $quantidadeItems .= '1 x ( referência: ' . $productList->getProdutoVariacao()->getSku()  . ' )';

                                        $contadorItemsKit++;
                                        if ($contadorItemsKit < $qtdItensKit) :
                                            $quantidadeItems .= '<br />';
                                        endif;
                                    endforeach;
                                endif;
                            endif;
                        endforeach;
                        echo $quantidade . ' <br> ' . $quantidadeItems;
                        ?>
                    </td>
                    <td data-title="Valor">
                        R$ <?php echo formata_valor($object->getValorTotal(), 2); ?>
                    </td>
                    <td data-title="centroDistribuicao">
                        <?php echo $centroDistribuicao ?>
                    </td>
                </tr>               
            <?php endforeach;
            if ($pager->count() == 0): ?>
                <tr>
                    <td colspan="7">Nenhum registro disponível</td>
                </tr>
            <?php endif  ?>
        </tbody>
    </table>
</div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>