<?php
$clienteLogado = ClientePeer::getClienteLogado(true);

$preCadastrado = PreCadastroClienteQuery::create()
    ->filterByConcluido(0)
    ->findOneByClienteId($clienteLogado->getId());

if (!$preCadastrado instanceof PreCadastroCliente) {
    exit_403();
}

$gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
$htmlRede = $gerenciador->geraHTMLRede($clienteLogado, 'rede-clientes', true);
