<?php

require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$erros = array();

if (!empty($_POST['cliente_id'])) {
    $objClienteDistribuidor = ClienteDistribuidorQuery::create()->findPk($_POST['cliente_id']);
    if (!$objClienteDistribuidor instanceof ClienteDistribuidor) {
        redirect_404();
    }
}

if (!$objClienteDistribuidor->getTelefoneCelular()) {
    $erros[] = 'Cliente não possui telefone celular cadastrado.';
}

if (!DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb()) {
    $erros[] = 'Você deve configurar sua chave de API do Mailforweb antes de enviar SMS.';
}

if (!empty($_POST['sms'])) {
    $arrSMS = array_map('trim', $_POST['sms']);
        
    if (empty($arrSMS['MENSAGEM'])) {
        $erros[] = 'O campo "Mensagem" é obrigatório.';
    }
    
    if (empty($erros)) {
        $integracaoMfw = new IntegracaoMailforweb(DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb());
        try {
            $result = $integracaoMfw->enviaSMS($objClienteDistribuidor->getTelefoneCelular(), $arrSMS['MENSAGEM']);
            if ($result->isSucesso()) {
                FlashMsg::sucesso('Mensagem enviada com sucesso.');
            } else {
                $erros = $result->getErros();
                if (empty($erros)) {
                    //se o mailforweb não retornou nenhuma mensagem de erro, adiciona uma mensagem genérica.
                    FlashMsg::erro('Não foi possível enviar sua mensagem.');
                }
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            FlashMsg::erro('Falha na comunicação com o Mailforweb.');
        }
    }
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

if (isset($_GET['v']) && $_GET['v'] == 1) {
    redirect('/distribuidores_novo/distribuidores/visualizacao?id=' . $_POST['cliente_id']);
}

if (isset($_GET['pag'])) {
    if ($_GET['pag'] == 'home') {
        redirect('/distribuidores_novo/');
    }
    
    redirect('/distribuidores_novo/' . $_GET['pag'] . '/');
}

redirect('/distribuidores_novo/distribuidores/');
