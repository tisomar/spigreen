<?php

$clientes = ClienteQuery::create()
    ->filterByVago(0)
    ->find();

$mesAtual = Date('m', strtotime('now'));
$anoAtual = Date('Y', strtotime('now'));

/**
 * @var $cliente Cliente
 */
foreach ($clientes as $cliente) {
    $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);

    /**
     * @var $planoCarreiraHistorico PlanoCarreiraHistorico
     */
    $planoCarreiraHistorico = PlanoCarreiraHistoricoQuery::create()
        ->filterByCliente($cliente)
        ->filterByMes($mesAtual)
        ->filterByAno($anoAtual)
        ->findOne();

    $totalPontosHistorico = $planoCarreiraHistorico->getTotalPontosPessoais() +
                            $planoCarreiraHistorico->getTotalPontosAdesao() +
                            $planoCarreiraHistorico->getTotalPontosRecompra();

    if ($gerenciador->getTotalPontosMes($mesAtual, $anoAtual) != $totalPontosHistorico) :
        $qualificacao = $gerenciador->getQualificacaoMes($mesAtual, $anoAtual);

        if ($qualificacao !== null) :
            $planoCarreiraHistorico->setCliente($cliente);
            $planoCarreiraHistorico->setPlanoCarreira($qualificacao);
            $planoCarreiraHistorico->setTotalPontosPessoais($gerenciador->getTotalPontosPessoaisMes($mes, $ano));
            $planoCarreiraHistorico->setTotalPontosAdesao($gerenciador->getTotalPontosEquipeAdesaoMes($mes, $ano));
            $planoCarreiraHistorico->setTotalPontosRecompra($gerenciador->getTotalPontosEquipeRecompraMes($mes, $ano));
            $planoCarreiraHistorico->save();
        endif;
    endif;
}