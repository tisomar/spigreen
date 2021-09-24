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

$optionsPlano = array(
    ''              => 'Todos os clientes',
    'c_plano'       => 'Com plano',
    's_plano'       => 'Sem plano'
);

$form->addElement(new Element\Select("Tipo de cliente", "filter[TipoCliente]", $optionsPlano, array(
    "value" => $request->query->get('filter[tipoCliente]', '', true),
)));

$options = array(
    ''              => 'Situação',
    'inadimplentes' => 'Inadimplentes',
    'em_dia'        => 'Em dia'
);

$form->addElement(new Element\Select("Situação", "filter[situacaoPlano]", $options, array(
    "value" => $request->query->get('filter[situacaoPlano]', '', true),
)));

$form->addElement(new Element\FilterButton());
//$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
