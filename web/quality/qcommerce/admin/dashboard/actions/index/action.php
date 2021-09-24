<?php

$con = Propel::getConnection();

// Conta o número de pedidos feitos hoje
$countPedidos = PedidoQuery::create()
    ->filterByCreatedAt(array('min' => date('Y-m-d 00:00:00')))
    ->filterByClassKey(1)
    ->filterByStatus(PedidoPeer::STATUS_ANDAMENTO)
    ->count($con);

// Lista os pedidos das últimas 24 horas
$collPedidos = PedidoQuery::create()
    ->filterByCreatedAt(array('min' => date('Y-m-d H:i:s', strtotime('-24 hours'))))
    ->filterByClassKey(1)
    ->filterByStatus(PedidoPeer::STATUS_ANDAMENTO)
    ->orderById(Criteria::DESC)
    ->limit(5)
    ->find($con);

// Conta os cliente que se cadastraram hoje
$countClientes = ClienteQuery::create()
    ->filterByCreatedAt(array('min' => date('Y-m-d')))
    ->count($con);

// Lista os cliente cadastrados nas últimas 24 horas
$collClientes = ClienteQuery::create()
    ->filterByCreatedAt(array('min' => date('Y-m-d H:i:s', strtotime('-24 hours'))))
    ->orderById(Criteria::DESC)
    ->limit(5)
    ->find($con);

// Cria a estatistica de número de pedidos por status
$pedidoStatus = PedidoStatusQuery::create()
    ->select(array('Id', 'LabelPreConfirmacao'))
    ->filterByLabelPreConfirmacao('Aguardando Finalização', Criteria::NOT_EQUAL)
    ->orderByOrdem()
    ->find()
    ->toArray();

$countPedidoByStatus = array_fill_keys(array_column($pedidoStatus, 'LabelPreConfirmacao'), 0);
$countPedidoByStatus['Finalizado'] = 0;
$countPedidoByStatus['Cancelado'] = 0;

$idPedidoByStatus = array_column($pedidoStatus, 'Id', 'LabelPreConfirmacao');
$idPedidoByStatus['Finalizado'] = PedidoPeer::STATUS_FINALIZADO;
$idPedidoByStatus['Cancelado'] = PedidoPeer::STATUS_CANCELADO;

$sql = "
    SELECT PEDIDO_STATUS_ID, LABEL_PRE_CONFIRMACAO, COUNT(1) as TOTAL_PEDIDO FROM (

        SELECT
          p.ID as PEDIDO_ID
          , ps.ID as PEDIDO_STATUS_ID
          , LABEL_PRE_CONFIRMACAO
        FROM qp1_pedido p
        JOIN qp1_pedido_status_historico psh ON p.ID = psh.PEDIDO_ID AND psh.IS_CONCLUIDO = 0
        JOIN qp1_pedido_status ps ON psh.PEDIDO_STATUS_ID = ps.ID
        WHERE p.STATUS = 'ANDAMENTO'

        UNION

        SELECT
          p2.ID
          , CASE p2.STATUS WHEN 'FINALIZADO' THEN 98 WHEN 'CANCELADO' THEN 99 END as PEDIDO_STATUS_ID
          , CASE p2.STATUS  WHEN 'FINALIZADO' THEN 'Finalizado' WHEN 'CANCELADO' THEN 'Cancelado' END as LABEL_PRE_CONFIRMACAO
        FROM qp1_pedido p2
        WHERE p2.STATUS = 'FINALIZADO' OR p2.STATUS = 'CANCELADO'

    ) as pedido

    GROUP BY LABEL_PRE_CONFIRMACAO
    ORDER BY PEDIDO_STATUS_ID
";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
    $countPedidoByStatus[$rs->LABEL_PRE_CONFIRMACAO] = $rs->TOTAL_PEDIDO;
}

