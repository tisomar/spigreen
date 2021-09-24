<?php
$objectTicket = TicketQuery::create()->findOneById($request->query->get('id'));


if ($objectTicket instanceof Ticket) {
    
    $status = $request->query->get('status');
    $objectTicket->setStatus($status);

    switch ($status) {
        case TicketPeer::STATUS_FINALIZADO:
            $session->getFlashBag()->add('success', 'O ticket foi finalizado com sucesso!');
            break;

        case TicketPeer::STATUS_EM_ANDAMENTO:
            $session->getFlashBag()->add('success', 'O ticket está em andamento!');
            break;
    }

    $objectTicket->save();
}

redirect('/admin/ticket/registration/?id=' . $container->getRequest()->query->get('id'));
