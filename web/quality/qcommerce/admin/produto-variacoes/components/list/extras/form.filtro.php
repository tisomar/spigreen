<legend>Filtro</legend>
<?php

use PFBC\Form;
use PFBC\Element;

$form = new Form("form-filtro");

$form->configure(array(
    'action' => $container->getRequest()->server->get('REQUEST_URI'),
));

$options = array('' => 'Selecione o atributo que deseja filtrar...');
foreach ($arrProdutoAtributos as $pAtributo) {
    $options[$pAtributo->getId()] = $pAtributo->getDescricao();
}
$form->addElement(new Element\Select('Atributo:', 'filtro[atributo]', $options, array(
    'value' => $container->getRequest()->request->get('filtro[atributo]', null, true),
    'required' => 'required',
)));

$form->addElement(new Element\Textbox('Valor:', 'filtro[valor]', array(
    'value' => $container->getRequest()->request->get('filtro[valor]', null, true),
    'required' => 'required',
)));

$form->addElement(new Element\Hidden('has_filter', 'true'));

$form->addElement(new Element\FilterButton());

$form->addElement(new Element\CancelButton($config['routes']['list'], "Todos"));

$form->render();