// Efetua a consulta dos pedidos e monta os totalizadores
//$sql = "
//    SELECT
//        MES,
//        ANO,
//        DIA,
//        SUM(QUANTIDADE_ITENS) as ITENS,
//        SUM(VALOR_TOTAL) as TOTAL,
//        SUM(VALOR_ENTREGA) as FRETE,
//        SUM(VALOR_DESCONTO_PAGAMENTO) as DESCONTOS,
//        COUNT(PEDIDO) as PEDIDOS
//    FROM (
//        SELECT
//           YEAR(p.CREATED_AT) as ANO
//            , MONTH(p.CREATED_AT) as MES
//            , DAY(p.CREATED_AT) as DIA
//            , HOUR(p.CREATED_AT) as HORA
//            , p.VALOR_ITENS + p.VALOR_ENTREGA - pfp.VALOR_DESCONTO as VALOR_TOTAL
//            , pfp.VALOR_DESCONTO as VALOR_DESCONTO_PAGAMENTO
//            , p.VALOR_ENTREGA
//            , SUM(ip.QUANTIDADE) as QUANTIDADE_ITENS
//            , p.ID as PEDIDO
//
//        FROM qp1_pedido p
//
//        JOIN qp1_pedido_item ip ON ip.PEDIDO_ID = p.ID
//
//        JOIN (
//            SELECT *
//            FROM qp1_pedido_status_historico
//            WHERE qp1_pedido_status_historico.PEDIDO_STATUS_ID = 1
//            AND qp1_pedido_status_historico.IS_CONCLUIDO = 1
//        ) as psh ON psh.PEDIDO_ID = p.ID
//
//        JOIN (
//            SELECT *
//            FROM (
//                SELECT *
//                FROM qp1_pedido_forma_pagamento
//                WHERE qp1_pedido_forma_pagamento.STATUS = 'APROVADO'
//                ORDER BY ID DESC
//            ) as qp1_pedido_forma_pagamento
//            GROUP BY qp1_pedido_forma_pagamento.PEDIDO_ID
//        ) as pfp ON pfp.PEDIDO_ID = p.ID
//
//        WHERE  p.CLASS_KEY = 1
//            AND p.STATUS <> 'CANCELADO'
//            AND p.CREATED_AT
//              BETWEEN '" . date('Y-m-d 00:00:00') . "'
//              AND '" . date('Y-m-d 23:59:59') . "'
//
//        GROUP BY p.ID
//
//        ORDER BY p.CREATED_AT DESC
//            , p.ID DESC
//            , psh.PEDIDO_STATUS_ID DESC
//    )as relatorio
//
//    GROUP BY ANO, MES, DIA
//
//    ORDER BY ANO, MES, DIA
//";
$startDate = new DateTime();
$startDate->setTime(0, 0, 0, 0);

$endDate = new DateTime();
$endDate->setTime(23, 59, 59, 999999);

$sql = "SELECT SUM(COALESCE(pfp.VALOR_PAGAMENTO, p.VALOR_ITENS + p.VALOR_ENTREGA)) as TOTAL,
               SUM(COALESCE(pfp.VALOR_PAGAMENTO, p.VALOR_ITENS + p.VALOR_ENTREGA) - COALESCE(pfp.VALOR_DESCONTO, 0) - IF (pfp.FORMA_PAGAMENTO IN ('PONTOS', 'BONUS_FRETE', 'PONTOS_CLIENTE_PREFERENCIAL'), pfp.VALOR_PAGAMENTO, 0)) as TOTAL_FATURADO
        FROM qp1_pedido p
        JOIN qp1_pedido_status_historico psh ON psh.PEDIDO_ID = p.ID AND psh.PEDIDO_STATUS_ID = 1 AND psh.IS_CONCLUIDO = 1
        JOIN qp1_pedido_forma_pagamento pfp ON p.ID = pfp.PEDIDO_ID AND pfp.STATUS = 'APROVADO'
        WHERE p.CLASS_KEY = 1
        AND p.STATUS <> 'CANCELADO'
        AND psh.UPDATED_AT BETWEEN '{$startDate->format('Y-m-d H:i:s:u')}' AND '{$endDate->format('Y-m-d H:i:s:u')}'
        GROUP BY p.ID";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

$totalizadores = array(
    'valor_total_venda' => 0,
    'valor_total_faturamento' => 0
);

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) :
    $totalizadores['valor_total_venda'] += $rs->TOTAL;
    $totalizadores['valor_total_faturamento'] += $rs->TOTAL_FATURADO;
endwhile;

// PERMISSÂO DE ACESSO AS INFORMACOES DE VENDA
$usuario = UsuarioPeer::getUsuarioLogado();
$countGrupos = PermissaoGrupoUsuarioQuery::create()
    ->filterByUsuarioId($usuario->getId())
    ->filterByGrupoId([6, 7, 8, 9], Criteria::NOT_IN) // Marketing e Logistica
    ->count();

$podeVisualizarEstatisticaVendas = $usuario->getId() == 1 || $countGrupos > 0;