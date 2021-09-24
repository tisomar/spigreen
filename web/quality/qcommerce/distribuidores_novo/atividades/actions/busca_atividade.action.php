<?php

$evento = DistribuidorEventoQuery::create()
            ->filterByCliente(ClientePeer::getClienteLogado())
            ->filterById($_GET['id'])
            ->findOne();

$resultado['CLIENTE_ID'] = $evento->getClienteDistribuidor()->getId();
$resultado['ASSUNTO'] = $evento->getAssunto();
$resultado['INTERESSE'] = $evento->getInteresse();
$resultado['DATA'] = $evento->getData('d/m/Y');
$resultado['DESCRICAO'] = $evento->getDescricao();
$resultado['VALOR'] = $evento->getValor();
$resultado['DESCRICAO_SMS'] = $evento->getDescricaoEmail();
$resultado['DESCRICAO_EMAIL'] = $evento->getDescricaoSms();

echo json_encode($resultado);
