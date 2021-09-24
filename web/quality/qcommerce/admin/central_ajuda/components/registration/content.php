<?php
/* @var $object AjudaPaginaVideo */
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Página:", "PAGINA", array(
    "value" => $object->getPagina(),
    "disabled" => 'disabled'
)));

$form->addElement(new Element\Textbox("Local:", "SISTEMA", array(
    "value" => $object->getSistema(),
    "disabled" => 'disabled'
)));

$form->addElement(new Element\Url("URL do vídeo:", "data[VIDEO]", array(
    "shortDesc" => 'Sites permitidos: Youtube e Vimeo. Você deve copiar a URL que consta dentro do IFRAME do incorporar.
    <br>
    Exemplo de um Iframe e o que deve ser copiado está destacado em negrito:
    <br /> <br />
    iframe width="560" height="315" src="<b>https://www.youtube.com/embed/3CGX6liJqhI</b>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>   
    
    ',
    "value" => $object->getVideo(),
    'class' => 'video'
)));


$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
