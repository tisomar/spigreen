<?php

$con = Propel::getConnection();
$linkPreview = get_url_admin() . '/bonus-aceleracao-preview/list?participacao_resultado_id=' . $request->request->get('distribuicaoid');

$distribuicao = ParticipacaoResultadoQuery::create()
    ->filterById($request->request->get('distribuicaoid'))
    ->findOne();

if ($distribuicao) :
    $totalPontosDistribuicao = $distribuicao->getTotalPontos();

    $participacaoCliente = ParticipacaoResultadoClienteQuery::create()
        ->filterByParticipacaoResultadoId($request->request->get('distribuicaoid'))
        ->filterByClienteId($request->request->get('id'))
        ->findOne();

    $valorDistribuidoCliente = $participacaoCliente->getTotalPontos();

    $distribuicao->setTotalPontos($totalPontosDistribuicao - $valorDistribuidoCliente);
    $distribuicao->save();

    if($distribuicao->getTotalPontos() == $totalPontosDistribuicao - $valorDistribuidoCliente): 
        $participacaoCliente->delete();

        $session->getFlashBag()->add('success', 'Participação removida com sucesso.');
        redirect($linkPreview);
    endif;
else:
    $session->getFlashBag()->add('error', 'Distribuição não encontrada.');
    redirect(get_url_admin() . '/bonus-produtos/list/');
endif;