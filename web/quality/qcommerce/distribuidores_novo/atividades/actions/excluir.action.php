<?php
require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_POST['id'])) {
    $evento = DistribuidorEventoQuery::create()->findPk($_POST['id']);
    if (!$evento || $evento->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    $evento->delete();
    
    FlashMsg::sucesso('Acompanhamento excluído com sucesso.');
    
    redirect('/distribuidores/eventos');
    exit;
}

redirect_404();
