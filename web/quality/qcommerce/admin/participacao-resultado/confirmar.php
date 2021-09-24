<?php

$participacao = ParticipacaoResultadoQuery::create()->findPk($request->request->get('id'));

if ($participacao) {
    if ($participacao->getStatus() == ParticipacaoResultado::STATUS_PREVIEW) {
        $participacao->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao->save();
        
        $session->getFlashBag()->add('success', 'Participação resultados confirmada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Participação resultados não encontrada.');
}

redirect('/admin/participacao-resultado/list');
