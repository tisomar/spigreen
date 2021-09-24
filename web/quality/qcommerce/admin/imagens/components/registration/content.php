<?php
/* @var $container \QPress\Container\Container */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$options = array();
if (isset($config['dimensions'][$request->query->get('context')])) {
    $options = $config['dimensions'][$request->query->get('context')];
}
$options = $options + array(
    "required" => $object->isNew() && !$object->getNome(),
);
$form->addElement(new Element\FileImage("Imagem", "NOME", $options));

if ($object->isImagemExists()) {
    $form->addElement(new Element\Hidden('data[IMAGEM]', $object->getNome()));

    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Imagem atual:</label>
            <div class="col-sm-6">
                ' . $object->getThumb('width=400', array(
                'class' => 'thumbnail',
                'style' => 'background: #555',
            )) . '
            </div>
        </div>';

    $form->addElement(new Element\HTML($html));
}

$form->addElement(new Element\Textbox("Legenda:", "data[LEGENDA]", array(
    "value" => $object->getLegenda()
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

// -------
$form->addElement(new Element\Hidden('data[' . $context_field . ']', $_GET['reference']));

$form->render();
