<?php
use Dompdf\Dompdf;
use Doctrine\Common\Collections\Criteria;
use PFBC\Element\DateTime;

$_class = 'Cliente';

$preQuery = ClienteQuery::create()
    ->filterByVago(false)
    ->useEnderecoQuery()
        ->filterByEnderecoPrincipal(1)
        ->useCidadeQuery()
            ->orderByNome()
            ->useEstadoQuery()
            ->endUse()
        ->endUse()
    ->endUse()
    ->groupById()
    ->orderByCreatedAt(Criteria::DESC);

if (!empty($request->query->get('filter')['Estado'])) {
    $id = $request->query->get('filter')['Estado'];
    $preQuery = ClienteQuery::create()
        ->filterByVago(false)
        ->useEnderecoQuery()
            ->filterByEnderecoPrincipal(1)
            ->useCidadeQuery()
                ->orderByNome()
                ->useEstadoQuery()
                    ->filterById($id)
                ->endUse()
            ->endUse()
        ->endUse()
        ->groupById()
        ->orderByCreatedAt(Criteria::DESC);
}

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/clientes-por-cidade/filter.basic.action.query.php';

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
