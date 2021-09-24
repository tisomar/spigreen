<?php
/* @var $object FreteGratisRegiao */

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

$form->addElement(new Element\Textbox("Valor mínimo no pedido", "data[VALOR_MINIMO]", array(
    "value" => 'R$ ' . format_money($object->getValorMinimo()),
    "required" => true,
    "class" => "mask-money"
)));

$form->addElement(new Element\Number("Prazo para entrega (dias)", "data[PRAZO_ENTREGA]", array(
    "value" => $object->getPrazoEntrega(),
    "required" => true,
)));

$form->addElement(new Element\Textarea("Observações", "data[OBSERVACAO]", array(
    "value" => $object->getObservacao(),
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
