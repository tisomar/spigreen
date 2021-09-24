<?php

$distribuicao = DistribuicaoQuery::create()->findPk($request->request->get('id'));

if ($distribuicao) {
    if (in_array($distribuicao->getStatus(), array(Distribuicao::STATUS_AGUARDANDO_PREVIEW, Distribuicao::STATUS_PROCESSANDO_PREVIEW, Distribuicao::STATUS_PREVIEW, Distribuicao::STATUS_AGUARDANDO))) {
        $distribuicao->setStatus(Distribuicao::STATUS_CANCELADO);
        $distribuicao->save();
        
        $session->getFlashBag()->add('success', 'Distribuição cancelada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Distribuição não encontrada.');
}

redirect('/admin/distribuicoes/list');
