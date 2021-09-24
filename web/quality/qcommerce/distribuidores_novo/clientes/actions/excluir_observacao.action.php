<?php
require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_GET['id'])) {
    $observacao = ClienteDistribuidorObservacaoQuery::create()->findPk($_GET['id']);
    if (!$observacao || $observacao->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    
    $observacao->delete();
    
    FlashMsg::sucesso('Observação excluída com sucesso.');
    
    redirect('/distribuidores_novo/clientes/visualizacao?id=' . $observacao->getClienteDistribuidorId());
    exit;
}

redirect_404();
