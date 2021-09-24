<?php
/* @var $object FaturamentoDireto */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
));


$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true,
)));

$form->addElement(new Element\Radio("Definir como uma opção padrão?", "data[PADRAO]", array(
    1 => 'Sim', 0 => 'Não'
), array(
    "value" => $object->getPadrao(),
    "required" => true,
    "shortDesc" => 'Caso escolha <b>sim</b>, esta opção ficará disponível para todos os clientes. Do contrário' .
        ' esta opção aparecerá apenas para os clientes que possuirem ela associada em seus cadastros.'
)));

$form->addElement(new Element\Number("Número de Parcelas:", "data[NUMERO_PARCELAS]", array(
    "value" => $object->getNumeroParcelas(),
    "required" => true,
)));

$form->addElement(new Element\Textbox("Valor mínimo no pedido", "data[VALOR_MINIMO_COMPRA]", array(
    "value" => 'R$ ' . format_money($object->getValorMinimoCompra()),
    "required" => true,
    "class" => "mask-money",
    'longDesc' => 'Define o valor mínimo de compras (sem o frete) para disponibilizar esta forma de pagamento.'
)));

$form->addElement(new Element\Textarea("Observação interna:", "data[OBSERVACAO_INTERNA]", array(
    "value" => $object->getObservacaoInterna(),
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
