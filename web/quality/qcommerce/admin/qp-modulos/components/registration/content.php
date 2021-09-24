<?php

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$modulosList = array();
foreach ($root->getBranch($_classQuery::create()->filterByTreeLevel(array('max' => 1))) as $modulo) {
    $modulosList[$modulo->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $modulo->getTreeLevel()) . '' . $modulo->getNome();
}

$form->addElement(new Element\Select("Subcategoria de:", "PARENT_ID", $modulosList, array(
    "value" => $object->getParent() ? $object->getParent()->getId() : null,
    "required" => true
)));

$form->addElement(new Element\Textbox("Url interna:", "data[URL]", array(
    "value" => $object->getUrl(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Icone:", "data[ICON]", array(
    "value" => $object->getIcon(),
)));


$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
