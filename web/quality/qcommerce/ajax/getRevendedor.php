<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 25/04/2018
 * Time: 09:28
 */

if ($container->getRequest()->getMethod() == 'POST') {
    $isFilter = $container->getRequest()->request->get('filtered');

    $sqlClientes = ClienteQuery::create()
        ->filterByNaoCompra(0)
        ->filterByTreeLeft(0, Criteria::GREATER_THAN)
        ->filterByPlanoId(null, Criteria::NOT_EQUAL)
        ->groupByChaveIndicacao()
        ->innerJoinHotsite();

    if (!$isFilter) {
        $filters = array_filter($container->getRequest()->request->all());
        $getFilter = false;

        if (isset($filters['text'])) {
            $getFilter = true;
            $sqlClientes
                    ->filterByNome('%' . $filters['text'] . '%', Criteria::LIKE)
                    ->_or()
                    ->filterByEmail('%' . $filters['text'] . '%', Criteria::LIKE)
                    ->_or()
                    ->filterByChaveIndicacao('%' . $filters['text'] . '%', Criteria::LIKE)
                    ->_or()
                    ->filterByCpfCnpj('%' . $filters['text'] . '%')
                    ->_or()
                    ->useHotsiteQuery()
                        ->filterByNome('%' . $filters['text'] . '%', Criteria::LIKE)
                        ->_or()
                        ->filterBySlug('%' . $filters['text'] . '%', Criteria::LIKE)
                    ->endUse();
        }

        if (isset($filters['cidade'])) {
            $sqlClientes
                ->useEnderecoQuery()
                    ->useCidadeQuery()
                        ->_if($getFilter)
                            ->_or()
                        ->_endif()
                        ->filterById($filters['cidade'], Criteria::EQUAL)
                    ->endUse()
                ->endUse();

            $getFilter = true;
        }

        if (isset($filters['estado'])) {
            $sqlClientes
                    ->useEnderecoQuery()
                        ->useCidadeQuery()
                            ->useEstadoQuery()
                                ->_if($getFilter)
                                    ->_or()
                                ->_endif()
                                ->filterById($filters['estado'], Criteria::EQUAL)
                            ->endUse()
                        ->endUse()
                    ->endUse();
        }
    } else {
        if (ClientePeer::isAuthenticad() && !is_null(ClientePeer::getClienteLogado(true)->getEnderecoPrincipal())) {
            $sqlClientes
                ->useEnderecoQuery()
                    ->useCidadeQuery()
                        ->filterById(ClientePeer::getClienteLogado(true)->getEnderecoPrincipal()->getCidadeId())
                    ->endUse()
                ->endUse();
        }
    }

    $arrClientes = $sqlClientes->find();

    if (count($arrClientes) > 0) {
        $html = '';
        $count = 1;
        foreach ($arrClientes as $cliente) {
            /** @var Cliente $cliente */
            /** @var Endereco $endereco */

            //Endereço principal do cliente.
            $endereco = $cliente->getEnderecoPrincipal();

            // Cor de fundo do painel para contraste
            $bg = checkEvenOrOdd($count);
            $count++;

            $html .= '<div class="row panel-body' . $bg . ' vertical-align">

                        <div class="col-xs-12 col-md-9">
                            <b><h3>' . $cliente->getNomeCompleto() . ' - ' . $cliente->getChaveIndicacao() . '</h3></b>
                            <br />
                            <p>' . $endereco->getLogradouro() . ' Nº: ' . $endereco->getNumero() . ', ' . $endereco->getBairro() . '</p>
                            <p>' . $endereco->getCidade()->getNome() . '/' . $endereco->getCidade()->getEstado()->getNome() . '</p>
                            <b>Telefone: ' . $cliente->getTelefone() . '</b>
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <button class="btn btn-block btn-primary rev-selected" data-name="' . $cliente->getNomeCompleto() . '" id="rev-' . $cliente->getId() . '">Selecionar</button>
                        </div>
                    </div> <hr>';
        }

        if (!empty($html)) {
            $return = array(
                'html'      => $html,
                'retorno'   => 'success',
                'msg'       => 'Consultado com sucesso.'
            );
        } else {
            $return = array(
                'html'      => '',
                'retorno'   => 'error',
                'msg'       => 'Erro ao consultar os revendedores.'
            );
        }
    } else {
        $html = '<div class="panel-body bg-default">
                    <b><h3>Nenhum consultor encontrado</h3></b>
                 </div>';
        $return = array(
            'html'      => $html,
            'retorno'   => 'success',
            'msg'       => 'Nenhum consultar encontrado.'
        );
    }
} else {
    $return = array(
        'html'      => '',
        'retorno'   => 'error',
        'msg'       => 'Método de pesquisa inválido.'
    );
}

echo json_encode($return);
die;


function checkEvenOrOdd($number)
{
    if ($number % 2 == 0) {
        return ' bg-default';
    } else {
        return '';
    }
}
