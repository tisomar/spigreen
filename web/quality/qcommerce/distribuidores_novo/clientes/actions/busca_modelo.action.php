<?php

$modelo = DistribuidorTemplateQuery::create()
            ->filterByCliente(ClientePeer::getClienteLogado())
            ->filterById($_GET['id'])
            ->findOne();

echo json_encode($modelo->getMensagem());
