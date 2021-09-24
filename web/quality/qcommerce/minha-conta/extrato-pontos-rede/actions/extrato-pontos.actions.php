<?php
$cliente = ClientePeer::getClienteLogado(true);
$con = Propel::getConnection();

$dtInicio = null;
if ($inicio = $request->query->get('inicio')) :
    $dtInicio = DateTime::createFromFormat('d/m/Y', $inicio);
    if (!$dtInicio) :
        FlashMsg::danger('Data inicial é inválida.');
    else :
        $dtInicio->setTime(0, 0, 0);
    endif;
endif;

$dtFim = null;
if ($fim = $request->query->get('fim')) :
    $dtFim = DateTime::createFromFormat('d/m/Y', $fim);

    if (!$dtFim) :
        FlashMsg::danger('Data final é inválida.');
    else :
        $dtFim->setTime(23, 59, 59);
    endif;
endif;

$dtPagamentoInicio = null;
if ($pagamentoInicio = $request->query->get('pagamento-inicio')) :
    $dtPagamentoInicio = DateTime::createFromFormat('d/m/Y', $pagamentoInicio);
    if (!$dtPagamentoInicio) :
        FlashMsg::danger('Data de pagamento inicial é inválida.');
    else :
        $dtPagamentoInicio->setTime(0, 0, 0);
    endif;
endif;

$dtPagamentoFim = null;
if ($pagamentoFim = $request->query->get('pagamento-fim')) :
    $dtPagamentoFim = DateTime::createFromFormat('d/m/Y', $pagamentoFim);

    if (!$dtPagamentoFim) :
        FlashMsg::danger('Data de pagamento final é inválida.');
    else :
        $dtPagamentoFim->setTime(23, 59, 59);
    endif;
endif;

$listaClientes = [];
$maxGeracao = 0;

