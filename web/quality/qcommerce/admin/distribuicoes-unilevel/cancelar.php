<?php

$distribuicao = DistribuicaoUnilevelQuery::create()->findPk($request->request->get('id'));

if ($distribuicao) {
    if (in_array($distribuicao->getStatus(), array(DistribuicaoUnilevel::STATUS_AGUARDANDO_PREVIEW, DistribuicaoUnilevel::STATUS_PROCESSANDO_PREVIEW,
            DistribuicaoUnilevel::STATUS_PREVIEW, DistribuicaoUnilevel::STATUS_AGUARDANDO))) {
        $distribuicao->setStatus(DistribuicaoUnilevel::STATUS_CANCELADO);
        $distribuicao->save();
        
        $session->getFlashBag()->add('success', 'Distribuição cancelada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Distribuição não encontrada.');
}

redirect('/admin/distribuicoes-unilevel/list');
