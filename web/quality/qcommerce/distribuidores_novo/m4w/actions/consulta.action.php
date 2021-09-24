<?php
require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());

$utilizacaoConta = null;
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getUtilizacaoConta();
        if ($result->isSucesso()) {
            $utilizacaoConta = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('1- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}
