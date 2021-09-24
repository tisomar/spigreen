<?php
include QCOMMERCE_DIR . "/includes/security.php";

if (($cliente = ClientePeer::getClienteLogado(true)) && !empty($_POST['lado'])) {
    $cliente->setLadoInsercaoCadastrados($_POST['lado']);
    $cliente->save();
    echo 'ok';
    exit;
}
