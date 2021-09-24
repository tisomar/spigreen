<?php
use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    // 'action' => $request->server->get('REQUEST_URI'),
    'action' => '/admin/relatorio/clientes-descontinuados/',
    'method' => 'get',
    'view' => new PFBC\View\Inline(),
    'labelToPlaceholder' => 1
));

$form->addElement(new Element\Textbox("Cadastros apartir de:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Cadastros a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Cadastros feitos até:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Cadastros feitos até...",
    "class" => "_datepicker mask-date",
)));

// $form->addElement(new Element\Select("Cliente", "filter[ClienteId]", array_column($listaClientes, 'Nome', 'Id'), array(
//     "title" => "Cliente",
//     'class' => 'select2',
//     'multiple' => false,
//     'style' => 'min-width:200px;',
//     'value' => $request->query->get('filter[ClienteId]', null, true),
// )));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
