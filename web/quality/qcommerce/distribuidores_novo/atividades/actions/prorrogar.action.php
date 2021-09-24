<?php

$erros = array();

$objEvento = DistribuidorEventoQuery::create()->findPk($_POST['id']);
if (!$objEvento || $objEvento->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
    redirect_404();
}

if (!empty($_POST['data'])) {
    $con = Propel::getConnection(DistribuidorEventoPeer::DATABASE_NAME);
    $con->beginTransaction();
    
    $objEvento->setData(DateTime::createFromFormat('d/m/Y', $_POST['data']));
    
    if (!empty($_POST['assunto'])) {
        $objEvento->setAssunto($_POST['assunto']);
    }
    
    if ($objEvento->myValidate($erros) && !$erros) {
        if (!$erros) {
            $objEvento->save();
            
            $con->commit();
            
            FlashMsg::sucesso('Atividade prorrogada com sucesso.');
        }
        
        if ($con->inTransaction()) {
            $con->rollBack();
        }
    }
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

if (isset($_GET['pag'])) {
    redirect('/distribuidores_novo/' . $_GET['pag'] . '/');
}

redirect('/distribuidores_novo/atividades/');
