<?php

/* @var $object Marca */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$form->addElement(new Element\FileImage("Imagem", "IMAGEM", array(
    "required" => $object->isNew() && !$object->getImagem(),
    "shortDesc" => "Adicione imagens na dimensão <b>155x124 pixels</b> para melhor visualização.",
    "dimensions" => array(
        'width' => '155px',
        'height' => '124px',
    ),
    'class' => 'check-ratio',
    "data-ratio" => "1.25",
)));

if ($object->isImagemExists()) {
    $form->addElement(new Element\Hidden('data[IMAGEM]', $object->getImagem()));

    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Imagem atual:</label>
            <div class="col-sm-6">
                ' . $object->getThumb('width=220&height=180&cropratio=1.222:1', array(
                'class' => 'thumbnail',
                'style' => 'background: #555',
            )) . '
            </div>
        </div>';

    $form->addElement(new Element\HTML($html));
}

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
