<?php
/* @var $object Suporte */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Radio("Tipo:", "data[TIPO]", SuportePeer::getTipoList(), array(
    "value" => $object->getTipo(),
    "required" => true,
    'class' => 'tipo'
)));


$form->addElement(new Element\Radio("Ativo:", "data[MOSTRAR]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getMostrar(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Ordem:", "data[ORDEM]", array(
    "value" => $object->getOrdem(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Título:", "data[TITULO]", array(
    "value" => $object->getTitulo(),
    'required' => true,
)));

$form->addElement(new Element\Textarea("Descrição resumida:", "data[DESCRICAO_RESUMIDA]", array(
    "value" => $object->getDescricaoResumida(),
    'required' => true,
)));

$form->addElement(new Element\Textarea("Texto:", "data[DESCRICAO]", array(
    "value" => $object->getDescricao(),
    "required" => false,
    'class' => 'mceEditor descricao'
)));

$form->addElement(new Element\Url("URL do vídeo:", "data[VIDEO]", array(
    "shortDesc" => "Sites permitidos: Youtube e Vimeo",
    "value" => $object->getVideo(),
    "required" => false,
    'class' => 'video'
)));

$form->addElement(new Element\File("Arquivo:", "ARQUIVO", array(
    "required" => false
)));


$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
?>

<script>
    $(function() {
        var $textoContainer = $('.descricao').closest('.form-group');
        var $videoContainer = $('.video').closest('.form-group');
        var $arquivoContainer = $('[name=ARQUIVO]').closest('.form-group');
        
        $('.tipo').change(function(){
            var tipo = $('.tipo:checked').val();
            switch (tipo) {
                case 'TEXTO':
                    $videoContainer.hide();
                    $arquivoContainer.hide();
                    $textoContainer.show();
                    break;
                case 'VIDEO':
                case 'VIDEO_AULA':
                    $textoContainer.hide();
                    $arquivoContainer.hide();
                    $videoContainer.show();
                    break;
                case 'ARQUIVO':
                    $textoContainer.hide();
                    $videoContainer.hide();
                    $arquivoContainer.show();
                    break;
            }
        }).change();
        
    });
</script>
