<?php
$breadcrumb = array();

$produtoCriteria = ProdutoQuery::create();
$objHotsite = new Hotsite();
if ($router->getArgument(0) != '' && !is_numeric($router->getArgument(0))) {
    $franqueado = $router->getArgument(0);
            
    $objHotsite = HotsiteQuery::create()
        ->filterBySlug($franqueado)
        ->findOne();
    
    if (is_null($objHotsite)) {
        redirect_404();
    }

    $clienteHotsite = $objHotsite->getCliente();
    $clienteLogado = ClientePeer::getClienteLogado();

    $podeEntrarHotsite = (
        !$clienteLogado || (
            $clienteHotsite->getId() == $clienteLogado->getClienteIndicadorId() &&
            !$clienteLogado->getPlanoId()
        )
    ) && $clienteHotsite->getStatus() == 1
    && $clienteHotsite->getPlanoId();

    if ($podeEntrarHotsite) :
        // Vamos salvar o cliente do hotsite na sessão.
        // Caso o visitante atual contrate um plano, vamos usar este cliente como patrocinador.
        if ($clienteHotsite->isInTree()) :
            $container->getSession()->set('PATROCINADOR_HOTSITE_ID', $clienteHotsite->getId());
        endif;
        //Salva o slug na sessao. Caso o visitante finalize algum pedido, precisamos salvar o bonus do franqueado.
        $container->getSession()->set('fromFranqueado', true);
        $container->getSession()->set('slugFranqueado', $franqueado);
    else :
        $container->getSession()->remove('fromFranqueado');
        $container->getSession()->remove('slugFranqueado');
        $container->getSession()->remove('PATROCINADOR_HOTSITE_ID');

        if ($clienteHotsite->getPlanoId()) :
            FlashMsg::erro('Não foi possível entrar no hotsite porque você já é um distribuidor.');
        elseif ($clienteHotsite->getId() != $clienteLogado->getClienteIndicadorId()) :
            FlashMsg::erro('Não foi possível entrar no hotsite de outro patrocinador.');
        endif;
    endif;
} else {
    FlashMsg::erro('Não foi possível encontrar o hotsite.');

    $container->getSession()->remove('fromFranqueado');
    $container->getSession()->remove('slugFranqueado');
    $container->getSession()->remove('PATROCINADOR_HOTSITE_ID');
}

redirect_home();
