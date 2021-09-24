<?php
require_once __DIR__ . '/../../../includes/security.php';
require __DIR__ . '/../includes/config.inc.php';

if (!empty($_POST['id'])) {
    $objMeta = DistribuidorTemplateQuery::create()->findPk($_POST['id']);
    if (!$objMeta || $objMeta->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    $objMeta->delete();

    FlashMsg::sucesso('Template excluído com sucesso.');
    redirect('/distribuidores_novo/modelos/' . $tipoTemplate);
    exit;
}

redirect_404();
