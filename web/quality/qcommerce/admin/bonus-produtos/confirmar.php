<?php

$distribuicao = DistribuicaoBonusProdutosQuery::create()->findPk($request->request->get('id'));

if ($distribuicao) {
    if ($distribuicao->getStatus() == Distribuicao::STATUS_PREVIEW) {
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $distribuicao->save();
        
        $session->getFlashBag()->add('success', 'Distribuição confirmada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Distribuição não encontrada.');
}

redirect('/admin/bonus-produtos/list');
