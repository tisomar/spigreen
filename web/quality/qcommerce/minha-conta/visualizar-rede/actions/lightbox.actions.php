<?php

$clienteLogado = ClientePeer::getClienteLogado(true);
$gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
$htmlRede = $gerenciador->geraHTMLRede($clienteLogado, 'rede-clientes', true);
