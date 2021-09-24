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

$query = EstadoQuery::create()
    ->select(['Id', 'Nome'])
    ->orderById()
    ->find()
    ->toArray();

$arrEstados =  array('' => 'Todos') + array_column($query, 'Nome', 'Id');

$form->addElement(new Element\Select("Estado:", "filter[EstadoId]", $arrEstados, array(
    "value" => $request->query->get('filter[EstadoId]', '', true),
)));

$query = CentroDistribuicaoQuery::create()
    ->select(['Id', 'Descricao'])
    ->orderById()
    ->find()
    ->toArray();

$arrCentros =  array('' => 'Todos') + array_column($query, 'Descricao', 'Id');

$form->addElement(new Element\Select("Centro de Distribuição:", "filter[CentroDistribuicaoId]", $arrCentros, array(
    "value" => $request->query->get('filter[CentroDistribuicaoId]', '', true)
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($container->getRequest()->server->get('REDIRECT_URL'), 'Listar todos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
