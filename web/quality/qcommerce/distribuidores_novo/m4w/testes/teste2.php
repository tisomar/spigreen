<?php

    require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

    $host = 'https://mail4web.com.br'; //host do mail4web

    $funcao = '/api/contatos/importa'; //funcao da API
    $apikey = 'x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps'; //chave de API do usuario


    $listas = array('Lista Distribuidores', 'Lista Distribuidores 1');

    $contatos = array(
        array('nome' => 'Wallace',
            'email' => 'wperini@gmail.com'),
        array('nome' => 'João da Silva',
            'email' => 'joaosilva@gmail.com'));

    //$integracaoMfw = new IntegracaoMailforweb(DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor($objCliente)->getChaveApiMailforweb());
    $integracaoMfw = new IntegracaoMailforweb($apikey);

    $result = $integracaoMfw->integraContatos($listas, $contatos);

    echo json_encode($result->getRetorno());
