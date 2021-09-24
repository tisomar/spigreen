<?php

use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    //'action' => $request->server->get('REQUEST_URI'),
    'action' => '/admin/relatorio/clientes-ativos-inativos/',
    'method' => 'get',
    'view'   => new PFBC\View\Inline(),
    'labelToPlaceholder' => 1
));
$options = get_array_mes();
$form->addElement(new Element\Select("Mês", "filter[MesPesquisa]", $options, array(
    "value" => $request->query->get('filter[MesPesquisa]', date('n'), true),
)));

$optionAno = get_array_anos();
//var_dump($optionAno);
$form->addElement(new Element\Select("Ano", "filter[AnoPesquisa]", $optionAno, array(
    "value" => $request->query->get('filter[AnoPesquisa]', date('Y'), true),
)));

$form->addElement(new Element\Textbox("Nome/Razão Social", "filter[NomeRazaoSocial]", array(
    "value" => $request->query->get('filter[NomeRazaoSocial]', null, true),
)));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
