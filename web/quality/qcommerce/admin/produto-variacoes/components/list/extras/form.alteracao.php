<legend>Alterações em lote</legend>
<?php

use PFBC\Form;
use PFBC\Element;

$form = new Form("form-alteracao-em-massa");

$form->configure(array(
    'action' => $container->getRequest()->server->get('REQUEST_URI'),
));

$options = array('' => 'Selecione o campo que quer alterar');
foreach ($arrProdutoAtributos as $pAtributo) {
    $options['ATRIBUTO-' . $pAtributo->getId()] = 'Atributo: ' . $pAtributo->getDescricao();
}
$options['SKU'] = 'Referencia';
$options['VALOR_BASE'] = 'Preço Normal';
$options['VALOR_PROMOCIONAL'] = 'Preço de Oferta';
$options['ESTOQUE_ATUAL'] = 'Estoque Atual';
if (Config::get('aviso_estoque_minimo')) {
    $options['ESTOQUE_MINIMO'] = 'Estoque Mínimo';
}
$options['DESTAQUE'] = 'Disponível';
$form->addElement(new Element\Select('Campo:', 'lote[campo]', $options, array('required' => 'trquired')));

$form->addElement(new Element\Textbox('Novo valor:', 'lote[valor]', array('required' => 'trquired')));

$form->addElement(new Element\Hidden("filtro[atributo]", $container->getRequest()->request->get('filtro[atributo]', null, true)));
$form->addElement(new Element\Hidden("filtro[valor]", $container->getRequest()->request->get('filtro[valor]', null, true)));

$form->addElement(new Element\SaveButton("Salvar"));

$form->addElement(new Element\Hidden('has_filter', true));

$form->render();
