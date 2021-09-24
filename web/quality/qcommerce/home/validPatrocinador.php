<?php

if ($container->getRequest()->getMethod() == 'GET') :
    $cliente = ClientePeer::getClienteLogado();

    if (!empty($cliente)):
        FlashMsg::add('danger', "{$cliente->getPrimeiroNome()}, você já possui um cadastro!");

        redirect('/home');
    endif;

    $codigoPatrocinador = $container->getRequest()->query->get('codigo_patrocinador');

    if ($codigoPatrocinador) :
        //Se for informado um valor numerico, assume que foi informado o codigo do patrocinador, senão busca pelo e-mail.
        if (ctype_digit($codigoPatrocinador)) :
            $objPatrocinador = ClienteQuery::create()->findOneByChaveIndicacao($codigoPatrocinador);
        else :
            $objPatrocinador = ClienteQuery::create()->findOneByEmail($codigoPatrocinador);
        endif;

        if ($objPatrocinador && $objPatrocinador->isInTree()) :
            $container->getSession()->set('CODIGO_PATROCINADOR', $codigoPatrocinador);
            $container->getSession()->set('PATROCINADOR_CONFIRMADO', '1');
            $container->getSession()->set('PATROCINADOR_ID', $objPatrocinador->getId());
            FlashMsg::add('success', 'Patrocinador confirmado.');

            redirect('/cadastro');
        else :
            $container->getSession()->set('CODIGO_PATROCINADOR', '');
            $container->getSession()->set('PATROCINADOR_CONFIRMADO', '0');
            $container->getSession()->set('PATROCINADOR_ID', '');
            FlashMsg::add('danger', 'Patrocinador não encontrado.');
        endif;
    else :
        //O usuario confirmou o codigo de patrocinador em branco.
        //Neste caso vamos assumir que ele deseja que o patrocinador seja escolhido automaticamente. Vamos marcar o patrocinador como confirmado.
        $container->getSession()->set('CODIGO_PATROCINADOR', '');
        $container->getSession()->set('PATROCINADOR_ID', '');
        //ajuste: não devemos mais considerar "confirmado".
        //$container->getSession()->set('PATROCINADOR_CONFIRMADO', '1');
        $container->getSession()->set('PATROCINADOR_CONFIRMADO', '0');

        FlashMsg::add('danger', 'Patrocinador não encontrado.');
    endif;
elseif ($container->getRequest()->query->get('action') === 'delete') :
    $container->getSession()->set('CODIGO_PATROCINADOR', '');
    $container->getSession()->set('PATROCINADOR_CONFIRMADO', '0');
    $container->getSession()->set('PATROCINADOR_ID', '');
    FlashMsg::add('success', 'Patrocinador removido.');
endif;

$location = get_url_site();
header('Location: ' . $location);
exit();
