<?php

    $arrTemplateSms = array();

if (!empty($_GET['id'])) {
    $objTemplateSms = DistribuidorTemplateQuery::create()->findPk($_GET['id']);
    if (!$objTemplateSms || $objTemplateSms->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
}
    
    $arrTemplateSms['ASSUNTO']  = $objTemplateSms->getAssunto();
    $arrTemplateSms['MENSAGEM'] = $objTemplateSms->getMensagem();
    $arrTemplateSms['CATEGORIA'] = $objTemplateSms->getCategoria();
    $arrTemplateSms['TIPO']     = strtoupper(max($objTemplateSms->getTipo(), $tipoTemplate));
    
    echo json_encode($arrTemplateSms);
