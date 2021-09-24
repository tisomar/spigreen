<?php


$objCliente = ClienteQuery::create()->findOneById(ClientePeer::getClienteLogado()->getId());

/**
 * Quando a requisição for POST, valida as informações obtidas do formulário.
 * Caso veio da página de pré-cadastro, então não entra na condição
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = filter_var_array($_POST, FILTER_SANITIZE_STRING);
    
    if (empty($post['SENHA_ATUAL'])) {
        FlashMsg::danger('Você deve informar a sua Senha atual.');
    } else {
        if (ClientePeer::geraHashSenha($post['SENHA_ATUAL']) !== $objCliente->getSenha()) {
            FlashMsg::danger('A Senha atual está incorreta, por favor, tente digitá-la novamente.');
        }
    }

    // Como a função setSenha() gera o Hash, então é necessário verificar
    // manualmente a quantidade mínima de caracteres do campo senha
    if (strlen($post['SENHA']) < ClientePeer::SENHA_TAMANHO_MINIMO) {
        FlashMsg::danger('O campo Nova senha precisa ter pelo menos ' . ClientePeer::SENHA_TAMANHO_MINIMO . ' caracteres.');
    }

    if (empty($post['SENHA2'])) {
        FlashMsg::danger('O campo Confirmar nova senha é obrigatório.');
    } else {
        if ($post['SENHA'] != $post['SENHA2']) {
            FlashMsg::danger('O campo Nova senha e Confirmar nova senha devem ser iguais.');
        }
    }

    if (FlashMsg::hasErros() == false) {
        $objCliente->setSenha($post['SENHA']);
        $objCliente->save();
        
        FlashMsg::success('Sua senha foi alterada com sucesso.');

        redirectTo('/minha-conta/dados/sucesso-alterar-senha');
        
        exit;
    }
}
