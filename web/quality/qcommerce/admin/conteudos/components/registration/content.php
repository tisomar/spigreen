<?php
/* @var $object Rede */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


if (UsuarioPeer::getUsuarioLogado()->isMaster()) {
    $form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
        "value" => $object->getNome(),
        "required" => true
    )));
} else {
    $form->addElement(new Element\Textbox("Nome:", "", array(
        "value" => $object->getNome(),
        "disabled" => true
    )));
}

$form->addElement(new Element\Textarea("Conteúdo:", "data[DESCRICAO]", array(
    "value" => $object->getDescricao(),
    'class' => 'mceEditor'
)));


if ($object->getPossuiGaleria()) {
    $collGaleria = GaleriaQuery::create()->select(array('Id', 'Nome'))->orderByNome()->find()->toArray();
    if (count($collGaleria)) {
        $options = get_select_default() + array_combine(array_column($collGaleria, 'Id'), array_column($collGaleria, 'Nome'));
    } else {
        $options = get_select_default();
    }
    $form->addElement(new Element\Select("Galeria", 'data[GALERIA_ID]', $options, array(
        'value' => $object->getGaleriaId()
    )));
}



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

    });
</script>
