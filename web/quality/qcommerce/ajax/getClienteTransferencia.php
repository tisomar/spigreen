<?php



$term = isset($_GET['q']) ? $_GET['q'] : null;

if (!is_null($term) && !empty($term)) {

    $arrClientes = ClienteQuery::create()
                        ->filterById(ClientePeer::getClienteLogado()->getId(), Criteria::NOT_EQUAL)
                        ->filterByNome('%'.$term.'%', Criteria::LIKE)
                        ->_or()
                        ->filterByRazaoSocial('%'.$term.'%', Criteria::LIKE)
                        ->_or()
                        ->filterByChaveIndicacao('%'.$term.'%', Criteria::LIKE)
                        ->_or()
                        ->filterByEqualCpfCnpj($term) // 094.720.009-67
                        ->find();

    if (count($arrClientes) > 0) {
        $html = [];
        $html[] = [
            'id' => ' ',
            'text' => 'Selecione um cliente',
        ];
        foreach ($arrClientes as $cliente){
            $html[] = [
                'id' => $cliente->getId(),
                'text' => $cliente->getNomeCompleto() . ' - ' . $cliente->getChaveIndicacao(),
            ];
        }
        $return = array(
            'items'      => $html,
            'retorno'   => 'success',
            'msg'       => ''
        );
    } else {
        $return = array(
            'items'      => '',
            'retorno'   => 'error',
            'msg'       => 'Nenhum Resultado encontrado.',
        );
    }
} else {
    $return = array(
        'items'      => '',
        'retorno'   => 'error',
        'msg'       => 'Erro ao consultar.',
    );
}

echo json_encode($return);
die;
