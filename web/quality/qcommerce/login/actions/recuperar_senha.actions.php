<?php
if (ClientePeer::isAuthenticad()) {
    redirectTo($root_path . '/minha-conta/pedidos/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    $objCliente = ClientePeer::retrieveByEmail($email);

    if ($objCliente instanceof Cliente) {
        $objCliente->recoveryPassword();
        FlashMsg::success('As instruções de como recuperar sua senha foram enviadas para o e-mail: ' . escape($email));
        redirect('/login');
    } else {
        FlashMsg::danger('Infelizmente não encontramos seu e-mail em nosso sistema, por favor, tente digitá-lo novamente.');
    }
}
