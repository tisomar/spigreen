<?php
//var_dump($_GET);die;
if (!empty($_GET['filter'])) {
    $arrFilter = array_map('trim', $_GET['filter']);
} else {
    $arrFilter = '';
}

    $query = ClienteDistribuidorQuery::create()
        ->filterByTipoLead('C', Criteria::NOT_EQUAL)
        ->addNomeCompletoColumn()
        ->filterByClienteRedefacilId(null)
        ->filterByEmail(null, Criteria::NOT_EQUAL)
        ->withColumn('(SELECT SUM(ati.valor) FROM qp1_distribuidor_evento ati WHERE ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.status = "FINALIZADO" AND (ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA IS NULL OR ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0))', 'valor_total')
        ->withColumn('(select IFNULL(count(*),0) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "FINALIZADO" AND (ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA IS NULL OR ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0) and ati.VALOR > 0) ', 'tem_compra')
        ->withColumn('(select IFNULL(count(*),0) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "FINALIZADO" AND (ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA IS NULL OR ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0)) ', 'tem_atividade')
        ->withColumn('(select IFNULL(count(*),0) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "ANDAMENTO") ', 'tem_agendamento')
        ->withColumn('(select max(ati.DATA) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "FINALIZADO" AND (ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA IS NULL OR ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0))', 'ultima_compra')
        ->filterByCliente(ClientePeer::getClienteLogado());


if (isset($arrFilter['Comprou']) && $arrFilter['Comprou'] >= 0) {
    if ($arrFilter['Comprou'] == 1) {
        $query->addHaving('tem_compra', 1, Criteria::GREATER_EQUAL);
    } elseif ($arrFilter['Comprou'] == 2) {
         $query->addHaving('tem_compra', 0, Criteria::EQUAL);
    } elseif ($arrFilter['Comprou'] == 3) {
        $query->addHaving('tem_agendamento', 1, Criteria::GREATER_EQUAL);
    } elseif ($arrFilter['Comprou'] == 4) {
         $query->addHaving('tem_agendamento', 0, Criteria::EQUAL);
    }
}
if (isset($arrFilter['Nome']) && !empty($arrFilter['Nome'])) {
    $query->addHaving('nome_razao_social', "%{$arrFilter['Nome']}%", Criteria::LIKE);
}

if (isset($arrFilter['DataIni']) && $arrFilter['DataIni'] != '' && isset($arrFilter['DataFim']) &&  $arrFilter['DataFim'] != '') {
    $dataInicial = DateTime::createFromFormat('d/m/Y', $arrFilter['DataIni'])->format('Y-m-d 00:00:00');
        $dataFinal = DateTime::createFromFormat('d/m/Y', $arrFilter['DataFim'])->format('Y-m-d 23:59:59');
        
        $query->withColumn('(select (case when(count(*) > 0) then "S" else "N" end) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "FINALIZADO" AND ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0 AND ati.DATA between "' . $dataInicial . '" AND "' . $dataFinal . '") ', 'periodo')
            ->addHaving('periodo', 'S', Criteria::EQUAL);
}
    $pager   = $query->find();
