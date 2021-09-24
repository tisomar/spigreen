<?php

$clienteLogado = ClientePeer::getClienteLogado(true);
$plano = $clienteLogado->getPlano();

// Calcula o percentual de progresso para atingir a próxima graduação do plano de carreira no mês atual
$gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteLogado);
$planoAtual = $clienteLogado->getPlanoCarreira(date('m'), date('Y'));

$menssagemRequisitos = '';

if ($plano && $planoAtual) :
    $planoCarreiraAtual = $planoAtual->getPlanoCarreira();

    $proximoPlano = PlanoCarreiraQuery::create()->findOneByNivel($planoCarreiraAtual->getNivel() + 1);

    if ($proximoPlano) :
        $totalPontosAtual = $gerenciador->getTotalPontosPorVlp($proximoPlano, date('m'), date('Y'));

        $percentualProgresso = round($totalPontosAtual * 100 / $proximoPlano->getPontos(), 2);

        if ($percentualProgresso >= 100) :
            $percentualProgresso = 100;

            $graduacaoReq = PlanoCarreiraQuery::create()
                ->filterById($proximoPlano->getRequGraduacao())
                ->findOne();

            $percentualVPL = ($proximoPlano->getAproveitamentoLinha() * 100) /  $proximoPlano->getPontos();

            $reqDireto = $proximoPlano->getRequDireto() == true ? ' direta(s) ' : '';
            $reqQuantidade = $proximoPlano->getRequQuantidade() . ' pessoa(s) ';
            $menssagemRequisitos =  "Parabéns, você atingiu a pontuação necessária para subir de graduação, porém necessita dos seguintes requisitos: <br>
                Ter $reqQuantidade {$reqDireto} contendo graduação de {$graduacaoReq->getGraduacao()} ativa(s) em sua rede.<br>
                Lembrando que o VPL máximo é de $percentualVPL %<br>";
        endif;
    endif;
endif;

/** CAPTURA DA REQUISIÇÃO O MÊS E O ANO SELECIONADO */
$mesSelecionado = $request->query->get('mes');
$anoSelecionado = $request->query->get('ano', date('Y'));

/** POPULA OS SELECTS */
if ($anoSelecionado == '2019') :
    $meses = [
        '' => 'Selecione o mês',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
else:
    $meses = [
        '' => 'Selecione o mês',
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
endif;

// PEGANDO DOTOS OS "ANOS" existentes na coluna DATA da tabela EXTRATO
$listaAno = ExtratoQuery::create()
    ->distinct()
    ->select(['ANO'])
    ->withColumn("year(DATA)", 'ANO')
    ->orderBy('ANO', Criteria::DESC)
    ->find();

if (empty($mesSelecionado)) :
    $mesSelecionado = date('m');
    $anoSelecionado = date('Y');
endif;

$controlePontos = $clienteLogado->getControlePontuacaoMes($mesSelecionado, $anoSelecionado);

$totalPontosPeriodo = $controlePontos->getPontosTotais() ?? 0;
