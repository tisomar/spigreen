<?php

if ($container->getRequest()->query->has('redirecionar') && valida_url_redirecionamento(urldecode($container->getRequest()->query->get('redirecionar')))) {
    $redirect = get_url_caminho(urldecode($container->getRequest()->query->get('redirecionar')));
} else {
    $redirect = '/minha-conta/plano-carreira';
}

if (ClientePeer::isAuthenticad()) {
    redirectTo($root_path . $redirect);
    exit;
}

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

                    $temSlug = $container->getSession()->has('slugFranqueado') &&
                        !empty($container->getSession()->get('slugFranqueado'));

                    if ($temSlug) :
                        $franqueado = HotsiteQuery::create()
                            ->filterBySlug($container->getSession()->get('slugFranqueado'))
                            ->findOne();

                        /* @var $franqueado Hotsite */

                        $podeLogarHotsite = $franqueado &&
                            $franqueado->getCliente()->getId() == $objCliente->getClienteIndicadorId() &&
                            !$objCliente->getPlanoId();

                        if (!$podeLogarHotsite) :
                            $container->getSession()->remove('fromFranqueado');
                            $container->getSession()->remove('slugFranqueado');
                            $container->getSession()->remove('PATROCINADOR_HOTSITE_ID');
                        endif;
                    endif;

                    // SE O CLIENTE LOGADO TIVER PLANO, O REDIRECT VAI PARA PLANO DE CARREIRA, CASO CONTRARIO ELE É REDIRECIONADO PARA OS PEDIDOS
                    $clienteLogado = ClientePeer::getClienteLogado(true);
                    $RedirectCentralDistribuidor = $clienteLogado->getPlano() == null ? '/minha-conta/pedidos' : '/minha-conta/plano-carreira';

                    redirect($RedirectCentralDistribuidor);
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
}
// Criando token temporário e único para utilizar no formulário
$tokenFormularioLogin = base64_encode(sha1(time()));

// Colocando token em um array na sessão para poder identificar a validade depois do submit
$_SESSION['tokens_csrf']['login'] = $tokenFormularioLogin;
