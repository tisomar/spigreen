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

$form->addElement(new Element\Textbox("Legenda:", "data[LEGENDA]", array(
    "value" => $object->getLegenda()
)));

if (Config::get('has_foto_por_cor')) {
    if ($cores->count()) {
        $options = array('' => 'Imagem principal do produto');
        foreach ($cores as $cor) {
            $options[$cor->getDescricao()] = 'Mostrar quando selecionar: ' . $cor->getDescricao();
        }
        $form->addElement(new Element\Select("Cor associada:", "data[COR]", $options, array(
            "value" => $object->getCor(),
            'shortDesc' => 'Você pode associar a imagem cadastrada à uma cor. Se houver esta associação, esta imagem aparecerá somente quando o cliente selecionar a cor associada na página de detalhes do produto.<br>'
                . 'É importante deixar ao menos uma imagem sem cor vinculada para que ela apareça quando o cliente não estiver selecionado nenhuma cor.'
        )));
    }
}




$form->addElement(new Element\FileImage("Imagem", "IMAGEM", array(
    "required" => $object->isNew() && !$object->getImagem(),
    "shortDesc" => "Adicione imagens na dimensão <b>" . $config['dimensao'][$container->getRequest()->query->get('context')] . '</b> para melhor visualização. ',
    "dimensions" => array(
        'width' => '350px',
        'height' => '350px',
    ),
    'class' => 'check-ratio',
    "data-ratio" => $imageRatio,
)));
?>

<?php

if ($object->isImagemExists()) {
    $form->addElement(new Element\Hidden('data[IMAGEM]', $object->getImagem()));

    //$config['dimensao']
    $atributos = array(
        'class' => 'thumbnail',
        'style' => 'background: #555',
    );

    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
                Imagem atual:
            </label>
            <div class="col-sm-6">
                ' . $object->getThumb('height=350&width=350', $atributos) . '
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
