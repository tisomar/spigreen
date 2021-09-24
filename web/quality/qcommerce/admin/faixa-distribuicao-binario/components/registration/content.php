<?php
use PFBC\View;
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Number("Pontos Inicial", "data[PONTUACAO_INICIAL]", array(
    "value" => $object->getPontuacaoInicial(),
    "required" => true,
    'min'   => '0'
)));

$form->addElement(new Element\Number("Pontos Final", "data[PONTUACAO_FINAL]", array(
    "value" => $object->getPontuacaoFinal(),
    "required" => true,
    'min'   => '0'
)));

$form->addElement(new Element\Number("Teto Pago", "data[PONTOS_TETO]", array(
    "value" => $object->getPontosTeto(),
    "required" => true,
    'min'   => '0'
)));

$form->addElement(new Element\Select("Plano", "produto[PLANO_ID]", ProdutoPeer::getPlanoList(), array(
    "value" => $object->getPlanoId(),
)));

$form->addElement(new Element\Textarea("Observação", "data[OBSERVACAO]", array(
    "value" => $object->getObservacao(),
    "required" => true
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
