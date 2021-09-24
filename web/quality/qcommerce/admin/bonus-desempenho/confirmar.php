<?php

$participacao = ParticipacaoResultadoQuery::create()->findPk($request->request->get('id'));

if ($participacao) {
    if ($participacao->getStatus() == ParticipacaoResultado::STATUS_PREVIEW) {
        $participacao->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao->save();
        
        $session->getFlashBag()->add('success', 'Bônus desempenho confirmada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Bônus desempenho resultados não encontrada.');
}

redirect('/admin/bonus-desempenho/list');
