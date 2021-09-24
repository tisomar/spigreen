<?php

use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'get',
    'view' => new PFBC\View\Inline(),
    "labelToPlaceholder" => 1
));

$form->addElement(new Element\Textbox("Título", "filter[TITULO]", array(
    "value" => $request->query->get('filter[TITULO]', null, true)
)));

$list = array();
$list['TIPO'] = (array('' => 'Tipo') + SuportePeer::getTipoList());
$form->addElement(new Element\Select("Tipo", "filter[TIPO]", $list['TIPO'], array(
    "value" => $request->query->get('filter[TIPO]', null, true),
)));

$list = array();
$list['MOSTRAR'] = (array('' => 'Disponivel no site') + SuportePeer::getMostrarList());
$form->addElement(new Element\Select("Ativo", "filter[MOSTRAR]", $list['MOSTRAR'], array(
    "value" => $request->query->get('filter[MOSTRAR]', null, true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
