<?php
include_once '../../includes/include_propel.inc.php';

header("Content-type: application/json");
 
if (isset($_GET['faixaId']) && (is_numeric($_GET['faixaId']))) {
    $retorno = array();
    
    $objFaixaCep = FaixaCepPeer::retrieveByPK($_GET['faixaId']);
    
    if ($objFaixaCep instanceof FaixaCep) {
        $retorno['faixaInicial'] = format_cep($objFaixaCep->getFaixaInicialCep());
        $retorno['faixaFinal'] = format_cep($objFaixaCep->getFaixaFinalCep());
    }
    
    echo json_encode($retorno);
}
