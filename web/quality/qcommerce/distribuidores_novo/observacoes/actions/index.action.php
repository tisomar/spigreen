<?php

if (!empty($_GET['filter'])) {
    $arrFilter = array_map('trim', $_GET['filter']);
} else {
    $arrFilter = array();
}

/* @var $query ClienteDistribuidorObservacaoQuery */
$query = ClienteDistribuidorObservacaoQuery::create()
            ->useClienteDistribuidorQuery()
                ->filterByCliente(ClientePeer::getClienteLogado())
            ->endUse()
            ->orderByDataCadastro(Criteria::DESC);

if (!empty($arrFilter['cliente'])) {
    $query->filterByClienteDistribuidorId($arrFilter['cliente']);
}

if (!empty($arrFilter['observacao'])) {
    $query->filterByObservacao("%{$arrFilter['observacao']}%", Criteria::LIKE);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pager = new QPropelPager($query, 'ClienteDistribuidorObservacaoPeer', 'doSelect', $page);

$breadcrumb = array('Observações' => '');
