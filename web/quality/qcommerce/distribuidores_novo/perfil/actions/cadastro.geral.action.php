<?php

$objConfiguracao = DistribuidorConfiguracaoQuery::create()->findOneByClienteId(ClientePeer::getClienteLogado()->getId());
if (!$objConfiguracao) {
    $objConfiguracao = new DistribuidorConfiguracao();
    $objConfiguracao->setCliente(ClientePeer::getClienteLogado());
}

if (!empty($_POST['configuracao'])) {
    $arrConfiguracao = array_map('trim', $_POST['configuracao']);
    
    $objConfiguracao->setByArray($arrConfiguracao);
    
    if ($objConfiguracao->myValidate($erros) && !$erros) {
        $objConfiguracao->save();
        
        if (isset($arrConfiguracao['META_VENDAS_MENSAL'])) {
            FlashMsg::sucesso('Meta anual salva com sucesso.');
        } else {
            FlashMsg::sucesso('Configurações salvas com sucesso.');
        }
        
        redirect('/distribuidores_novo/perfil/');
    }
} else {
    $arrConfiguracao = $objConfiguracao->toArray(BasePeer::TYPE_FIELDNAME);
    $arrConfiguracao['META_VENDAS_MENSAL'] = number_format($arrConfiguracao['META_VENDAS_MENSAL'], 2, ',', '.');
}
