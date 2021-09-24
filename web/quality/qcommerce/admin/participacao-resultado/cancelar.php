<?php

$participacao = ParticipacaoResultadoQuery::create()->findPk($request->request->get('id'));

if ($participacao) {
    if (in_array($participacao->getStatus(), array(ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW, ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW, ParticipacaoResultado::STATUS_PREVIEW, ParticipacaoResultado::STATUS_AGUARDANDO))) {
        $participacao->setStatus(ParticipacaoResultado::STATUS_CANCELADO);
        $participacao->save();
        
        $session->getFlashBag()->add('success', 'Participação resultados cancelada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Participação resultados não encontrada.');
}

redirect('/admin/participacao-resultado/list');
