<?php

$clienteLogado = ClientePeer::getClienteLogado(true);

$dtInicio = null;
$dtFim = null;

if ($inicio = $request->query->get('inicio')) :
    $dtInicio = DateTime::createFromFormat('d/m/Y', $inicio);
    if (!$dtInicio) :
        FlashMsg::danger('Data inicial é inválida.');
    else :
        $dtInicio->setTime(0, 0, 0);
    endif;
endif;

if ($fim = $request->query->get('fim')) :
    $dtFim = DateTime::createFromFormat('d/m/Y', $fim);

    if (!$dtFim) :
        FlashMsg::danger('Data final é inválida.');
    else :
        $dtFim->setTime(23, 59, 59);
    endif;
endif;

try {
    $query = TicketQuery::create()
        ->filterByClienteId($clienteLogado->getId())
        ->orderByData(Criteria::DESC);
} catch (PropelException $e) {
    var_dump($e->getMessage());
}

if ($dtInicio) :
    $query->filterByData($dtInicio, Criteria::GREATER_EQUAL);
endif;

if ($dtFim) :
    $query->filterByData($dtFim, Criteria::LESS_EQUAL);
endif;

if($topico = $request->query->get('topico') && $request->query->get('topico') !== '') :
    $query->filterByCategoria($request->query->get('topico'));
endif;

$page = (int)$router->getArgument(0);

if ($page < 1) :
    $page = 1;
endif;

$pager = $query->paginate($page, 10);

$queryString = '';

if ($qs = $request->getQueryString()) :
    $queryString = '?' . $qs;
endif;

if ($container->getRequest()->getMethod() == 'POST'):

    $categoria = $request->request->get('categoria');
    $assunto = $request->request->get('assunto');
    $email = $request->request->get('email');
    $descricaoTicket = $request->request->get('descricao-ticket');
    $grupoId = $request->request->get('grupoId');

    $ticket = new Ticket();
    $ticket->setClienteId($clienteLogado->getId());
    $ticket->setCategoria($categoria);
    $ticket->setAssunto($assunto);
    $ticket->setGrupoId($grupoId);
    $ticket->setEmailDestino($email);
    $ticket->setDescricao($descricaoTicket);
    $ticket->setData(date('Y-m-d H:i:s'));
    $ticket->save();

    $ticketMensagem = new TicketMessages();
    $ticketMensagem->setTicketId($ticket->getId());
    $ticketMensagem->setRemetente('CLIENTE');
    $ticketMensagem->setRemetenteNome($clienteLogado->getNomeCompleto());
    $ticketMensagem->setMensagem($descricaoTicket);
    $ticketMensagem->setData(date('Y-m-d H:i:s'));
    $ticketMensagem->save();

    \QPress\Mailing\Mailing::send($email, $assunto, $descricaoTicket);

    if (FlashMsg::hasErros() == false) :
        try {
            FlashMsg::success('Ticket enviado com sucesso.');
            redirectTo('/minha-conta/ticket');
        } catch (Expection $e) {
            FlashMsg::danger('Algo de errado aconteceu ao tentarmos salvar seu endereço, por favor, tente novamente.');
        }
   endif;
endif;