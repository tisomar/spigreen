<?php

$arrTemplateSms = array();

$erros = array();

if (!empty($_POST['id']) && $_POST['id'] != -1) {
    $objTemplateSms = DistribuidorTemplateQuery::create()->findPk($_POST['id']);
    if (!$objTemplateSms || $objTemplateSms->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
} else {
    $objTemplateSms = new DistribuidorTemplate();
}

$isNew = $objTemplateSms->isNew();

if (!empty($_POST['template_sms'])) {
    $arrTemplateSms = array_map('trim', $_POST['template_sms']);

    $objTemplateSms->setByArray($arrTemplateSms, BasePeer::TYPE_FIELDNAME, $erros);
    $objTemplateSms->setCliente(ClientePeer::getClienteLogado());

    if ($_POST['id'] == -1) {
        $objTemplateSms->setAtivo(true);
    }

    if ($objTemplateSms->myValidate($erros) && !$erros) {
        //verifica se ja existe uma meta para este mes
        $query = DistribuidorTemplateQuery::create()
            ->filterByCliente(ClientePeer::getClienteLogado());

        if (!$isNew) {
            $query->filterById($objTemplateSms->getId(), Criteria::NOT_EQUAL);
        }

        if (!$erros) {
            $objTemplateSms->save();

            FlashMsg::sucesso($isNew ? 'Modelo cadastrado com sucesso.' : 'Modelo atualizado com sucesso.');

            redirect('/distribuidores_novo/modelos/' . $tipoTemplate);
        }
    }
} else {
    $arrTemplateSms['ASSUNTO']  = $objTemplateSms->getAssunto();
    $arrTemplateSms['MENSAGEM'] = $objTemplateSms->getMensagem();
    $arrTemplateSms['TIPO']     = strtoupper(max($objTemplateSms->getTipo(), $tipoTemplate));
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}
