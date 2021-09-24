<?php
include("../includes/config.inc.php");

$objAnalytics = new Analytics($analyticsLogin, $analyticsPassword, $analyticsId);
$data = $objAnalytics->referencias(5);
 
$saida .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">';
$saida .= '<tr>';
$saida .= '<td class="header">P&aacute;ginas de Refer&ecirc;ncia</td>';
$saida .= '<td class="header center" width="70">Acessos</td>';
$saida .= '</tr>';

if (is_array($data)) {
    foreach ($data as $site => $valores) {
        $saida .= '<tr>';
        $saida .= '<td>' . $site . '</td>';
        $saida .= '<td class="center">' . $valores['ga:visits'] . '</td>';
        $saida .= '</tr>';
    }
}
$saida .= '</table>';

echo $saida;
