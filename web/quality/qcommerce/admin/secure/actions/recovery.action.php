<?php

require_once __DIR__ . '/../../includes/config.inc.php';

if ($request->getMethod() == 'POST') {
    $email = $request->request->get('login');
    $user = UsuarioQuery::create()->findOneByLogin($email);

    if ($user instanceof Usuario) {
        $user->initProccessRecoveryPassword();
        $session->getFlashBag()->set('success', 'As instruções de como gerar uma nova senha foram enviadas para <b>' . $user->getEmail() . '</b>');
    } else {
        $session->getFlashBag()->set('error', 'O login informado (' . $email . ') não foi encontrado.');
    }
} else {
    $session->getFlashBag()->set('info', ' Informe seu seu login de acesso que lhe enviaremos as instruções de como recuperar sua senha para o e-mail vinculado ao seu usuário.');
}
