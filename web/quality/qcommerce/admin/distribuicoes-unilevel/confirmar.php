<?php

$distribuicao = DistribuicaoUnilevelQuery::create()->findPk($request->request->get('id'));

if ($distribuicao) {
    if ($distribuicao->getStatus() == DistribuicaoUnilevel::STATUS_PREVIEW) {
        $distribuicao->setStatus(DistribuicaoUnilevel::STATUS_AGUARDANDO);
        $distribuicao->save();
        
        $session->getFlashBag()->add('success', 'Distribuição confirmada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Distribuição não encontrada.');
}

redirect('/admin/distribuicoes-unilevel/list');
