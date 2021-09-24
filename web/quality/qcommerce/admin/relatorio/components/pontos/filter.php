<?php
use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'get',
    'view' => new PFBC\View\Inline(),
    'labelToPlaceholder' => 1
));

$form->addElement(new Element\Textbox("Data Inicial:", "filter[DataInicial]", array(
    "value" => $request->query->get('filter[DataInicial]', null, true),
    "title" => "Distribuidos a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Data Final:", "filter[DataFinal]", array(
    "value" => $request->query->get('filter[DataFinal]', null, true),
    "title" => "Distribuidos até...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Select("Cliente", "filter[ClienteId]", array_column($listaClientes, 'Nome', 'Id'), array(
    "title" => "Cliente",
    'class' => 'select2',
    'multiple' => false,
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[ClienteId]', null, true),
)));

$form->addElement(new Element\Select("Bônus", "filter[Tipo]", $tipoBonus, [
    "title" => "Bônus",
    'class' => 'select2',
    'data-allow-clear' => true,
    'data-placeholder' => true,
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[Tipo]', null, true)
]));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));

?>
<style>
    .select2-container.select2-allowclear .select2-choice abbr {
        transform: translateY(-50%);
    }
</style>
