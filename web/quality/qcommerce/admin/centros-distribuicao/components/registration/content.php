<?php

use PFBC\Form;
use PFBC\Element;

/* @var $object CentroDistribuicao */
$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Descrição:", "data[Descricao]", array(
    "value" => $object->getDescricao(),
    "required" => true
)));

$form->addElement(new Element\Textbox("CEP:", "data[Cep]", array(
    "value" => $object->getCep(),
    "required" => true,
    "class" => "mask-cep"
)));

$form->addElement(new Element\Radio("Status", "data[Status]", array(1 => 'Ativo', 0 => 'Inativo'), array(
    "value" => $object->getStatus()
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
