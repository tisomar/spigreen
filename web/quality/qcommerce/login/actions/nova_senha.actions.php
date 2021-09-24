<?php
if (!$isCentral) {
    $token = $args[0];
    

    /* @var $objCliente Cliente */
    $cliente = ClienteQuery::create()->findOneByRecuperacaoSenhaToken($token);
    
    // Verifica se o token é válido
    if (is_null($cliente) || $cliente->isTokenRecuperacaoSenhaValido() == false) {
        FlashMsg::warning('Parece que o prazo para recuperar a sua senha expirou ou a recuperação da senha já foi feita.');
        redirect('/login');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hasError = false;

    $pass = filter_var_array($_POST['pass'], FILTER_SANITIZE_STRING);

    /**
     * Caso a alteração de senha seja pela central do cliente, o mesmo
     * deverá informar sua senha atual para informar uma nova senha.
     */
    if ($isCentral) {
        $cliente = ClientePeer::retrieveByLoginSenha(ClientePeer::getClienteLogado()->getEmail(), $pass['a']);

        if (!$cliente instanceof Cliente) {
            FlashMsg::danger('Sua senha atual está incorreta.');
            $hasError = true;
        }
    }

    // -----------------------------------------------------------------------

    if (!$hasError) {
        // Valida se a senha atingiu o quantidade mínima de caracteres
        if (strlen($pass['n']) >= ClientePeer::SENHA_TAMANHO_MINIMO) {
            // Valida se a senha de confirmação é igual à nova senha informada
            if ($pass['n'] == $pass['c']) {
                // Confirma a nova senha e invalida quaisquer solicitação de recuperação
                $cliente->setSenha($pass['n'])->save();
                $cliente->invalidaTokenRecuperacaoSenha();
                
                if ($isCentral) {
                    FlashMsg::success('Senha alterada com sucesso!');
                } else {
                    FlashMsg::success('Parabéns! A sua nova senha foi salva com sucesso. Utilize o formulário abaixo para acessar a sua conta.');
                    
                    if ($container->getCarrinhoProvider()->getCarrinho()->countItems() > 0) {
                        $redirecionar = '?redirecionar=' . get_url_site() . '/checkout/endereco/';
                    } else {
                        $redirecionar = '';
                    }

                    redirectTo(get_url_site() . '/login/' . $redirecionar);
                }
            } else {
                FlashMsg::danger('Por favor, a confirmação se senha está diferente da nova senha informada.');
            }
        } else {
            FlashMsg::danger('Por favor, sua nova senha deve conter no mínimo 6 caracteres.');
        }
    }
}
