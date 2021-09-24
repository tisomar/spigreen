<?php

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';

if ($request->getMethod() == 'POST') {
    $login = $request->request->get('login');
    $senha = $request->request->get('senha');
    
    $user = UsuarioPeer::retrieveByLoginSenha($login, $senha);

    if ($user instanceof Usuario) {
        $user->makeLogin();
        if ($redirect = $container->getSession()->get('admin.lastpage')) {
            $container->getSession()->remove('admin.lastpage');
        } else {
            $redirect = '/admin/dashboard/';
        }

        redirect($redirect);
    } else {
        $session->getFlashBag()->set('error', 'Credenciais inválidas.');
    }
}
