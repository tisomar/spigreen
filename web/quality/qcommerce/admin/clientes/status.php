<?php
$objCliente = ClienteQuery::create()->findOneById($request->query->get('id'));

if ($objCliente instanceof Cliente) {
    $status = $request->query->get('status');
    $objCliente->setStatus($status);

    switch ($status) {
        case ClientePeer::STATUS_APROVADO:
            $objCliente->setMotivoReprovacao(null);
            $session->getFlashBag()->add('success', 'O cadastro foi aprovado com sucesso!');
            break;

        case ClientePeer::STATUS_REPROVADO:
            $objCliente->setMotivoReprovacao($container->getRequest()->query->get('motivo'));
            $session->getFlashBag()->add('success', 'O cadastro foi reprovado com sucesso!');
            break;

        case ClientePeer::STATUS_PENDENTE:
            $objCliente->setMotivoReprovacao(null);
            $session->getFlashBag()->add('success', 'O cadastro foi bloqueado com sucesso!');
            break;
    }

    $objCliente->save();

    \QPress\Mailing\Mailing::enviarAvisoStatusCliente($objCliente);
}

redirect('/admin/clientes/registration/?id=' . $container->getRequest()->query->get('id'));
