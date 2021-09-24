<?php

/* @var $object Pedido */

$_classPeer = $_class::PEER;

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

ProdutoPeer::disableSoftDelete();
ProdutoVariacaoPeer::disableSoftDelete();
ProdutoVariacaoAtributoPeer::disableSoftDelete();


// Pesquisa todos os status
$allStatus = PedidoStatusQuery::create()->filterByFrete($object->getFrete())->orderByOrdem()->find();

// Se o usuario estiver no grupo de marketing
$usuario = UsuarioPeer::getUsuarioLogado();
$idGruposSelecionados = PermissaoGrupoUsuarioQuery::create()->select(array('GrupoId'))->filterByUsuarioId($usuario->getId())->find()->toArray();
$isMarketingGroup = false;
if(in_array(7, $idGruposSelecionados) || in_array(9, $idGruposSelecionados) || in_array(5, $idGruposSelecionados)) :
    $isMarketingGroup = true;
endif;

$isFinanceGroup = false;
if(in_array(6, $idGruposSelecionados) || in_array(1, $idGruposSelecionados)) :
    $isFinanceGroup = true;
endif;

// Listagem de pontos do pedido
$arrListaPontos = ExtratoQuery::create()
    ->filterByPedido($object)
    ->filterByOperacao('+')
    ->filterByPontos(0, Criteria::GREATER_THAN)
    ->filterByTipo(array(
        Extrato::TIPO_INDICACAO_DIRETA,
        Extrato::TIPO_INDICACAO_INDIRETA,
        Extrato::TIPO_RESIDUAL,
        Extrato::TIPO_VENDA_HOTSITE,
        Extrato::TIPO_BONUS_FRETE
    ))
    ->find();

$bonusProdutos = ExtratoBonusProdutosQuery::create()->filterByClienteId($object->getCliente()->getId())->filterByIsDistribuido(false)->find();