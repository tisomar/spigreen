<?php
require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_GET['id'])) {
    $objMeta = DistribuidorMetaVendaQuery::create()->findPk($_GET['id']);
    if (!$objMeta || $objMeta->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    $objMeta->delete();
    
    FlashMsg::sucesso('Meta de venda excluída com sucesso.');
    
    redirect('/distribuidores_novo/perfil/');
    exit;
}

redirect_404();
