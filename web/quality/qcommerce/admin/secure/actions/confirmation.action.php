<?php
require_once QCOMMERCE_DIR . '/admin//includes/config.inc.php';

$usuario = UsuarioQuery::create()->findOneByToken($router->getArgument(0));

if (!$usuario instanceof BaseUsuario) {
    $session->getFlashBag()->set('error', 'Este link não é mais válido.');
    $session->getFlashBag()->set('info', 'Caso tenha esquecido sua senha, re-faça o processo de recuperação de senha.');
    redirectTo(get_url_admin() . '/secure/login');
    exit;
}

if ($request->getMethod() == 'POST') {
    $error = UsuarioPeer::validateNewPassword($request->request->get('senha'), $request->request->get('senha2'));

    if (count($error) == 0) {
        $usuario->setSenha($request->request->get('senha'))->setToken(null)->save();
        $session->getFlashBag()->add('success', 'Sua senha foi atualizada com sucesso.');
        redirectTo(get_url_admin() . '/secure/login');
        exit;
    } else {
        foreach ($error as $type => $message) {
            $session->getFlashBag()->set($type, $message);
        }
    }
}
