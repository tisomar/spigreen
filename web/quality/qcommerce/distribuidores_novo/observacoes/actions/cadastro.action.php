<?php

$erros = array();

if (!empty($_GET['id'])) {
    $objObservacao = ClienteDistribuidorObservacaoQuery::create()->findPk($_GET['id']);
    if (!$objObservacao || $objObservacao->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
        redirect_404();
    }
} else {
    $objObservacao = new ClienteDistribuidorObservacao();
}

$isNew = $objObservacao->isNew();

if (!empty($_POST['observacao'])) {
    $arrObservacao = array_map('trim', $_POST['observacao']);
    
    $objObservacao->setByArray($arrObservacao);
    
    if ($objObservacao->myValidate($erros) && !$erros) {
        //verifica se foi mesmo escolhido um cliente do distribuidor logado
        if ((!$objObservacao->getClienteDistribuidor()) || $objObservacao->getClienteDistribuidor()->getClienteId() != ClientePeer::getClienteLogado()->getId()) {
            $erros[] = 'Cliente inválido.';
        }
        
        if (!$erros) {
            $objObservacao->save();

            FlashMsg::sucesso($isNew ? 'Observação cadastrada com sucesso.' : 'Observação atualizada com sucesso.');

            redirect('/distribuidores/observacoes');
        }
    }
} else {
    $arrObservacao = $objObservacao->toArray(BasePeer::TYPE_FIELDNAME);
}

foreach ($erros as $erro) {
    FlashMsg::erro($erro);
}

$breadcrumb = array(
    'Observações' => $root_path . '/distribuidores_novo/observacoes',
    'Cadastro' => ''
);
