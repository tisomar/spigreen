<?php

$arrCliente = array();

$erros = array();

if (!empty($_POST['id']) && $_POST['id'] != -1) {
    $objClienteDistribuidor = ClienteDistribuidorQuery::create()->findPk($_POST['id']);
    if (!$objClienteDistribuidor) {
        redirect_404();
    }
} else {
    $objClienteDistribuidor = new ClienteDistribuidor();
}

$objEvento = null;

$isNew = $objClienteDistribuidor->isNew();

if (isset($_POST['cliente_distribuidor']) && !empty($_POST['cliente_distribuidor'])) {
    $arrClienteDistribuidor = array_map('trim', $_POST['cliente_distribuidor']);
    
    $arrClienteDistribuidor['TELEFONE_CELULAR'] = str_replace('_', '', $arrClienteDistribuidor['TELEFONE_CELULAR']);
    $arrClienteDistribuidor['WHATSAPP'] = str_replace('_', '', $arrClienteDistribuidor['WHATSAPP']);
    $arrClienteDistribuidor['TELEFONE'] = str_replace('_', '', $arrClienteDistribuidor['TELEFONE']);
    
    $objClienteDistribuidor->setByArray($arrClienteDistribuidor, BasePeer::TYPE_FIELDNAME, $erros);
    $objClienteDistribuidor->setCliente(ClientePeer::getClienteLogado());
    
    if ($isNew && !empty($_POST['inserir_primeiro_evento'])) {
        $arrEvento = array_map('trim', $_POST['evento']);
        
        $objEvento = new DistribuidorEvento();
        $objEvento->setByArray($arrEvento, BasePeer::TYPE_FIELDNAME, $erros);
        $objEvento->setCliente(ClientePeer::getClienteLogado());
    }
        
    if ($objClienteDistribuidor->myValidate($erros) && !$erros) {
        $con = Propel::getConnection(ClienteDistribuidorPeer::DATABASE_NAME);
        $con->beginTransaction();
        
        $objClienteDistribuidor->save();
        
        if ($objEvento) {
            $objEvento->setClienteDistribuidor($objClienteDistribuidor);
            
            if ($objEvento->myValidate($erros) && !$erros) {
                $objEvento->save();
            }
        }
        
        if (!$erros) {
            $con->commit();
            
            FlashMsg::sucesso($isNew ? 'Cliente cadastrado com sucesso.' : 'Cliente atualizado com sucesso.');
        } else {
            $con->rollBack();
        }
    }
} else {
    $arrClienteDistribuidor = $objClienteDistribuidor->toArray(BasePeer::TYPE_FIELDNAME);
    if (empty($arrClienteDistribuidor['TIPO'])) {
        $arrClienteDistribuidor['TIPO'] = ClienteDistribuidor::TIPO_PESSOA_FISICA;
    }
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}


if (isset($_GET['pag'])) {
    if ($_GET['pag'] == 'home') {
        redirect('/distribuidores_novo/');
    } elseif ($_GET['pag'] == 'visualizacao') {
        redirect('/distribuidores_novo/clientes/visualizacao/?id=' . $objClienteDistribuidor->getId());
    }
    
    redirect('/distribuidores_novo/' . $_GET['pag'] . '/');
}

redirect('/distribuidores_novo/clientes/');
