<?php
    
    require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';
    $query = DistribuidorMetaVendaQuery::create()
        ->filterByCliente(ClientePeer::getClienteLogado())
        ->orderByDataFinal(Criteria::DESC);

    $objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());
    
    $utilizacaoContaMFW = null;
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getUtilizacaoConta();
        if ($result->isSucesso()) {
            $utilizacaoContaMFW = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('4- Não foi possível ler o extrato do Mailforweb.');
    }
}
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pager = new QPropelPager($query, 'DistribuidorMetaVendaPeer', 'doSelect', $page);
