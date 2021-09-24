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

$form->addElement(new Element\Select("Mês", "filter[Mes]", $meses, [
    "title" => "Mês",
    'class' => 'form-control',
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[Mes]', null, true),
]));

$form->addElement(new Element\Select("Ano", "filter[Ano]", $anos, [
    "title" => "Ano",
    'class' => 'form-control',
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[Ano]', null, true),
]));

$form->addElement(new Element\Select("Cliente", "filter[ClienteId]", $listaClientes, [
    "title" => "Cliente",
    'class' => 'select2',
    'style' => 'min-width:500px;',
    'value' => $request->query->get('filter[ClienteId]', null, true),
]));

// $form->addElement(new Element\Textbox("Nome/Razão Social", "filter[NomeRazaoSocial]", array(
//     "value" => $request->query->get('filter[NomeRazaoSocial]', null, true),
// )));

// $form->addElement(new Element\Select("Bônus", "filter[Tipo]", $tipoBonus, [
//     "title" => "Bônus",
//     'class' => 'select2',
//     'data-allow-clear' => true,
//     'data-placeholder' => true,
//     'style' => 'min-width:200px;',
//     'value' => $request->query->get('filter[Tipo]', null, true)
// ]));

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
