<?php

$resgate = ResgateQuery::create()->findPk($request->request->get('id'));

if ($resgate) {
    if (($situacao = $request->request->get('situacao')) && $situacao != $resgate->getSituacao()) {
        $gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);
        
        switch ($situacao) {
            case Resgate::SITUACAO_EFETUADO:
                $extrato = $gerenciador->tentaEfetuarResgate($resgate);
                if ($extrato) {
                    $session->getFlashBag()->add('success', 'Resgate efetuado com sucesso.');
                } else {
                    $session->getFlashBag()->add('error', 'O saldo de pontos deste cliente é insuficiente.');
                }
                break;
                
            case Resgate::SITUACAO_NAOEFETUADO:
                $gerenciador->cancelaResgate($resgate);
                $session->getFlashBag()->add('success', 'Resgate cancelado com sucesso.');
                break;
            
            case Resgate::SITUACAO_PENDENTE:
                $gerenciador->marcaResgateComoPendente($resgate);
                $session->getFlashBag()->add('success', 'Resgate atualizado com sucesso.');
                break;
        }
    }
} else {
    $session->getFlashBag()->add('error', 'Solicitação de resgate não encontrada.');
    redirect('/admin/resgate/list');
}

redirect('/admin/resgate/registration/?id=' . $request->request->get('id'));
