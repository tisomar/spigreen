<?php
/* @var $object ProdutoComentario */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Hidden("form", "registrer"));

$form->addElement(new Element\Hidden('data[ID]', $object->getId()));

$form->addElement(new Element\HTML('<legend>Dados Cadastrais</legend>'));

$form->addElement(new Element\Html(
    '<blockquote>'
            . '<p><em>"' . nl2br($object->getDescricao()) . '"</em></p>'
            . '<small><b>' . $object->getNome() . '</b> em ' . $object->getData('d/m/Y') . '</small>'
            . '<br /><b>Avaliado como:</b> ' . ProdutoComentarioPeer::getNotaDescricao($object->getNota())
            . '<br /><b>E-mail:</b> ' . $object->getEmail()
        . '</blockquote>'
));

$form->addElement(new Element\Select("Status:", "data[STATUS]", ProdutoComentarioPeer::getStatusList(), array(
    "value" => $object->getStatus(),
    "required" => true
)));

        

$form->addElement(new Element\Button("Salvar Informações"));

$form->addElement(new Element\Button("Cancelar", "button", array(
    "onclick" => "history.go(-1);", "class" => "btn-link"
)));

$form->render();
