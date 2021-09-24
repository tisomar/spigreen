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

$form->addElement(new Element\Select("Cliente Movido", "filter[ClienteMovido]", $listaClientesMovidos, [
    "title" => "Cliente",
    'class' => 'select2',
    'style' => 'min-width:500px;',
    'value' => $request->query->get('filter[ClienteId]', null, true),
]));

$form->addElement(new Element\Select("Cliente Destino", "filter[ClienteDestino]", $listaClientesDestino, [
    "title" => "Cliente",
    'class' => 'select2',
    'style' => 'min-width:500px;',
    'value' => $request->query->get('filter[ClienteId]', null, true),
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
