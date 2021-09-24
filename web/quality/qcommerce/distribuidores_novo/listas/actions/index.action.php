<?php
$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());

$utilizacaoContaMFW = null;
$pager = array();
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getListas();
        if ($result->isSucesso()) {
            $pager = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('6- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}
$segmentos = array();
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getSegmentos();

        if ($result->isSucesso()) {
            $segmentos = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('6- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}

$remetentes = array();
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getRemetentes();

        if ($result->isSucesso()) {
            $remetentes = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('6- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}

$email_conta = "";
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getEmilConta();

        if ($result->isSucesso()) {
            $email_conta = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('6- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}
