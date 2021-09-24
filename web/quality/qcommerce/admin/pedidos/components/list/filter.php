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

$form->addElement(new Element\Textbox("Código", "filter[Id]", array(
    "value" => $request->query->get('filter[Id]', null, true),
)));

$form->addElement(new Element\Textbox("Cliente/Razão Social", "filter[ClienteNome]", array(
    "value" => $request->query->get('filter[ClienteNome]', null, true),
)));

$form->addElement(new Element\Textbox("CPF", "filter[ClienteCpf]", array(
    "value" => $request->query->get('filter[ClienteCpf]', null, true),
    "class" => 'mask-cpf'
)));

$form->addElement(new Element\Textbox("CNPJ", "filter[ClienteCnpj]", array(
    "value" => $request->query->get('filter[ClienteCnpj]', null, true),
    "class" => 'mask-cnpj'
)));

$form->addElement(new Element\Textbox("Pedidos apartir de:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Pedidos a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Pedidos feitos até:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Pedidos feitos até...",
    "class" => "_datepicker mask-date",
)));

//$options = array('' => 'Todos os Status') + PedidoPeer::getStatusList();
//$form->addElement(new Element\Select("Status do Pedido:", "filter[Status]", $options, array(
//    "value" => $request->query->get('filter[Status]', null, true),
//)));

$options = array('' => 'Todos os Status') + PedidoPeer::getPedidoStatusList() + PedidoPeer::getStatusList();
$form->addElement(new Element\Select("Status do Pedido:", "filter[StatusHistorico]", $options, array(
    "value" => $request->query->get('filter[StatusHistorico]', '', true),
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
