<?php

use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-alteracao-rede");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'post',
    'view' => new PFBC\View\Inline(),
    'labelToPlaceholder' => 1
));

$listaClientes[''] = 'Cliente a ser movido';
$form->addElement(new Element\Select("Cliente a mover", "filter[ClienteMoverId]", $listaClientes, [
    'title' => 'Cliente',
    'class' => 'select2',
    'style' => 'min-width:300px;',
    'required' => 'true',
    'value' => $request->query->get('filter[ClienteId]', null, true)
]));

$listaClientes[''] = 'Mover para a rede do cliente:';
$form->addElement(new Element\Select("Mover para a rede do cliente", "filter[ClienteDestinoId]", $listaClientes, [
    'title' => 'Cliente',
    'class' => 'select2',
    'style' => 'min-width:300px;',
    'required' => 'true',
    'value' => $request->query->get('filter[ClienteDestinoId]', null, true)
]));

$form->addElement(new Element\SaveButton("MOVER"));

Widget::render('admin/filter', $form->render(true));
?>
<style>
    .select2-container.select2-allowclear .select2-choice abbr {
        transform: translateY(-50%);
    }
</style>