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

$form->addElement(new Element\Textbox("Data Inicial:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Pedidos a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Data Final:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Pedidos até...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Select("Cliente", "filter[ClienteId]", array_column($listaClientes, 'Nome', 'Id'), array(
    "title" => "Cliente",
    'class' => 'select2',
    'multiple' => false,
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[ClienteId]', null, true),
)));

$form->addElement(new Element\Select("Tipo de Venda", "filter[TipoVenda]", array('' => 'Todas as Vendas', 'direta' => 'Vendas Diretas', 'pontos' => 'Vendas com bônus'), array(
    "title" => "tipovenda",
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[TipoVenda]', null, true),
)));

$form->addElement(new Element\Select("Centro de distribuição", "filter[FilialDistribuicao]", array('0' => 'Selecione', '3' => 'Filial MT', '1' => 'Filial ES', 'retirada_loja_ES' => 'Retirada em loja ES', 'retirada_loja_MT' => 'Retirada em loja MT ( Cuiabá )', 'retirada_loja_GO' => 'Retirada em loja GO ( Goiânia )'), array(
    "title" => "filialDistribuicao",
    'style' => 'min-width:200px;',
    'value' => $request->query->get('filter[FilialDistribuicao]', null, true),
)));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
