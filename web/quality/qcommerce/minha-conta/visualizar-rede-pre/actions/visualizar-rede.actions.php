<?php
$clienteLogado = ClientePeer::getClienteLogado(true);

$preCadastrado = PreCadastroClienteQuery::create()
    ->filterByConcluido(0)
    ->findOneByClienteId($clienteLogado->getId());

if (!$preCadastrado instanceof PreCadastroCliente && !$clienteLogado->isInTree()) {
    exit_403();
}

$gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
$htmlRede = $gerenciador->geraHTMLRede($clienteLogado, 'rede-clientes', true);

$plano = $clienteLogado->getPlano();

$gerenciadorBonus = new GerenciadorBonusRedeBinaria(Propel::getConnection(), $logger);
$totaisProximaDistribuicao = $gerenciadorBonus->getTotaisProximaDistribuicaoCliente($clienteLogado);

$pontosTotais = $totaisProximaDistribuicao['total'];
$pontosLadoEsquerdo = $totaisProximaDistribuicao['esquerda'];
$pontosLadoDireito = $totaisProximaDistribuicao['direita'];
