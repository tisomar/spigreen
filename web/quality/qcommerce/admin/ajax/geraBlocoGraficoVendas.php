<?php
 
include("../includes/config.inc.php");
ini_set('max_execution_time', '60');

function blocoGrafico($data, $objAnalytics, $anId)
{
    
    $strBloco = "";

    /* data do dia */
    $strDia = date("d/m", $data);
    $strData = date("Y-m-d", $data);
    //var_dump($strData);
    $strDia .= ";";
    
    /* cadastros no dia */
    $strCadastro = ClienteQuery::create()->filterByDataCadastro(array('min' => date("Y-m-d 00:00:00", $data), 'max' => date("Y-m-d 23:59:59", $data)))->find();
    $strCadastro = ($strCadastro->count() > 0) ? $strCadastro->count() : "0";
    $strCadastro .= ";";

    /* vendas em andamento */
    $strVendasAndamento = PedidoQuery::create()->filterByData($data)->filterBySituacao(Pedido::ANDAMENTO)->count();
    $strVendasAndamento = ($strVendasAndamento > 0) ? $strVendasAndamento : "0";
    $strVendasAndamento .= ";";

    /* vendas realizadas no dia */
    $strVendasRealizadas = PedidoQuery::create()->filterByData($data)->filterBySituacao(Pedido::FINALIZADO)->count();
    $strVendasRealizadas = ($strVendasRealizadas > 0) ? $strVendasRealizadas : "0";
    $strVendasRealizadas .= ";";

    /* volume de vendas cancelados */
    $strVendasCancelados = PedidoQuery::create()->filterByData($data)->filterBySituacao(Pedido::CANCELADO)->count();

    $strVendasCancelados = ($strVendasCancelados > 0) ? $strVendasCancelados : "0";

    $strBloco .= $strDia . $strCadastro . $strVendasAndamento . $strVendasRealizadas . $strVendasCancelados;

    return $strBloco;
}

$ultimoGrafico = GraficoQuery::create()->findOneByTipo('VENDAS');

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
    $ultimoGrafico->setTipo('VENDAS');
    $ultimoGrafico->save();

    echo $ultimoGrafico->getGraficoString();
} else {
    echo $ultimoGrafico->getGraficoString();
}
