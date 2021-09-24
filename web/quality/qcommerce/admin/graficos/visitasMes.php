<?php
include("../includes/config.inc.php");
include_once('ofc-library/open-flash-chart.php');

$line_1 = new line(2, '#8E929E', 'Visitas', 10);

$objAnalytics = new Analytics($analyticsLogin, $analyticsPassword, $analyticsId);
$data = $objAnalytics->dataVisitasMes();

$maior = 0;

$dias = array();
$visitas = array();

foreach ($data as $metric => $valores) {
    array_push($dias, $metric . "/" . date('Y'));
    array_push($visitas, $valores['ga:visits']);
}

for ($i = 0; $i < Count($dias); $i++) {
    $line_1->data[] = $visitas[$i];
        
    if ($visitas[$i] > $maior) {
        $maior = $visitas[$i];
    }
}


$g = new graph();
$g->title(utf8_encode('Visitas por mês (' . date('Y') . ')'), '{font-size: 10px;}');

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
$g->set_x_labels($dias);
//
// set the X axis to show every 2nd label:
//
$g->set_x_label_style(1, '#FFFFFF', 2, 1);
//
// and tick every second value:
//
//$g->set_x_axis_steps( 1 );

$g->bg_colour = "#FFFFFF";

//$g->set_y_format( '#val#' );
$g->set_num_decimals(2);
$g->set_is_fixed_num_decimals_forced(false);
$g->set_is_decimal_separator_comma(false);
$g->set_is_thousand_separator_disabled(false);
//


$g->set_y_max($maior);
$g->set_y_label_style('none');
//$g->y_label_steps( 10 );
//$g->set_y_legend( 'Visitas', 12, '#736AFF' );
echo $g->render();
