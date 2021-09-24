<?php
    require_once __DIR__ . '/../../../includes/security.php';

if (!empty($_POST['id'])) {
    $cliente = ClienteDistribuidorQuery::create()->findPk($_POST['id']);
    if (!$cliente || $cliente->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
    $cliente->delete();

    FlashMsg::sucesso('Cliente excluído com sucesso.');

    redirect('/distribuidores_novo/clientes');
    exit;
}

    redirect_404();
