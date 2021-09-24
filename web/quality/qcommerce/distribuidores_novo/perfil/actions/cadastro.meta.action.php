<?php

$arrMetaVenda = array();

$erros = array();

if (!empty($_GET['id'])) {
    $objMeta = DistribuidorMetaVendaQuery::create()->findPk($_GET['id']);
    if (!$objMeta || $objMeta->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
} else {
    $objMeta = new DistribuidorMetaVenda();
}

$isNew = $objMeta->isNew();

if (!empty($_POST['meta_venda'])) {
    $arrMetaVenda = array_map('trim', $_POST['meta_venda']);
    
    $objMeta->setByArray($arrMetaVenda, BasePeer::TYPE_FIELDNAME, $erros);
    $objMeta->setCliente(ClientePeer::getClienteLogado());
        
    if ($objMeta->myValidate($erros) && !$erros) {
        //verifica se ja existe uma meta para este mes
        $query = DistribuidorMetaVendaQuery::create()
                    ->filterByCliente(ClientePeer::getClienteLogado())
                    ->filterByDataInicial($objMeta->getDataInicial(null));
        if (!$isNew) {
            $query->filterById($objMeta->getId(), Criteria::NOT_EQUAL);
        }
        if ($query->count() > 0) {
            $erros[] = 'Já existe uma meta cadastrada para este mês.';
        }
        
        if (!$erros) {
            $objMeta->save();
        
            FlashMsg::sucesso($isNew ? 'Meta de venda cadastrada com sucesso.' : 'Meta de venda atualizada com sucesso.');
        }
    }
} else {
    $arrMetaVenda = $objMeta->toArray(BasePeer::TYPE_FIELDNAME);
    $arrMetaVenda['MES'] = $objMeta->getDataFinal('m/Y');
    $arrMetaVenda['META'] = number_format($arrMetaVenda['META'], 2, ',', '.');
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

redirect('/distribuidores_novo/perfil/');
