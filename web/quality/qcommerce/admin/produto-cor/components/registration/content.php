<?php

/* @var $object Rede */

use PFBC\Form;
use PFBC\Element;
use PFBC\View;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
));

$form->addElement(new Element\Textbox('Nome da cor:', 'data[NOME]', array(
    "value" => $object->getNome(),
    "required" => true,
)));


$form->addElement(new Element\Textbox('RGB:', 'data[RGB]', array(
    "value" => $object->getRgb(),
    "class" => "cpicker",
    "data-color-format" => "hex",
    "data-change-color" => ".change-color",
    'shortDesc' => 'Você pode optar por informar um código RGB ou uma imagem.',
    "append" => ' '
)));

$form->addElement(new Element\FileImage("Imagem", "IMAGEM", array(
    'shortDesc' => 'A imagem deve ser um quadrado de 32x32 pixels.',
    "dimensions" => array(
        'width' => '32px',
        'height' => '32px',
    )
)));

$form->addElement(new Element\Hidden('data[IMAGEM]', $object->getImagem()));


$html = '
    <div class="form-group">
        <label class="col-sm-3 control-label" for="registrer-element-1">
        Cor atual:</label>
        <div class="col-sm-6 change-color">
            ' . $object->getBoxColor(32, 32) . '
        </div>
    </div>';

if (!$object->isNew()) {
    $form->addElement(new Element\HTML($html));
}

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
