<?php

use PFBC\Form;
use PFBC\Element;

/* @var $object EstadoCentroDistribuicao */
$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$query = EstadoQuery::create()
    ->select(['Id', 'Nome'])
    ->filterById($request->query->get('id'))
    ->find()
    ->toArray();

$arrEstados =  array_column($query, 'Nome', 'Id');

$form->addElement(new Element\Select("Estado:", "data[ESTADO_ID]", $arrEstados, array(
    "value" => $object->getEstado()->getId(),
    "disabled" => "disabled"
)));

$query = CentroDistribuicaoQuery::create()
    ->select(['Id', 'Descricao'])
    ->filterByStatus(true)
    ->orderByDescricao()
    ->find()
    ->toArray();

$arrCentros =  array_column($query, 'Descricao', 'Id');

$form->addElement(new Element\Select("Centro de Distribuição:", "data[CENTRO_DISTRIBUICAO_ID]", $arrCentros, array(
    "value" => $object->getCentroDistribuicao() ? $object->getCentroDistribuicao()->getId() : null
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) :
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
endif;

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
