<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);

echo Date('Y-m-d H:i:s');
echo '<br><br>';

$clientes = ClienteQuery::create()
    ->filterByVago(0)
    ->where('Cliente.PlanoId IS NOT NULL')
    ->find();

// Criação do plano de carreira para o período do mès 7 a 10 de 2019

/**
 * @var $cliente Cliente
 */
foreach ($clientes as $cliente) :
    $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);

    // Somente criará o historico se o cliente possui pontos no mês
    // e qualificação com base nos requisitos do plano de carreira
    $dataInicio = '01/07/2019';
    $dataFim = '31/10/2019';

    if ($gerenciador->getTotalPontosPessoaisPeriodo($dataInicio, $dataFim) >= ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal()) :
        $qualificacao = $gerenciador->getPrimeiraQualificacao($dataInicio, $dataFim);
        if ($qualificacao !== null) :
            $clientePlanoHistorico = new PlanoCarreiraHistorico();
            $clientePlanoHistorico->setCliente($cliente);
            $clientePlanoHistorico->setPlanoCarreira($qualificacao);
            $clientePlanoHistorico->setMes('10');
            $clientePlanoHistorico->setAno('2019');
            $clientePlanoHistorico->setTotalPontosPessoais($gerenciador->getTotalPontosPessoaisPeriodo($dataInicio, $dataFim));
            $clientePlanoHistorico->setTotalPontosAdesao($gerenciador->getTotalPontosEquipeAdesaoPeriodo($dataInicio, $dataFim));
            $clientePlanoHistorico->setTotalPontosRecompra($gerenciador->getTotalPontosEquipeRecompraPeriodo($dataInicio, $dataFim));
            $clientePlanoHistorico->save();

            var_dump('adicionado histórico do cliente : '.$cliente->getId().', Plano de carreira: '.$clientePlanoHistorico->getPlanoCarreira()->getId().', Período: '.$clientePlanoHistorico->getMes().'-'.$clientePlanoHistorico->getAno());
        endif;
    endif;
endforeach;

// Criação do histórico do plano de carreira referente aos meses após o mes 10 de 2019

$contMes = 11;
$contAno = 2019;

$mesAtual = (int) Date('m', strtotime('now'));
$anoAtual = (int) Date('Y', strtotime('now'));

do {
    if (($contAno > $anoAtual) || ($contAno === $anoAtual && $contMes > $mesAtual)) :
        break;
    endif;

    $mes = $contMes > 9 ? (string) $contMes : '0'.$contMes;
    $ano = (string) $contAno;

    /**
     * @var $cliente Cliente
     */
    foreach ($clientes as $cliente) :
        $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);

        // Somente criará o historico se o cliente possui pontos no mês
        // e qualificação com base nos requisitos do plano de carreira
        if ($gerenciador->getTotalPontosPessoaisMes($mes, $ano) >= ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal()) :
            $qualificacao = $gerenciador->getQualificacaoMes($mes, $ano);

            if ($qualificacao !== null) :
                $clientePlanoHistorico = new PlanoCarreiraHistorico();
                $clientePlanoHistorico->setCliente($cliente);
                $clientePlanoHistorico->setPlanoCarreira($qualificacao);
                $clientePlanoHistorico->setMes($mes);
                $clientePlanoHistorico->setAno($contAno);
                $clientePlanoHistorico->setTotalPontosPessoais($gerenciador->getTotalPontosPessoaisMes($mes, $ano));
                $clientePlanoHistorico->setTotalPontosAdesao($gerenciador->getTotalPontosEquipeAdesaoMes($mes, $ano));
                $clientePlanoHistorico->setTotalPontosRecompra($gerenciador->getTotalPontosEquipeRecompraMes($mes, $ano));
                $clientePlanoHistorico->save();
            endif;
        endif;
    endforeach;

    if ($contMes === 12) :
        $contMes = 0;
        $contAno++;
    endif;

    $contMes++;
} while(true);

echo Date('Y-m-d H:i:s');
echo '<br><br>';