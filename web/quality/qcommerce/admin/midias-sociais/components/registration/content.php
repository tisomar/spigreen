<?php

/* @var $object Rede */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

if (UsuarioPeer::getUsuarioLogado()->isMaster()) {
    $form->addElement(new Element\Textbox('Mídia Social:', 'data[NOME]', array(
        "value" => $object->getNome(),
        "required" => true,
    )));
} else {
    $form->addElement(new Element\Textbox('Mídia Social:', '', array(
        "value" => $object->getNome(),
        'disabled' => true
    )));
}

$form->addElement(new Element\Number("Ordem", "data[ORDEM]", array(
    "value" => $object->getOrdem(),
    "required" => true,
    "min" => 1,
)));

$form->addElement(new Element\Select("Ativo:", "data[ATIVO]", array('Não', 'Sim'), array(
    "value" => $object->getAtivo(),
    "required" => true,
    'class' => 'tooltips',
    'data-trigger' => 'hover',
    'data-original-title' => 'Define se a mídia social estará disponível no site.',
)));

if (UsuarioPeer::getUsuarioLogado()->isMaster()) {
    $form->addElement(new Element\Url("Icon:", "data[ICON]", array(
        "value" => $object->getLink(),
        'required' => true,
        'prepend' => 'fa-',
        'shortDesc' => 'Adicione o nome de um ícone que contenha no Font-Awesome ' .
            '(padrão de icones utilizado). Adicionar apenas o nome retirando os prefixos ' .
            '"fa" e "fa-", deixando apenas "facebook" por exemplo.'
    )));
}

$form->addElement(new Element\Url("Link:", "data[LINK]", array(
    "value" => $object->getLink(),
    'required' => true
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
