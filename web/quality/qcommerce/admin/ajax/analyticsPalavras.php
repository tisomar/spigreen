<?php
include("../includes/config.inc.php");
header('Content-Type: text/html; charset=UTF-8');
 
$objAnalytics = new Analytics($analyticsLogin, $analyticsPassword, $analyticsId);
$data = $objAnalytics->palavrasChave(5);

$saida .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">';
$saida .= '<tr>';
$saida .= '<td class="header">Palavras-chave</td>';
$saida .= '<td class="header center" width="70">Acessos</td>';
$saida .= '</tr>';

if (is_array($data)) {
    foreach ($data as $palavra => $valores) {
        $saida .= '<tr>';
        $saida .= '<td>' . $palavra . '</td>';
        $saida .= '<td class="center">' . $valores['ga:visits'] . '</td>';
        $saida .= '</tr>';
    }
}

$saida .= '</table>';

echo $saida;
