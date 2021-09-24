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

$form->addElement(new Element\Textbox("Nome", "filter[Nome]", array(
    "value" => $request->query->get('filter[Nome]', null, true),
)));

$form->addElement(new Element\Textbox("E-mail", "filter[Email]", array(
    "value" => $request->query->get('filter[Email]', null, true),
)));

$form->addElement(new Element\Textbox("Cadastros apartir de:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Pedidos a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Cadastros feitos até:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Pedidos feitos até...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('PHP_SELF'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
