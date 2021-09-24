<?php

require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';







$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());

$utilizacaoContaMFW = null;
$cadastro = array();
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getCadastroDireto();

        if ($result->isSucesso()) {
            $cadastro = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('5- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}

echo json_encode($cadastro);
die;
