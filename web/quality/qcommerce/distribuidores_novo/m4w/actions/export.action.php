<?php

    require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

    $objConfiguracao    = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());
    $integracaoMfw      = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
/*

    $listas = array('Lista Distribuidores');

    $contatos = array(
        array('nome' => 'Wallace',
            'email' => 'wperini@gmail.com'),
        array('nome' => 'João da Silva',
            'email' => 'joaosilva@gmail.com'));

    $fp = fopen("bloco1.txt", "a");

    // Escreve "exemplo de escrita" no bloco1.txt
    $escreve = fwrite(
            $fp, json_encode($_POST['listas']) . '<br>' .
                 json_encode(unserialize($_POST['contatos'])) . '<br>' .
                 json_encode($listas) . '<br>' .
                     json_encode($contatos));

    // Fecha o arquivo
    fclose($fp);
*/

    $result = $integracaoMfw->integraContatos($_POST['listas'], unserialize($_POST['contatos']));

    echo json_encode($result->getRetorno());
