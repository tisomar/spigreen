<?php

/* @var $object Pedido */

$_classPeer = $_class::PEER;

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

$graduacoes = PlanoCarreiraQuery::create()
->orderByNivel()
->find();

$graduacaoList = [];

foreach($graduacoes as $graduacao):
    $graduacaoList[$graduacao->getId()] = $graduacao->getGraduacao();
endforeach;

$graduacaoAtual = $object->getGraduacaoMinimaId();

if ($request->getMethod() == 'POST') :
    $data = $container->getRequest()->request->get('data');

    if ($request->query->has('id')) {
        $object = $_classPeer::retrieveByPK($request->query->get('id'));
        $object->setPontosResgate($data['PONTOSRESGATE']);
        $object->setPrimeiroPremio($data['PRIMEIROPREMIO']);
        $object->setSegundoPremio($data['SEGUNDOPREMIO']);
        $object->setPercentualVme($data['PERCENTUALVME']);
        $object->setGraduacaoMinimaId($data['GRADUACAO_MINIMA_ID']);
        $object->save();
        
        $session->getFlashBag()->add('success', 'Registro alterado com sucesso!');
        redirectTo($config['routes']['list']);
    } else {
        $object = new $_class();

        $object = new $_class();
        $object->setPontosResgate($data['PONTOSRESGATE']);
        $object->setPrimeiroPremio($data['PRIMEIROPREMIO']);
        $object->setSegundoPremio($data['SEGUNDOPREMIO']);
        $object->setPercentualVme($data['PERCENTUALVME']);
        $object->setGraduacaoMinimaId($data['GRADUACAO_MINIMA_ID']);
        $object->save();

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
        redirectTo($config['routes']['list']);
    }
endif;