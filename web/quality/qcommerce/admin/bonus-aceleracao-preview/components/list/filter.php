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


$form->addElement(new Element\Textbox("Cliente", "filter[NomeCliente]", array(
    "value" => $request->query->get('filter[NomeCliente]', null, true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL') . '?participacao_resultado_id=' . $request->query->get('participacao_resultado_id'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));
$form->addElement(new Element\Hidden("participacao_resultado_id", $request->query->get('participacao_resultado_id')));

Widget::render('admin/filter', $form->render(true));
