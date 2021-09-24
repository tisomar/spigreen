<?php 

$clientes = ClienteQuery::create()
    ->filterByNomeRazaoSocial($request->query->get('q', ''))
    ->filterByVago(0)
    ->filterByClienteIndicadorId(null, Criteria::ISNOTNULL)
    ->find();

if (count($clientes) > 0) {
    $html = [];
    $html[] = [
        'id' => '',
        'text' => 'Selecione um cliente',
    ];
    foreach ($clientes as $cliente){
        $html[] = [
            'id' => $cliente->getId(),
            'text' => $cliente->getNomeCompleto() . ' - ' . $cliente->getChaveIndicacao(),
        ];
    }
    $return = [
        'items' => $html,
        'retorno' => 'success',
        'msg' => ''
    ];
} else {
    $return = [
        'items' => '',
        'retorno' => 'error',
        'msg' => 'Nenhum Resultado encontrado.',
    ];
}

echo json_encode($return);
