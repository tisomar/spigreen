<?php
if (!ClientePeer::isAuthenticad()) {
    header('Location: ' . $root_path . '/login/index/');
    exit();
}

$erros = array();

if (!empty($_POST['id']) && $_POST['id'] != -1) {
    $objEvento = DistribuidorEventoQuery::create()->findPk($_POST['id']);
    if (!$objEvento || $objEvento->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
} else {
    $objEvento = new DistribuidorEvento();
}


$objRecorrencia = null;

$isNew = $objEvento->isNew();

if (isset($_POST['evento']) && !empty($_POST['evento'])) {
    $con = Propel::getConnection(DistribuidorEventoPeer::DATABASE_NAME);
    $con->beginTransaction();

    $arrEvento = array_map('trim', $_POST['evento']);

    $arrRecorrencia = array_map('trim', isset($_POST['recorrencia']) ? (array)$_POST['recorrencia'] : array());

    $objEvento->setByArray($arrEvento, BasePeer::TYPE_FIELDNAME, $erros);
    $objEvento->setCliente(ClientePeer::getClienteLogado());
//    $objEvento->setDistribuidorTemplateIdPerda($_POST['modeloATIVIDADE']);

    if ($objEvento->myValidate($erros) && !$erros) {
        //verifica se foi mesmo escolhido um cliente do distribuidor logado
        if ((!$objEvento->getClienteDistribuidor()) || $objEvento->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
            $erros[] = 'Cliente inválido.';
        }

        if (!empty($arrRecorrencia['RECORRENCIA'])) {
            //gera uma recorrencia
            $objRecorrencia = new DistribuidorEvento();

            $objRecorrencia->setByArray($arrRecorrencia, BasePeer::TYPE_FIELDNAME, $erros);
            $objRecorrencia->setCliente(ClientePeer::getClienteLogado());
            $objRecorrencia->setClienteDistribuidor($objEvento->getClienteDistribuidor());

            $objRecorrencia->myValidate($erros);
        }

        if (!$erros) {
            if (DistribuidorEvento::STATUS_FINALIZADO == $objEvento->getStatus() && !$objEvento->getDataFechamento()) {
                $objEvento->setDataFechamento(new DateTime());
            }

            $objEvento->save();

            if ($objRecorrencia) {
                $objRecorrencia->save();
            }

            $clienteDistribuidor = $objEvento->getClienteDistribuidor();
            $clienteDistribuidor->setLead(false);
            $clienteDistribuidor->save();

            $con->commit();

            FlashMsg::sucesso($isNew ? 'Atividade cadastrada com sucesso.' : 'Atividade atualizada com sucesso.');
        }

        if ($con->inTransaction()) {
            $con->rollBack();
        }
    }
} else {
    $arrEvento = $objEvento->toArray(BasePeer::TYPE_FIELDNAME);
    $arrEvento['DATA'] = $objEvento->getData('d/m/Y');
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

if (isset($_GET['pag'])) {
    if ($_GET['pag'] == 'home') {
        redirect('/distribuidores_novo/');
    }

    redirect('/distribuidores_novo/' . $_GET['pag'] . '/');
}

redirect('/distribuidores_novo/atividades/');
