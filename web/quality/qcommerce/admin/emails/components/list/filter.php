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

$form->addElement(new Element\Textbox("Remetente", "filter[Remetente]", array(
    "value" => $request->query->get('filter[Remetente]', null, true),
)));

$form->addElement(new Element\Textbox("Destinatário", "filter[Destinatario]", array(
    "value" => $request->query->get('filter[Destinatario]', null, true),
)));

$form->addElement(new Element\Textbox("Assunto", "filter[Assunto]", array(
    "value" => $request->query->get('filter[Assunto]', null, true),
)));

$options = array('' => 'Status') + EmailLogPeer::getStatusList();
$form->addElement(new Element\Select("Status", "filter[Status]", $options, array(
    "value" => $request->query->get('filter[Status]', null, true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));
$form->addElement(new Element\Hidden("order_by", ""));

Widget::render('admin/filter', $form->render(true));
