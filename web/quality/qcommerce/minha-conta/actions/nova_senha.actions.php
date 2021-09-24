<?php

if (isset($args[0])) {
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = filter_var_array($_POST['pass'], FILTER_SANITIZE_STRING);

    $cliente = ClientePeer::retrieveByLoginSenha(ClientePeer::getClienteLogado()->getEmail(), $pass['a']);

    if ($cliente instanceof Cliente) {
        if (strlen($pass['n']) >= 6) {
            if ($pass['n'] != $pass['a']) {
                if ($pass['n'] == $pass['c']) {
                    $cliente->setSenha($pass['n'])->save();
                    FlashMsg::success('Senha alterada com sucesso!');
                } else {
                    FlashMsg::danger('Por favor, a confirmação se senha está diferente da nova senha informada.');
                }
            } else {
                FlashMsg::danger('Por favor, informe uma nova senha diferente da senha atual.');
            }
        } else {
            FlashMsg::danger('Por favor, sua nova senha deve conter no mínimo 6 caracteres.');
        }
    } else {
        FlashMsg::danger('Sua senha atual está incorreta.');
    }
}
