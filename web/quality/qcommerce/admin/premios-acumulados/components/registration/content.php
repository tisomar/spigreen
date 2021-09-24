<?php

use PFBC\Form;
use PFBC\Element;

/** @var $object PlanoCarreira */

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
    'enctype' => 'multipart/form-data'
));


// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Cadastro dos prêmios de pontos acumulados</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Cadastro dos prêmios de pontos acumulados</h4>
        </div>
    </div>
'));


$form->addElement(new Element\Number('Pontos para resgate', 'data[PONTOSRESGATE]', [
    'value' => $object->getPontosResgate(),
    'required' => true,
    'min' => 1
]));

$form->addElement(new Element\Textbox('Primeiro prêmio', 'data[PRIMEIROPREMIO]', [
    'value' => $object->getPrimeiroPremio(),
    'required' => true,
]));

$form->addElement(new Element\Textbox('Segundo prêmio', 'data[SEGUNDOPREMIO]', [
    'value' => $object->getSegundoPremio(),
]));

$form->addElement(new Element\Textbox('Percentual VME', 'data[PERCENTUALVME]', [
    'value' => $object->getPErcentualVme(),
    'placeholder' => 'Ex: 60'
]));

$graduacaoAr =  array('' => 'Graduação') + $graduacaoList;
$form->addElement(new Element\Select("Graduação mínima", "data[GRADUACAO_MINIMA_ID]", $graduacaoAr, array(
    "value" => $graduacaoAtual ? $graduacaoAtual : null,
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
    $('[name="data[REQU_QUANTIDADE]"]').on('blur', function(e) {
        $('[name="data[REQU_GRADUACAO]"]').attr('required', !!e.target.value);
    });

    $(function() {
        $('[name="data[REQU_GRADUACAO]"]').attr('required', !!$('[name="data[REQU_QUANTIDADE]"]').val());
    });
</script>
