<?php

use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'get',
//    'view' => new PFBC\View\Inline(),
//    "labelToPlaceholder" => 1
));

$form->addElement(new Element\Textbox("Referência", "filter[Referencia]", array(
    "value" => $request->query->get('filter[Referencia]', null, true),
)));

$form->addElement(new Element\Textbox("Nome", "filter[Nome]", array(
    "value" => $request->query->get('filter[Nome]', null, true),
)));

$options = array(
    '' => 'Marca',
);

$options += array_column(MarcaQuery::create()->select(array('Id', 'Nome'))->orderByNome()->find()->toArray(), 'Nome', 'Id');
$form->addElement(new Element\Select("Marca", "filter[MarcaId]", $options, array (
    "value" => $request->query->get('filter[MarcaId]', null, true),
)));


$options = array(
    '' => 'Categoria',
);
$arrayCategorias = CategoriaQuery::create()->select(array('Id', 'Nome', 'NrLvl'))->orderByNrLft()->filterByNrLvl(array('min' => 1))->find()->toArray();
foreach ($arrayCategorias as $categoria) {
    $options[$categoria['Id']] = str_repeat('&minus; ', ($categoria['NrLvl'] - 1) * 1) . $categoria['Nome'];
}

$form->addElement(new Element\Select("Categoria", "filter[CategoriaId]", $options, array (
    "value" => $request->query->get('filter[CategoriaId]', null, true),
)));

$options = array(
    '' => 'Mostrar no site',
    ProdutoPeer::DESTAQUE_SIM => 'Mostrar no site: Sim',
    ProdutoPeer::DESTAQUE_NAO => 'Mostrar no site: Não',
);
$form->addElement(new Element\Select('Mostrar no site', 'filter[Disponivel]', $options, array(
    'value' => $request->query->get('filter[Disponivel]', null, true)
)));

$form->addElement(new Element\FilterButton());
$form->addElement(new Element\CancelButton($config['routes']['registration'], 'Cancelar Busca'));

foreach ($container->getRequest()->query->all() as $key => $value) {
    if ('filter' != $key) {
        $form->addElement(new Element\Hidden($key, $value));
    }
}

if (false == $container->getRequest()->query->has('page')) {
    $form->addElement(new Element\Hidden("page", ""));
}

if (false == $container->getRequest()->query->has('is_filter')) {
    $form->addElement(new Element\Hidden("is_filter", "true"));
}

Widget::render('admin/filter', $form->render(true));
