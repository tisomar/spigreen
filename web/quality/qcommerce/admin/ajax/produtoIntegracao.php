<?php

//Incluindo classes do propel
include("../../includes/include_propel.inc.php");

$objIntegracao = new Integracao();
 
//Id do produto
$produtoId =  $_POST["produtoId"];

//Qual integração realizar ação {UOL, GOOGLE, BUSCAPE}
$tipo =  $_POST["tipo"];

//Qual ação realizar {INCLUIR, DELETAR}
$acao =  $_POST["acao"];

//Realizando ações
if ($acao == "INCLUIR") {
    $objIntegracao->incluiProduto($produtoId, $tipo);
} elseif ($acao == "DELETAR") {
    $objIntegracao->deletaProduto($produtoId, $tipo);
}
