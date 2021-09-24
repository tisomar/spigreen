<?php

$participacao = ParticipacaoResultadoQuery::create()->findPk($request->request->get('id'));

if ($participacao) {
    if ($participacao->getStatus() == ParticipacaoResultado::STATUS_PREVIEW) {
        $participacao->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao->save();
        
        $session->getFlashBag()->add('success', 'Bônus aceleração confirmada com sucesso.');
    }
} else {
    $session->getFlashBag()->add('error', 'Bônus aceleração resultados não encontrada.');
}

redirect('/admin/bonus-aceleracao/list');
