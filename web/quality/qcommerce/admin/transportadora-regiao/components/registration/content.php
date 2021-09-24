<?php
/* @var $object TransportadoraRegiao */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Textbox("Nome da região:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$form->addElement(new Element\Textbox("CEP inicial", "data[CEP_INICIAL]", array(
    "value" => format_cep($object->getCepInicial()),
    "required" => true,
    "class" => "mask-cep"
)));

$form->addElement(new Element\Textbox("CEP final", "data[CEP_FINAL]", array(
    "value" => format_cep($object->getCepFinal()),
    "required" => true,
    "class" => "mask-cep"
)));

$form->addElement(new Element\Textarea("Observações", "data[OBSERVACAO]", array(
    "value" => $object->getObservacao(),
    "required" => true,
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
