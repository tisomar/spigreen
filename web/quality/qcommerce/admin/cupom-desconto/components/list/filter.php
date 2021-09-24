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

$form->addElement(new Element\Textbox("Cupom", "filter[CUPOM]", array(
    "value" => $request->query->get('filter[CUPOM]', null, true),
)));
$list = array();
$list['TIPO_DESCONTO'] = array_merge(array('' => 'Tipo de Desconto'), CupomPeer::getTipoDescontoList());
$form->addElement(new Element\Select("Tipo", "filter[TIPO_DESCONTO]", $list['TIPO_DESCONTO'], array(
    "value" => $request->query->get('filter[TIPO_DESCONTO]', null, true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
