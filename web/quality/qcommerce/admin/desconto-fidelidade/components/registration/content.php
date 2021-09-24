<?php
use PFBC\View;
use PFBC\Form;
use PFBC\Element;

/** @var $object DescontoFidelidade */


$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));


//$form->addElement(new Element\Textarea("Descrição da classificação", "data[OBSERVACAO]", array(
//    "value" => $object->getObservacao(),
//    'class' => '',
//)));

$form->addElement(new Element\Number("Mês inicial", "data[MES_INICIAL]", array(
    "value" => $object->getMesInicial(),
    "required" => true,
    'min'   => '0'
)));

$form->addElement(new Element\Number("Mês final", "data[MES_FINAL]", array(
    "value" => $object->getMesFinal(),
    "required" => true,
    'min'   => '0'
)));

$form->addElement(new Element\Textbox("Percentual (use ponto para separar as casas decimais se necessário)", "data[PERCENTUAL_DESCONTO]", array(
    "value" => $object->getPercentualDesconto(),
    "required" => true
)));

//
//
//$form->addElement(new Element\Number("Geração de Pontos", "data[GERACAO_PONTOS]", array(
//    "value" => $object->getGeracaoPontos(),
//    "required" => true,
//    'min'   => '0'
//)));
//
//$form->addElement(new Element\Number("Desconto Revenda", "data[DESCONTO_REVENDA]", array(
//    "value" => $object->getDescontoRevenda(),
//    "required" => true,
//    'min'   => '0',
//    'max'   => '100'
//)));
//
//$form->addElement(new Element\Select("Indicação Direta", "data[INDICACAO_DIRETA]", PlanoPeer::getValueSet(PlanoPeer::INDICACAO_DIRETA), array(
//    "value" => $object->getIndicacaoDireta()
//)));
//
//$form->addElement(new Element\Select("Indicação Indireta", "data[INDICACAO_INDIRETA]", PlanoPeer::getValueSet(PlanoPeer::INDICACAO_INDIRETA), array(
//    "value" => $object->getIndicacaoIndireta()
//)));
//
//$form->addElement(new Element\Select("Residual", "data[RESIDUAL]", PlanoPeer::getValueSet(PlanoPeer::RESIDUAL), array(
//    "value" => $object->getResidual()
//)));
//
//$form->addElement(new Element\Select("Plano adiciona/libera cliente na rede binária?", "data[ATIVA_REDE]", PlanoPeer::getValueSet(PlanoPeer::ATIVA_REDE), array(
//    "value" => $object->getAtivaRede()
//)));
//
//$form->addElement(new Element\Number("Rede Binária", "data[REDE_BINARIA]", array(
//    "value" => $object->getRedeBinaria(),
//    "required" => true,
//    'min'   => '0',
//    'max'   => '100'
//)));
//
//$form->addElement(new Element\Select("Part. Lucros", "data[PARTICIPACAO_LUCROS]", PlanoPeer::getValueSet(PlanoPeer::PARTICIPACAO_LUCROS), array(
//    "value" => $object->getParticipacaoLucros()
//)));
//
//
//$form->addElement(new Element\Select("Plano de Carreira", "data[PLANO_CARREIRA]", PlanoPeer::getValueSet(PlanoPeer::PLANO_CARREIRA), array(
//    "value" => $object->getPlanoCarreira()
//)));
//
//$form->addElement(new Element\Select("Produto Inicial", "data[PRODUTO_ID]", PlanoPeer::getProdutoList(), array(
//    "value" => $object->getProdutoId(),
//)));


$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
