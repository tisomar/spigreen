<?php include __DIR__ . '/../../config/menu.php'; ?>

<?php

/* @var $object Produto */
/* @var $container \QPress\Container\Container */

if (is_null($object->getOrdem())) {
    $ordem = ProdutoAtributoQuery::create()->select(array('Ordem'))->filterByProdutoId($_GET['reference'])->orderByOrdem(Criteria::DESC)->findOne();
    $ordem = (int) $ordem + 1;
} else {
    $ordem = $object->getOrdem();
}

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Descrição:", "data[DESCRICAO]", array(
    "value" => $object->getDescricao(),
    "title" => "Descrição do atributo que aparecerá para o cliente no momento da compra. Ex: Cor, Tamanho, Voltagem, Modelagem, etc."
)));

$form->addElement(new Element\Number("Ordem:", "data[ORDEM]", array(
    "value" => $ordem,
    "title" => "Ordem do atributo. Utilizado para informar qual atributo aparecerá primeiro para que o cliente selecione no momento da compra do produto."
)));

if (Config::get('has_produto_cor')) {
    $form->addElement(new Element\Radio("Tipo de atributo:", "data[TYPE]", $_classPeer::getTypeList(), array(
        "value" => $object->getType(),
    )));
}

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('data[' . $context_field . ']', $_GET['reference']));

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
