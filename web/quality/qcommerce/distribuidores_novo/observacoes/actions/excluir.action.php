<?php
require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_POST['id'])) {
    $observacao = ClienteDistribuidorObservacaoQuery::create()->findPk($_POST['id']);
    if (!$observacao || $observacao->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    $observacao->delete();
    
    FlashMsg::sucesso('Observação excluída com sucesso.');
    
    redirect('/distribuidores/observacoes');
    exit;
}

redirect_404();
