<?php
$cep = filter_input(INPUT_GET, 'cep');
$page = filter_input(INPUT_GET, 'page') ?? 1;

list($clientesPorCep, $final) = ClientePeer::getPatrocinadorMaisProximoPorCEP($cep, $page);

$clientes = [];

$lastMonthStart = new DateTime('first day of last month');
$lastMonthStart->setTime(0, 0, 0);

$lastMonthEnd = new DateTime('last day of last month');
$lastMonthEnd->setTime(23, 59, 59, 99999);

/** @var $cliente Cliente */
foreach ($clientesPorCep as $cliente) :
    $clientes[] = [
        'id' => $cliente->getId(),
        'nome' => $cliente->getNomeCompleto(),
        'codigo_patrocinador' => $cliente->getChaveIndicacao(),
        'cidade_uf' => $cliente->getEnderecoPrincipal() ?
            $cliente->getEnderecoPrincipal()->getCidade()->getNome() . '/' .
            $cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla() : ''
    ];
endforeach;

header('Content-Type: text/json');
echo json_encode([
    'clientes' => $clientes,
    'final' => $final
]);
exit;
