<?php

$objClienteDistribuidor = null;
if (!empty($_GET['id'])) {
    $objClienteDistribuidor = ClienteDistribuidorQuery::create()
        ->withColumn('(SELECT SUM(ati.valor) FROM qp1_distribuidor_evento ati WHERE ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID)', 'valor_total')
        ->withColumn('(select max(ati.DATA) from qp1_distribuidor_evento ati where ati.CLIENTE_DISTRIBUIDOR_ID = qp1_cliente_distribuidor.ID AND ati.STATUS = "FINALIZADO" AND (ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA IS NULL OR ati.DISTRIBUIDOR_TEMPLATE_ID_PERDA = 0))', 'ultima_compra')
        ->findPk($_GET['id']);
}
if (!$objClienteDistribuidor || $objClienteDistribuidor->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
    redirect_404();
}

$observacoesCliente = ClienteDistribuidorObservacaoQuery::create()
    ->filterByClienteDistribuidor($objClienteDistribuidor)
    ->orderById()
    ->find();

if (!empty($_POST['observacao'])) {
    $objObservacao = new ClienteDistribuidorObservacao();
    
    $arrObservacao = array_map('trim', $_POST['observacao']);
    
    $objObservacao->setByArray($arrObservacao);
    $objObservacao->setClienteDistribuidor($objClienteDistribuidor);
    
    if ($objObservacao->myValidate($erros) && !$erros) {
        $objObservacao->save();
        
        FlashMsg::sucesso('Observação adicionada com sucesso.');
        
        redirect('/distribuidores_novo/clientes/visualizacao?id=' . $objClienteDistribuidor->getid());
    }
}
