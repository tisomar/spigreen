<?php

/* @var $object ConfiguracaoPontuacaoMensal */

$_class = ConfiguracaoPontuacaoMensalPeer::OM_CLASS;

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$preQuery = ConfiguracaoPontuacaoMensalQuery::create()
    ->filterById(1);

$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';


$object = $query_builder->findOneOrCreate();


if ($container->getRequest()->getMethod() == 'POST') {
    $postData = trata_post_array($request->request->get('data'));

    if ($postData['TIPO_AVISO_2'] == '1') {
        $postData['DIA_AVISO_2'] = $postData['DIA_AVISO_11'];
    } elseif ($postData['TIPO_AVISO_2'] == '2') {
        $postData['DIA_AVISO_2'] = $postData['DIA_AVISO_12'];
    }


    $object->setByArray($postData);
    $object->save();
}

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
