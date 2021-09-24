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


$form->addElement(new Element\Date("Data de:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Carrinhos abandonados a partir de..."
)));

$form->addElement(new Element\Date("Data até:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Carrinhos abandonados até..."
)));


$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
