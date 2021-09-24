<?php
$_class = 'Pedido';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$subquery = '(SELECT SUM(' . ExtratoPeer::PONTOS . ') FROM ' . ExtratoPeer::TABLE_NAME . ' WHERE ' . ExtratoPeer::OPERACAO . ' = "+" and ' . ExtratoPeer::PEDIDO_ID . ' = ' . PedidoPeer::ID . ')';

$preQuery = PedidoQuery::create()
    ->joinPedidoStatusHistorico()
    ->usePedidoStatusHistoricoQuery()
        ->filterByPedidoStatusId(2)
    ->endUse()
    ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
    ->withColumn($subquery, 'TotalPontos')
    ->orderByCreatedAt();


    
$query_builder  = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

if (!isset($filters['DataDe']) || !$filters['DataDe']) {
    $dataInicialPadrao = new \DateTime('first day of this month');

    if (!is_array($filters)) {
        $filters = array();
    }

    $filters['DataDe'] = $dataInicialPadrao->format('d/m/Y');
    $request->query->set('filter', $filters);
    $request->query->set('is_filter', true);
}

if (!isset($filters['DataAte']) || !$filters['DataAte']) {
    $dataFinalPadrao = new \DateTime('today');

    if (!is_array($filters)) {
        $filters = array();
    }

    $filters['DataAte'] = $dataFinalPadrao->format('d/m/Y');
    $request->query->set('filter', $filters);
    $request->query->set('is_filter', true);
}

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {

    $objects = $query_builder->find();

    // var_dump($objects);
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Pedido Id;Data;Cliente;Produtos;Qtd;Valor;Filial de entrega' . PHP_EOL;
    
    $dataInicial = DateTime::createFromFormat('d/m/Y H:i:s', $filters['DataDe'] . ' 00:00:00');
    $dataAtual = DateTime::createFromFormat('Y-m-d H:i:s', '2018-08-18 00:00:00');
    $totalCusto = 0;

    foreach ($objects as $obj) :
        $CD = '';
        switch($obj->getCentroDistribuicao()->getDescricao()) :
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
        $centroDistribuicao = $obj->getFrete() !== 'retirada_loja' ? ($obj->getCentroDistribuicao()->getDescricao() == 'Espírito Santo' ? 'Filial ES' : 'Filial MT') : $CD;

        $itensPedido = '';
        $itensPedidoKit = '';
        $quantidade = '';
        $qtdItens = count($obj->getPedidoItems());
        $contador = 0;
        $contadorItemsKit = 0;

        $custo = 0;

        foreach ($obj->getPedidoItems() as $objItemPedido):
            $itensPedido .= $objItemPedido->getProdutoVariacao()->getProduto()->getNome() . ' ( referência: ' . $objItemPedido->getProdutoVariacao()->getSku() . ' )';
            $contador++;
            if ($contador < $qtdItens) :
                $itensPedido .= ' | ';
            endif;

            // Items do kit
            if (!empty($objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId())):
                $planoId = $objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId();
                $produtosKit = $obj->getPedidoItemsAll($planoId);
                
                $checkEmpty = empty($produtosKit->toArray());
                if(!$checkEmpty) :
                    $qtdItensKit = count($produtosKit);
                    $itensPedidoKit .= ' | Itens do kit | ';
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
                            $itensPedidoKit .= ' | ';
                        endif;
                    endforeach;
                else:
                    $produtoId = $objItemPedido->getProdutoVariacao()->getProduto()->getId();
                    $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produtoId);
                    $qtdItensKit = count($arrProdutoCompostos);
                    $itensPedidoKit .= ' | Itens do kit | ';
                    foreach($arrProdutoCompostos as $productList) :
                        $itensPedidoKit .= $contadorItemsKit + 1 . ' - ' . $productList->getProdutoVariacao()->getProdutoNomeCompleto() . ' ( referência: ' . $productList->getProdutoVariacao()->getSku()  . ' )';
                        
                        $contadorItemsKit++;
                        if ($contadorItemsKit < $qtdItensKit) :
                            $itensPedidoKit .= ' | ';
                        endif;
                    endforeach;
                endif;
            endif;
        endforeach;

        $itensPedido .=  $itensPedidoKit;

        foreach ($obj->getPedidoItems() as $objItemPedido):
            $quantidade .=  $objItemPedido->getQuantidade() . 'x ( referência: ' . $objItemPedido->getProdutoVariacao()->getSku() . ' )';

            $quantidadeItems = '';
            $qtdItens = count($obj->getPedidoItems());
            $contador = 0;
            $contadorItemsKit = 0;

            $contador++;
            if ($contador < $qtdItens) :
                $quantidade .= ' | ';
            endif;

            // Items do kit
            if (!empty($objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId())):

                $planoId = $objItemPedido->getProdutoVariacao()->getProduto()->getPlanoId();
                $produtosKit = $obj->getPedidoItemsAll($planoId);
                
                $checkEmpty = empty($produtosKit->toArray());
                if(!$checkEmpty) :
                    $qtdItensKit = count($produtosKit);
                    $quantidadeItems .= ' | ';

                    foreach ( $produtosKit as $objPedidoItem ):
                        $variacao = $objPedidoItem->getProdutoVariacao();
                        $produto = $variacao->getProduto();

                        if($produto->getPlanoId() === null) :
                            $quantidadeItems .= $objPedidoItem->getQuantidade() . 'x  ( referência: ' . $variacao->getSku() . ' ) ';
                        endif;

                        $contadorItemsKit++;
                        if ($contadorItemsKit < $qtdItensKit) :
                            $quantidadeItems .= ' | ';
                        endif;
                    endforeach;
                else: 
                    $produtoId = $objItemPedido->getProdutoVariacao()->getProduto()->getId();
                    $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produtoId);
                    $qtdItensKit = count($arrProdutoCompostos);
                    $quantidadeItems .= ' | ';
                    foreach($arrProdutoCompostos as $productList) :
                        $quantidadeItems .= '1 x ( referência: ' . $productList->getProdutoVariacao()->getSku()  . ' )';

                        $contadorItemsKit++;
                        if ($contadorItemsKit < $qtdItensKit) :
                            $quantidadeItems .= ' | ';
                        endif;
                    endforeach;
                endif;
            endif;
        endforeach;

        $quantidade .= $quantidadeItems;

        $row = [
            '"' . $obj->getId() . '"',
            '"' . $obj->getCreatedAt('d/m/Y') . '"',
            '"' . $obj->getCliente()->getNomeCompleto() . '"',
            '"' . $itensPedido . '"',
            '"' . $quantidade . '"',
            '"' . formata_valor($obj->getValorTotal(), 2) . '"',
            '"' . $centroDistribuicao . '"'
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // $content = str_replace(',', ';', $objects->toCSV());
    
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);
    
    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);
    
    // Criando nome para o arquivo
    $filename = sprintf('pedidos_para_faturamento%s.csv', date('Y-m-d H-i-s'));
    
    // Definindo header de saída
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Expires: 0");
    header("Pragma: public");
    
    // Enviando headers para o browser
    $fp = fopen('php://output', 'w');
    fwrite($fp, $content);
    fclose($fp);
    exit();
}

$pager = $query_builder->find();

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$listaClientes = ClienteQuery::create()
    ->orderByNome()
    ->find()
    ->toArray();
