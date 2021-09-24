<?php
$cliente = ClientePeer::getClienteLogado(true);

$objPlano = $cliente->getPlano();

$gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);
$totalPontos = $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, null, null, 'INDICACAO_DIRETA');

//busca os pedidos que são pagamentos de mensalidade
$pedidosMensalidades = PedidoQuery::create()
                            ->filterByCliente($cliente)
                            ->filterByClassKey(PedidoPeer::CLASS_KEY_PEDIDO)
                            ->usePedidoItemQuery(null, Criteria::INNER_JOIN)
                                ->useProdutoVariacaoQuery(null, Criteria::INNER_JOIN)
                                    ->useProdutoQuery(null, Criteria::INNER_JOIN)
                                        ->filterByMensalidade(true)
                                    ->endUse()
                                ->endUse()
                            ->endUse()
                            ->groupById()
                            ->orderById(Criteria::DESC)
                            ->find();

$totalPatrociados = null;
if ($objPlano) {
    $gerenciadorRede = new GerenciadorRede(Propel::getConnection(), $logger);
    
    /* hotfix 2598: é para exibir o total de clientes que $cliente é patrocinador direto. Antes exibia o total de descendentes na arvore binaria */
    $totalPatrociados = $gerenciadorRede->getTotalPatrocinadosDiretos($cliente);
}

$resgateDesabilitado = (bool) Config::get('resgate.desabilitado');
