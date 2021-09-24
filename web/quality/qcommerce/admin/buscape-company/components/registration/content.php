
<?php
include __DIR__ . '/../../config/menu.php';

/* @var $object Produto */
/* @var $container \QPress\Container\Container */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Radio("Ativo?", "data[ATIVO]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => (int) !is_null($item->getProdutoId()),
    'required' => true
)));

$form->addElement(new Element\Hidden('data[PRODUTO_ID]', $_GET['reference']));

$form->addElement(new Element\SaveButton());

$form->render();
