<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cliente = ClientePeer::getClienteLogado(true);
$clienteId = $cliente->getId();

// $list = ResgateQuery::create()
//     ->filterByClienteId($clienteId)
//     ->orderByData(Criteria::DESC)
//     ->find();


$mesResgate = ResgateQuery::create()
    ->filterByClienteId($clienteId)
    ->orderByData(Criteria::DESC)
    ->find();


// $optionResgate = '';
// foreach ($mesResgate as $resgate) :
//     $optionResgate = '<option value"%d">'.'Data da solicitação: '.$resgate->getData('d/m/Y')./*' - '.' Valor R$ '.$resgate->getValor().' - '.'Situação: '.$resgate->getSituacao().*/'</option>';
// endforeach;

$optionResgate = '';
foreach ($mesResgate as $resgate) :
    $optionResgate .= sprintf(
        '<option value="%d">%s</option>',
        $resgate->getId(),
        $resgate->getData('d/m/Y')
    );
endforeach;
