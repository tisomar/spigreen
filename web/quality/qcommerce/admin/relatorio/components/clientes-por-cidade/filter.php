<?php
use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$queryCidades = CidadeQuery::create()
    ->select([
        'ID',
        'NOME'
    ])
    ->addAsColumn('ID', CidadePeer::ID)
    ->addAsColumn('NOME', CidadePeer::NOME)
    ->useEnderecoQuery()
        ->useClienteQuery()
            ->filterByVago(false)
        ->endUse()
    ->endUse()
    ->orderByNome()
    ->find()
    ->toArray();

$queryEstados = EstadoQuery::create()
    ->select([
        'ID',
        'SIGLA'
    ])
    ->addAsColumn('ID', EstadoPeer::ID)
    ->addAsColumn('SIGLA', EstadoPeer::SIGLA)
    ->orderBySigla()
    ->find()
    ->toArray();

function array_unshift_assoc($arr, $key, $val)
{
    $arr = array_reverse($arr, true);
    $arr[$key] = $val;
    return array_reverse($arr, true);
}

$cidades = array_column($queryCidades, 'NOME', 'ID');
$cidades = array_unshift_assoc($cidades, '', 'Selecione');

$estatos = array_column($queryEstados, 'SIGLA', 'ID');
$estatos = array_unshift_assoc($estatos, '', 'Selecione');

$form = new Form("form-filter");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'get',
    'view' => new PFBC\View\Inline(),
    'labelToPlaceholder' => 1
));

$form->addElement(new Element\Textbox("Nome do cliente", "filter[NomeRazaoSocial]", [
    'value' => $request->query->get('filter')['NomeRazaoSocial'] ?? null
]));

$form->addElement(new Element\Select("Cidade", "filter[Cidade]", $cidades, [
    'value' => $request->query->get('filter')['Cidade'] ?? null
]));

$form->addElement(new Element\Select("Cidade", "filter[Estado]", $estatos, [
    'value' => $request->query->get('filter')['Estado'] ?? null
]));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\Hidden("page", $request->query->get('page', 1)));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
