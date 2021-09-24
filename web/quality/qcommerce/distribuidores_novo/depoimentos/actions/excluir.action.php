<?php
require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_POST['id'])) {
    $depoimento = DistribuidorDepoimentoQuery::create()->findPk($_POST['id']);
    if (!$depoimento || $depoimento->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    
    $depoimento->delete();
    
    FlashMsg::sucesso('Depoimento excluído com sucesso.');
    
    redirect('/distribuidores/depoimentos');
    exit;
}

redirect_404();
