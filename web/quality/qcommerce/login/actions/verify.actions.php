<?php

if ($container->getRequest()->query->has('redirecionar') && valida_url_redirecionamento(urldecode($container->getRequest()->query->get('redirecionar')))) {
    $redirect = get_url_caminho(urldecode($container->getRequest()->query->get('redirecionar')));
} else {
    $redirect = '/home';
}

//if (ClientePeer::isAuthenticad())
//{
//    redirectTo($root_path . $redirect);
//    exit;
//}

/**
 * Prevenindo contra CSRF na técnica Brute Force (por Post externo)
 * https://en.wikipedia.org/wiki/Cross-site_request_forgery
 *
 * Será gerado um token único e colocado na sessão, esse mesmo
 * token será utilizado no formulário de login
 *
 * Quando o formulário de login for enviado, então verificar-se-á
 * se o token que está na sessão é o mesmo token do formulário,
 * caso não for, então significa que a entrada foi criada externamente
 * e não por este servidor (Ex.: Um post externo de outro site).
 */
// Verificando se o token gerado para o formulário existe na sessão, se sim,
// se trata de um formulário genuíno criado por este site
// OBS.: Se o tempo de sessão expirar, o token não poderá ser validado e será
// apresentada uma mensagem de erro, solicitando para preencher novamente o formulário

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isValidToken = isset($_SESSION['tokens_csrf']['login']) && isset($_POST['token']) && $_SESSION['tokens_csrf']['login'] == $_POST['token'];
    $isValidToken = true;
    $isValidFields = isset($_POST['login-email']) && $_POST['login-senha'] && $isValidToken;

    if ($isValidFields) {
        $em = filter_var(trim($_POST['login-email']), FILTER_SANITIZE_STRING);
        $sh = filter_var(trim($_POST['login-senha']), FILTER_SANITIZE_STRING);
        $token = filter_var(trim($_POST['token']), FILTER_SANITIZE_STRING);

        if (empty($em) || empty($sh)) {
            FlashMsg::danger('Por favor, preencha as informações de acesso.');
        } else {
            $objCliente = ClientePeer::retrieveByLoginSenha($em, $sh);
            if ($objCliente instanceof Cliente) {
                if ($objCliente->getStatus() == ClientePeer::STATUS_APROVADO
                    || ($objCliente->getStatus() == ClientePeer::STATUS_PENDENTE && $objCliente->getTaxaCadastro())) {
                    unset($_SESSION['tokens_csrf']['login']);

                    if ($objCliente->getTabelaPrecoId()) {
                        $container  ->getCarrinhoProvider()
                                    ->getCarrinho()
                                    ->updatePedidoItemsByTabelaPrecoId($objCliente->getTabelaPrecoId());
                    }

                    ClientePeer::setClienteLogado($objCliente);
                    $container->getSession()->set('resellerLoggedActive', true);

                    if ($objCliente->getTaxaCadastro()) {
                        FlashMsg::warning('Para comprar você precisará ativar o cadastro pagando a taxa de cadastro.');
                    } elseif ($objCliente->getTipoConsumidor() > 0 && is_null($objCliente->getEnderecoPrincipal())) {
                        FlashMsg::warning('Cadastre seu endereço para darmos sequência no seu cadastro de revendedor');
                    }

                    $coll = CategoriaQuery::create()
                        ->_if(Config::get('mostrar_todas_categorias') == 0)
                        ->add('1', CategoriaPeer::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
                        ->addOr('2', CategoriaPeer::queryProdutosAtivos(), Criteria::CUSTOM)
                        ->_endif()
                        ->filterByCombo(true)
                        ->filterByParentDisponivel(true)
                        ->filterByDisponivel(true)
                        ->filterByNrLvl(array('min' => 1, 'max' => 2))
                        ->orderByNrLft()
                        ->findOne();

                    if ($coll) {
                        redirect('/produtos/' . $coll->getSlug() . '/');
                    } else {
                        redirect('/home/');
                    }
                } elseif ($objCliente->getStatus() == ClientePeer::STATUS_REPROVADO) {
                    FlashMsg::warning('Seu cadastro consta como reprovado em nosso sistema, por favor, entre em contato conosco para que possamos regularizar sua situação.');
                } elseif ($objCliente->getStatus() == ClientePeer::STATUS_PENDENTE) {
                    FlashMsg::warning('Seu cadastro está em processo de liberação, assim que liberado você será notificado.');
                } else {
                    FlashMsg::warning('Não conseguimos efetuar a autenticação com seu usuário. Por favor, entre em contato conosco solicitando a liberação.');
                }
            } else {
                FlashMsg::danger('E-mail ou senha inválidos.');
            }
        }
    } else {
        FlashMsg::danger('Envio de formulário inválido, provavelmente o seu tempo de preenchimento do formulário expirou, '
                . 'por favor, tente novamente.');
    }
} else {
    $franqueadoNoValid = false;
    $isNewReseller = false;


    if ($container->getSession()->has('resellerActive')
        || $container->getSession()->has('resellerLoggedActive')) {
        $isNewReseller = true;
    }

    if (!$isNewReseller && ClientePeer::isAuthenticad() && $container->getSession()->has('noFranqueado')) {
        //$franqueadoNoValid = true;
    }
}


// Criando token temporário e único para utilizar no formulário
$tokenFormularioLogin = base64_encode(sha1(time()));

// Colocando token em um array na sessão para poder identificar a validade depois do submit
$_SESSION['tokens_csrf']['login'] = $tokenFormularioLogin;
