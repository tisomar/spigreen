<?php

$resgate = ResgatePremiosAcumuladosQuery::create()->findPk($request->request->get('id'));

if ($resgate) {

    if ($request->request->get('situacao') != $resgate->getSituacao()) :
        $gerenciador = new GerenciadorPontosAcumulados($con = Propel::getConnection(), $logger);
        
        $situacao = $request->request->get('situacao');

        switch ($situacao) {
            case ResgatePremiosAcumulados::SITUACAO_EFETUADO:

                $extrato = $gerenciador->tentaEfetuarResgate($resgate);
                if ($extrato) {
                    $session->getFlashBag()->add('success', 'Resgate efetuado com sucesso.');
                } else {
                    $session->getFlashBag()->add('error', 'O saldo de pontos deste cliente é insuficiente.');
                }
                break;
                
            case ResgatePremiosAcumulados::SITUACAO_NAOEFETUADO:
                $cancelamento = $gerenciador->cancelaResgate($resgate);
                if ($cancelamento) {
                    $session->getFlashBag()->add('success', 'Resgate cancelado com sucesso.');
                } else {
                    $session->getFlashBag()->add('error', 'Ocorreu algum erro na tentativa de cancelamento');
                }
                break;
            
            case ResgatePremiosAcumulados::SITUACAO_PENDENTE:
                $gerenciador->marcaResgateComoPendente($resgate);
                
                $session->getFlashBag()->add('success', 'Resgate atualizado com sucesso.');
                break;
        }

    endif;
} else {
    $session->getFlashBag()->add('error', 'Solicitação de resgate não encontrada.');
    redirect('/admin/resgate-premios-acumulados/list');
}

redirect('/admin/resgate-premios-acumulados/registration/?id=' . $request->request->get('id'));
