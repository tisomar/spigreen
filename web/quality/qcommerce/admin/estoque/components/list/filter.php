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

$arrProdutosSimples =  array('' => 'Todos os produtos') + ProdutoPeer::getProdutoSimplesList();

$form->addElement(new Element\Select("Produtos:", "filter[ProdutoId]", $arrProdutosSimples, array(
    "value" => $request->query->get('filter[ProdutoId]', '', true),
)));

$form->addElement(new Element\Select("Variacao:", "filter[ProdutoVariacaoId]", array('' => 'Todas as Variações'), array(
    "value" => $request->query->get('filter[ProdutoVariacaoId]', '', true),
    "id" => ''
)));

$aCentrosDistribuicao = array('' => 'Todos os Centros') + CentroDistribuicaoPeer::getCentrosDistribuicaoList();

$form->addElement(new Element\Select("Centro:", "filter[CentroDistribuicaoId]", $aCentrosDistribuicao, array(
    "value" => $request->query->get('filter[CentroDistribuicaoId]', '', true),
)));

$form->addElement(new Element\Select(
    "Tipo:",
    "filter[Operacao]",
    array('' => 'Entradas e Saídas', 'ENTRADA' => 'Somente Entradas', 'SAIDA' => 'Somente Saídas'),
    array("value" => $request->query->get('filter[Operacao]', '', true),
    )
));

$form->addElement(new Element\Textbox("Data Inicial:", "filter[DataDe]", array(
    "value" => $request->query->get('filter[DataDe]', null, true),
    "title" => "Data inicial a partir de...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\Textbox("Data Final:", "filter[DataAte]", array(
    "value" => $request->query->get('filter[DataAte]', null, true),
    "title" => "Data final até...",
    "class" => "_datepicker mask-date",
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));
$form->addElement(new Element\Hidden("order_by", ""));

Widget::render('admin/filter', $form->render(true));
