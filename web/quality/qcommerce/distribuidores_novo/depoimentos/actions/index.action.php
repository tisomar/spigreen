<?php

if (!empty($_GET['filter'])) {
    $arrFilter = array_map('trim', $_GET['filter']);
} else {
    $arrFilter = array();
}

ProdutoPeer::disableSoftDelete();

$query = DistribuidorDepoimentoQuery::create()
                ->filterByCliente(ClientePeer::getClienteLogado())
                ->orderByDataCadastro(Criteria::DESC);

if (!empty($arrFilter['cliente'])) {
    $query->filterByClienteDistribuidorId($arrFilter['cliente']);
}

if (!empty($arrFilter['produto'])) {
    $query->filterByProdutoId($arrFilter['produto']);
}

if (!empty($arrFilter['depoimento'])) {
    $query->filterByDepoimento("%{$arrFilter['depoimento']}%", Criteria::LIKE);
}

if (!empty($arrFilter['data'])) {
    $data = DateTime::createFromFormat('d/m/Y', $arrFilter['data']);
    if ($data) {
        $inicio = clone $data;
        $fim = clone $data;
        $inicio->setTime(0, 0, 0);
        $fim->setTime(23, 59, 59);
        $query->filterByDataCadastro($inicio, Criteria::GREATER_EQUAL);
        $query->filterByDataCadastro($fim, Criteria::LESS_EQUAL);
    } else {
        FlashMsg::erro('Data inválida.');
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pager = new QPropelPager($query, 'DistribuidorDepoimentoPeer', 'doSelect', $page);

$breadcrumb = array('Depoimentos' => '');
