<?php

$documentsClientes = DocumentoAlertaClientesQuery::create()
    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
    ->useDocumentoAlertaQuery()
    ->filterByCancelada(false)
    ->endUse()
    ->addDescendingOrderByColumn(DocumentoAlertaClientesPeer::DATA_CRIACAO, Criteria::DESC)
    ->paginate($container->getRequest()->query->get('page', 1));
