<?php

require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$erros = array();

if (!empty($_POST['convite'])) {
    $objConvite = new DistribuidorDepoimentoConvite();
    
    $arrConvite = array_map('trim', $_POST['convite']);
    
    $objConvite->setByArray($arrConvite);
        
    if ($objConvite->myValidate($erros) && !$erros) {
        //verifica se foi mesmo escolhido um cliente do distribuidor logado
        if ((!$objConvite->getClienteDistribuidor()) || $objConvite->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
            $erros[] = 'Cliente inválido.';
        }
        
        //verifica se o cliente possui um e-mail cadastrado
        if ((!$objConvite->getClienteDistribuidor())  || !$objConvite->getClienteDistribuidor()->getEmail()) {
            $erros[] = 'Cliente não possui e-mail cadastrado.';
        }
        
        if (!DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb()) {
            $erros[] = 'Você deve configurar sua chave de API do Mailforweb antes de enviar mensagem de e-mail.';
        }
        
        if (empty($erros)) {
            $objConvite->geraToken();
            
            $enviou = false;
            try {
                $integracaoMfw = new IntegracaoMailforweb(DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor(ClientePeer::getClienteLogado())->getChaveApiMailforweb());
                
                $result = $integracaoMfw->enviaEmailTransacional(
                    $objConvite->getClienteDistribuidor()->getEmail(),
                    'Depoimento',
                    Mensagem::geraMensagemEmailConviteDepoimento($objConvite),
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
                $objConvite->save();
            
                FlashMsg::sucesso('Convite enviado com sucesso.');
            } else {
                if (empty($erros)) {
                    $erros[] = 'Não foi possível enviar o convite.';
                }
            }
        }
    }
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

if (isset($_GET['v']) && $_GET['v'] == 1) {
    redirect('/distribuidores_novo/clientes/visualizacao?id=' . $objConvite->getClienteDistribuidor()->getId() . '');
}

redirect('/distribuidores_novo/clientes/');
