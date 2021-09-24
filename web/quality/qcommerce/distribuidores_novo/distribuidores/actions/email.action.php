<?php

require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$erros = array();

if (!empty($_POST['cliente_id'])) {
    $objClienteDistribuidor = ClienteDistribuidorQuery::create()->findPk($_POST['cliente_id']);
    if (!$objClienteDistribuidor instanceof ClienteDistribuidor) {
        redirect_404();
    }
}

if (!empty($_POST['email'])) {
    $arrEmail = array_map('trim', $_POST['email']);
    
    if (empty($arrEmail['ASSUNTO'])) {
        $erros[] = 'O campom "Assunto" é obrigatório.';
    }
    
    if (empty($arrEmail['MENSAGEM'])) {
        $erros[] = 'O campom "Mensagem" é obrigatório.';
    }
    
    if (!$objClienteDistribuidor->getEmail()) {
        $erros[] = 'Cliente não possui e-mail cadastrado.';
    }
    
    if (!DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb()) {
        $erros[] = 'Você deve configurar sua chave de API do Mailforweb antes de enviar mensagem de e-mail.';
    }
    
    if (!$erros) {
        $enviou = false;
        try {
            $integracaoMfw = new IntegracaoMailforweb(DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb());
                        
            $result = $integracaoMfw->enviaEmailTransacional(
                $objClienteDistribuidor->getEmail(),
                $arrEmail['ASSUNTO'],
                Mensagem::geraMensagemEmailClienteDistribuidor(nl2br($arrEmail['MENSAGEM'])),
                ClientePeer::getClienteLogado()->getEmail(),
                ClientePeer::getClienteLogado()->getNomeCompleto(),
                null,
                null,
                null
            );
            
            $enviou = $result->isSucesso();
            if (!$enviou) {
                $erros = $result->getErros();
            }
        } catch (Exception $ex) {
            $enviou = false;
            error_log($ex->getMessage());
        }
        
        if ($enviou) {
            FlashMsg::sucesso('Mensagem enviada com sucesso.');
        } else {
            if (empty($erros)) {
                //se o mailforweb não retornou nenhuma mensagem de erro, adiciona uma mensagem genérica.
                $erros[] = 'Não foi possível enviar a mensagem.';
            }
        }
    }
}

$templatesEMAIL = DistribuidorTemplateQuery::create()
    ->filterByCliente(ClientePeer::getClienteLogado())
    ->filterByTipo(DistribuidorTemplate::TIPO_EMAIL)
    ->orderByAssunto()
    ->find();

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
