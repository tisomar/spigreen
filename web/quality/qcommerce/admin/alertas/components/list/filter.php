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



$form->addElement(new Element\Textbox("Data de Envio", "filter[DATA_ENVIO]", array(
    "value" => $request->query->get('filter[DATA_ENVIO]', null, true),
    "title" => "Data de envio",
    "class" => "_datepicker mask-date",
)));

$options = array('' => 'Todos') + UsuarioPeer::getUsuarioNomeList();
$form->addElement(new Element\Select("Usuário", "filter[USUARIO_ID]", $options, array(
    "value" => $request->query->get('filter[USUARIO_ID]', null, true),
)));


$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
