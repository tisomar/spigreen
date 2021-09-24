<?php

$erros              = array();
$_classPeer         = $_class::PEER;

if ($request->getMethod() == 'POST') {
    $data = $request->request->get('data', array());

    if (count($data) > 0) {
        $data               = trata_post_array($request->request->get('data'));

        /** @var \QualityPress\QCommerce\Component\Association\Manager\AssociationManager $associationManager */
        $associationManager = $container['quality.association.manager.product'];

        foreach ($data as $productAssociableId) {
            $associationManager->addAssociatedObject($objAssociacao, ProdutoPeer::retrieveByPK($productAssociableId));
        }

        try {
            $objAssociacao->save();
            $link = "<a href='{$config['routes']['list']}'>clique aqui</a>";
            $session->getFlashBag()->add('success', sprintf('Os produtos foram associados com sucesso. Se você quiser acessar a lista de produto associados %s', $link));
            redirectTo($config['routes']['list']);
        } catch (Exception $e) {
            $session->getFlashBag()->add('error', 'Houve algum erro ao tentar associar os produtos.');
            $session->getFlashBag()->add('error', $e->getMessage());
        }
    } else {
        $session->getFlashBag()->add('error', 'Você precisa selecionar algum produto da lista para associá-lo ao produto do cadastro.');
    }
}

$notListThisProducts = array_column($objAssociacao->getAssociacaoProdutoProdutos()->toArray(), 'ProdutoId');
array_push($notListThisProducts, $reference);

$object_peer = 'ProdutoPeer';
$_classQuery = $classQueryName = 'ProdutoQuery';
$preQuery = $_classQuery::create()
    ->filterById($notListThisProducts, Criteria::NOT_IN);

include __DIR__ . '/../../actions/registration/filter.basic.action.query.php';

$rowsPerPage = 20;
$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, $rowsPerPage);
