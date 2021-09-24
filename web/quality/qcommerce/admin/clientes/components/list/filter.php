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

$form->addElement(new Element\Textbox("Nome/Razão Social", "filter[NomeRazaoSocial]", array(
    "value" => $request->query->get('filter[NomeRazaoSocial]', null, true),
)));

$form->addElement(new Element\Textbox("CPF/CNPJ", "filter[CpfCnpj]", array(
    "value" => $request->query->get('filter[CpfCnpj]', null, true),
)));

$form->addElement(new Element\Textbox("Email", "filter[Email]", array(
    "value" => $request->query->get('filter[Email]', null, true),
)));

$options = array('' => 'Status') + ClientePeer::getStatusList();
$form->addElement(new Element\Select("Status", "filter[Status]", $options, array(
    "value" => $request->query->get('filter[Status]', null, true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));
$form->addElement(new Element\Hidden("order_by", ""));

Widget::render('admin/filter', $form->render(true));
