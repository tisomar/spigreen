<?php

include('../includes/config.inc.php');
include('../includes/security.inc.php');
 
if (isset($_GET['id']) && isset($_GET['valor']) && isset($_GET['campo'])) {
    $valor = filter_var(trim($_GET['valor']), FILTER_SANITIZE_STRING);
    
    $objOpcaoValor = OpcaoValorPeer::retrieveByPK($_GET['id']);
    
    if ($objOpcaoValor instanceof OpcaoValor) {
        if ($_GET['campo'] == 'nome') {
            $objOpcaoValor->setNome($valor);
        } elseif ($_GET['campo'] == 'nome_exibicao') {
            $objOpcaoValor->setNomeExibicao($valor);
        } elseif ($_GET['campo'] == 'ordem') {
            $objOpcaoValor->setOrdem($valor);
        } elseif ($_GET['campo'] == 'descricao') {
            $objOpcaoValor->setDescricao($valor);
        }
        $objOpcaoValor->save();
    }
}
