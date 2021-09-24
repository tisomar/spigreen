<?php
include("../includes/config.inc.php");
include_once('ofc-library/open-flash-chart.php');

$line_1 = new line(2, '#8E929E', 'Faturamento', 10);

//$objAnalytics = new Analytics($analyticsLogin, $analyticsPassword, $analyticsId);
//$data = $objAnalytics->dataVisitasDia();

$maior = 0;

$meses = array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
$faturamento = array();

for ($i = 1; $i <= 12; $i++) {
    $dataIni = mktime(0, 0, 0, $i, 1, date('Y'));
    $dataFim = mktime(0, 0, 0, $i, date('t', $dataIni), date('Y'));

    $arrPedidos = array();

    $c = new Criteria();
    $crit0 = $c->getNewCriterion(PedidoPeer::PEDI1_DAT, $dataIni, Criteria::GREATER_EQUAL);
    $crit1 = $c->getNewCriterion(PedidoPeer::PEDI1_DAT, $dataFim, Criteria::LESS_EQUAL);
    $crit0->addAnd($crit1);

    $c->add($crit0);
    $c->add(PedidoPeer::PEDI1_SIT, Pedido::FINALIZADO);

    $arrPedidos = PedidoPeer::doSelect($c);

    $valor = 0;

    foreach ($arrPedidos as $objPedido) {
        $valor += $objPedido->getValorTotal();
    }
    
    $line_1->data[] = $valor;

    //var_dump($valor);

    if ($valor > $maior) {
        $maior = $valor;
    }
}

$g = new graph();

$g->title(utf8_encode('Faturamento por mês (' . date('Y') . ')'), '{font-size: 20px;}');

//
// BAR CHART:
//
//
// ------------------------
//
$g->data_sets[] = $line_1;
//
// X axis tweeks:
//
$g->set_x_labels($meses);
//
// set the X axis to show every 2nd label:
//
$g->set_x_label_style(10, '#000000', 2, 1);
//
// and tick every second value:
//
$g->set_x_axis_steps(1);

$g->bg_colour = "#FFFFFF";

//$g->set_y_format( '#val#' );
$g->set_num_decimals(2);
$g->set_is_fixed_num_decimals_forced(true);
$g->set_is_decimal_separator_comma(false);
$g->set_is_thousand_separator_disabled(false);
//

$g->set_y_max($maior);
$g->set_y_label_style('none');
$g->y_label_steps(10);
$g->set_y_legend('Faturamento', 12, '#000000');
echo $g->render();
