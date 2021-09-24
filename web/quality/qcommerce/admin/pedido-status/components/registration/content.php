<?php

/* @var $object PedidoStatus */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox('Pré-confirmação:', 'data[LABEL_PRE_CONFIRMACAO]', array(
    "value" => $object->getLabelPreConfirmacao(),
    "required" => true,
)));

$form->addElement(new Element\Textarea('Mensagem ao consumidor:', 'data[MENSAGEM]', array(
    "value" => $object->getMensagem(),
    "required" => true,
)));

$form->addElement(new Element\Textbox('Pós-confirmação:', 'data[LABEL_POS_CONFIRMACAO]', array(
    "value" => $object->getLabelPosConfirmacao(),
    "required" => true,
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

$form->addElement(new Element\Hidden('data[ID]', $object->getId()));

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