try {
    $clienteId = $cliente->getId();

    $page = (int)$router->getArgument(0);
    if ($page < 1) :
        $page = 1;
    endif;

    $limit = 20;
    $offset = ($page - 1) * $limit;

    $filtros = '';

    /** Filtros que vem do index */
    if ($dtInicio != null) :
        $filtros .= " AND p.CREATED_AT >= '{$dtInicio->format('Y-m-d H:i:s')}'";
    endif;

    if ($dtFim != null) :
        $filtros .= " AND p.CREATED_AT <= '{$dtFim->format('Y-m-d H:i:s')}'";
    endif;

    if ($dtPagamentoInicio != null) :
        $filtros .= " AND ph.UPDATED_AT >= '{$dtPagamentoInicio->format('Y-m-d H:i:s')}'";
    endif;

    if ($dtPagamentoFim != null) :
        $filtros .= " AND ph.UPDATED_AT <= '{$dtPagamentoFim->format('Y-m-d H:i:s')}'";
    endif;

    $selectedCliente = $request->query->get('cliente', '');
    if (!empty($selectedCliente) && $cli = ClientePeer::retrieveByPK($selectedCliente)) :
        $filtros .= " AND {$cli->getTreeLeft()} <= c2.tree_left AND {$cli->getTreeRight()} >= c2.tree_right";
    endif;

    $descricaoPontos = $request->query->get('descricaoPontos');
    switch ($descricaoPontos) :
        case 'PP':
            $filtros .= " AND (
                c1.ID = c2.ID
                OR (pr.PLANO_ID IS NULL AND (
                    p.HOTSITE_CLIENTE_ID = c1.ID
                    OR (EXISTS (
                        SELECT 1
                        FROM qp1_extrato_cliente_preferencial e
                        WHERE e.PEDIDO_ID = p.ID
                    ) AND c2.INDICADOR_ID = c1.ID)
                ))
            )";
            break;
        case 'PH':
            $filtros .= " AND pr.PLANO_ID IS NULL AND p.HOTSITE_CLIENTE_ID IS NOT NULL";
            break;
        break;
        case 'PA':
            $filtros .= " AND pr.PLANO_ID IS NOT NULL AND c2.ID <> c1.ID";
            break;
        case 'PR':
            $filtros .= " AND pr.PLANO_ID IS NULL AND c2.ID <> c1.ID AND
                (p.HOTSITE_CLIENTE_ID IS NULL OR p.HOTSITE_CLIENTE_ID <> c1.ID)
                AND (NOT EXISTS (
                    SELECT 1
                    FROM qp1_extrato_cliente_preferencial e
                    WHERE e.PEDIDO_ID = p.ID
                ) OR c2.INDICADOR_ID <> c1.ID)
            ";
            break;
        case 'PCP':
            $filtros .= " AND pr.PLANO_ID IS NULL AND c2.ID <> c1.ID AND EXISTS (
                    SELECT 1
                    FROM qp1_extrato_cliente_preferencial e
                    WHERE e.PEDIDO_ID = p.ID
                )";
            break;
    endswitch;

    $filtroGeracao = (int)$request->query->get('geracao', '');
    if (is_numeric($filtroGeracao)) :
        $filtros .= " AND c2.tree_level - c1.tree_level = {$filtroGeracao}";
    endif;

    $mainSql = "
        SELECT {columns}
        FROM qp1_pedido p,
             qp1_cliente c1,
             qp1_cliente c2,
             qp1_pedido_status_historico ph,
             qp1_pedido_item pi,
             qp1_produto_variacao pv,
             qp1_produto pr
        WHERE p.STATUS <> 'CANCELADO'
          AND p.CLIENTE_ID = c2.ID
          AND c1.tree_left <= c2.tree_left
          AND c1.tree_right >= c2.tree_right
          AND c1.ID = {$cliente->getId()}
          AND ph.PEDIDO_ID = p.ID
          AND ph.PEDIDO_STATUS_ID = 1
          AND ph.IS_CONCLUIDO = 1
          AND pi.PEDIDO_ID = p.ID
          AND pi.PLANO_ID IS NULL
          AND pi.PRODUTO_VARIACAO_ID = pv.ID
          AND pv.PRODUTO_ID = pr.ID
          {$filtros}";

    $sql = str_replace('{columns}', "
        SUM(pi.VALOR_PONTOS_UNITARIO * pi.QUANTIDADE) PONTOS,
        IF(c2.CNPJ IS NULL, c2.NOME, c2.RAZAO_SOCIAL) CLIENTE,
        c2.tree_level - c1.tree_level GERACAO,
        p.ID PEDIDO,
        p.created_at DATA_PEDIDO,
        ph.updated_at DATA_PAGAMENTO,
        CASE
            WHEN c2.tree_level = c1.tree_level THEN 'PP'
            WHEN pr.PLANO_ID IS NOT NULL THEN 'PA'
            WHEN EXISTS (SELECT 1 FROM qp1_extrato_cliente_preferencial e WHERE e.PEDIDO_ID = p.ID) THEN 'PCP'
            WHEN p.HOTSITE_CLIENTE_ID IS NOT NULL THEN 'PH'
            ELSE 'PR'
        END DESCRICAO", $mainSql) . "
        GROUP BY p.ID, pr.PLANO_ID
        ORDER BY p.CREATED_AT DESC
        LIMIT {$limit}
        OFFSET {$offset}";

    $sqlCount = str_replace('{columns}', "COUNT(DISTINCT CONCAT_WS('-', p.ID, pr.PLANO_ID)) TOTAL", $mainSql);

    $queryClientes = ClienteQuery::create()
        ->filterByTreeLeft($cliente->getTreeLeft(), Criteria::GREATER_EQUAL)
        ->filterByTreeRight($cliente->getTreeRight(), Criteria::LESS_EQUAL)
        ->filterByVago(0)
        ->orderByNomeRazaoSocial()
        ->find();

    foreach ($queryClientes as $cli) :
        $listaClientes[$cli->getId()] = $cli->getNomeCompleto();
        $maxGeracao = max($maxGeracao, $cli->getTreeLevel() - $cliente->getTreeLevel());
    endforeach;

    $con = Propel::getConnection();
    $query = $con->prepare($sql);
    $query->execute();

    $list = $query->fetchAll(PDO::FETCH_ASSOC);

    $sqlTotal = str_replace(
        '{columns}',
        'SUM(pi.VALOR_PONTOS_UNITARIO * pi.QUANTIDADE) TOTAL_PONTOS',
        $mainSql
    );

    $query = $con->prepare($sqlTotal);
    $query->execute();

    $totalPontosPeriodo = (int) $query->fetch(PDO::FETCH_ASSOC)['TOTAL_PONTOS'] ?? 0;

    $gerenciador = new GerenciadorPontosAcumulados($con = Propel::getConnection(), $logger);
    $totalPontosUtilizados = $gerenciador->getTotalPontosResgatadosResgatePremiacao($cliente);
    $totalPontosRetiradosAdmin = $gerenciador->getPontuacaoRetiradaAdmin($cliente);
    $totalPontosAcumulados = $gerenciador->getTotalPontosAcumuladosCliente($cliente) - $totalPontosUtilizados - $totalPontosRetiradosAdmin;

    $queryCount = $con->prepare($sqlCount);
    $queryCount->execute();

    $countResults = (int) $queryCount->fetch(PDO::FETCH_ASSOC)['TOTAL'] ?? 0;

    $firstPage = 1;
    $lastPage = (int) ceil($countResults / $limit);

    $prevPage = max($page - 1, $firstPage);
    $nextPage = min($page + 1, $lastPage);

    $minPage = max($firstPage, $page - 2);
    $maxPage = min($lastPage, $page + 2);
} catch (\PropelException $e) {
    $logger->error($e->getMessage());
    echo $e->getMessage();
    die;
}

$listaPontosDescricao = [
    'PP' => 'Pontos Pessoais',
    'PH' => 'Pontos de Loja Online',
    'PA' => 'Pontos de Adesão de Kit',
    'PR' => 'Pontos de Recompra',
    'PCP' => 'Pontos de Clientes Preferenciais',
];
