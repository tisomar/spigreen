<?php

/* @var $container \QPress\Container\Container */

/**
 * Redireciona o usuário para a página de 404 se o módulo não estiver habilitado.
 */
if (Config::get('has_faq') == false) {
    redirect_404();
}

if ($container->getRequest()->getMethod() == 'POST') {
    try {
        // Verificação CSRF
//        \QPress\CSRF\NoCSRF::check('csrf_token', $container->getRequest()->request->all(), true, 60 * 10, false);

        // Continuação caso a verificação do CSRF esteja ok.
        $data = filter_var_array($container->getRequest()->request->all(), FILTER_SANITIZE_STRING);

        if ($data['nome'] == '') {
            FlashMsg::danger('O campo nome é obrigatório.');
        }

        if ($data['email'] == '') {
            FlashMsg::danger('O campo e-mail é obrigatório.');
        } elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) == false) {
            FlashMsg::danger('O e-mail informado é inválido.');
        }

        if ($data['pergunta'] == '') {
            FlashMsg::danger('O campo pergunta é obrigatório.');
        } elseif (!isset($data['pergunta'][5])) {
            FlashMsg::danger('A pergunta deve conter pelo menos 5 caracteres.');
        }

        if (FlashMsg::hasErros() == false) {
            $response = FaqPeer::insertQuestion($data);

            if ($response instanceof Faq) {
                \QPress\Mailing\Mailing::enviarPerguntaFaq($response);
                redirect('/perguntas-frequentes/duvida-enviada-com-sucesso');
            } else {
                $addError = function ($message) {
                    FlashMsg::danger($message);
                };

                array_map($addError, $response);
            }
        }
    } catch (Exception $e) {
        // CSRF attack detected
        FlashMsg::danger('Não foi possível enviar as informações. Por favor, tente novamente!');
    }
} else {
    if (ClientePeer::isAuthenticad()) {
        $container->getRequest()->request->set('nome', ClientePeer::getClienteLogado()->getNome());
        $container->getRequest()->request->set('email', ClientePeer::getClienteLogado()->getEmail());
    }
}



$query = FaqQuery::create();
$busca = "";
$termos = array();
/**
 * BUSCA
 */
if ($container->getRequest()->query->get('filter')) {
    $busca = filter_var(escape($container->getRequest()->query->get('filter'), FILTER_SANITIZE_STRING));

    $termos = explode(' ', $busca);

    $orderBy = array();
    $condition = array();
    $limit = count($termos);

    foreach ($termos as $i => $termo) {
        $clause['PERGUNTA'] = FaqPeer::PERGUNTA . " " . Criteria::LIKE . " '%" . $termo . "%'";
        $clause['RESPOSTA'] = FaqPeer::RESPOSTA . " " . Criteria::LIKE . " '%" . $termo . "%'";

        $condition[0][] = $name0 = 'cond' . $i;
        $condition[1][] = $name1 = 'cond' . ($i + $limit);
        $condition[2][] = $name2 = 'cond' . ($i + ($limit * 2));

        $query->condition($name0, $clause['PERGUNTA']);
        $query->condition($name1, $clause['PERGUNTA']);
        $query->condition($name2, $clause['RESPOSTA']);

        $orderBy[] = $clause['PERGUNTA'];
    }

    $query->addDescendingOrderByColumn('(' . implode(' AND ', $orderBy) . ')');

    foreach ($orderBy as $clause) {
        $query->addDescendingOrderByColumn('(' . $clause . ')');
    }

    $query->where($condition[0]);
    $query->orWhere($condition[1]);
    $query->orWhere($condition[2]);
}

/**
 * END -> BUSCA
 */

$query->filterByMostrar(true)
    ->filterByResposta(null, Criteria::NOT_EQUAL)
    ->orderByOrdem();

$page = is_numeric($router->getArgument(0)) ? $router->getArgument(0) : 1;

# caso esteja em uma paginação, desabilita a indexacao desta página
if ($page > 1) {
    $meta['noindex'] = false;
}

$collFaq = $query->paginate($page, 10);
