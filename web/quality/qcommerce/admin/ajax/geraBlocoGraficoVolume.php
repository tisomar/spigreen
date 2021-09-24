<?php
 
include("../includes/config.inc.php");
ini_set('max_execution_time', '60');

function blocoGrafico($data, $objAnalytics, $anId)
{
    $strBloco = "";

    /* data do dia */
    $strDia = date("d/m", $data);
    $strData = date("Y-m-d", $data);
    $strDia .= ";";
    
    $c = new Criteria();
    $c->addAsColumn('somaPedido', "SUM(" . PedidoPeer::VALOR_TOTAL . ")");
    $c->add(PedidoPeer::DATA, $data, Criteria::EQUAL);
    $soma = PedidoQuery::create(null, $c)->addSelfSelectColumns()->findOne();
    $soma = $soma ? round($soma->getVirtualColumn("somaPedido")) : '0';
    
    $soma .= ";";

    $queryPedido = PedidoQuery::create()
            ->filterBySituacao(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->filterByData($data)
            ->useHistoricoQuery()
                 ->filterBySituacaoId(2, Criteria::GREATER_EQUAL)
            ->endUse()
            ->clearSelectColumns()
            ->withColumn("SUM(" . PedidoPeer::VALOR_TOTAL . ")", 'somaPedidoPago')
            ->groupByData();
    
    $stmt = PedidoPeer::doSelectStmt($queryPedido);
    $row = $stmt->fetch();
    
    $somaPagos = !empty($row) ? $row['somaPedidoPago'] : '';
    
    $somaPagos = $somaPagos ? round($somaPagos) : '0';
    
    $strBloco .= $strDia . $soma . $somaPagos;

    return $strBloco;
}

$ultimoGrafico = GraficoQuery::create()->findOneByTipo('VOLUME');

$today2 = strtotime($ultimoGrafico ? $ultimoGrafico->getDataAtualizacao("Y-m-d") : '');
$today = strtotime(date("Y-m-d"));
$diff = round(abs($today - $today2) / 60 / 60 / 24);
$atualizarGrafico = $diff;

if (($atualizarGrafico) || (!count($ultimoGrafico))) {
    $grafStr = "";

    $countDiasMes = date("t");

    $countDiasMes = round(abs($countDiasMes / 2));

    $data = strtotime(date("Y-m-d"));

    for ($i = $countDiasMes; $i >= 0; $i--) {
        $dataTemp = strtotime("-$i days", $data);
        $grafStr .= blocoGrafico($dataTemp, $objAnalytics, $analyticsId) . "\n";
    }

    $ultimoGrafico = (!($ultimoGrafico instanceof Grafico)) ? new Grafico() : $ultimoGrafico;
    $ultimoGrafico->setGraficoString($grafStr);
    $ultimoGrafico->setTipo('VOLUME');
    $ultimoGrafico->save();

    echo $ultimoGrafico->getGraficoString();
} else {
    echo $ultimoGrafico->getGraficoString();
}
