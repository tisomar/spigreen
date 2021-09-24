<?php
$clienteLogado = ClientePeer::getClienteLogado(true);

/** Variaveis para copiar o link de indicação */
$linkIndicacao = get_url_site() . '/home/validPatrocinador?codigo_patrocinador=' . $clienteLogado->getChaveIndicacao();

$gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
$htmlRede = $gerenciador->geraHTMLRede($clienteLogado, 'rede-clientes', true);

$gerenciadorBonus = new GerenciadorBonusRedeBinaria(Propel::getConnection(), $logger);
$totaisProximaDistribuicao = $gerenciadorBonus->getTotaisProximaDistribuicaoCliente($clienteLogado);
$pontosTotais = $totaisProximaDistribuicao['total'];
$pontosLadoEsquerdo = $totaisProximaDistribuicao['esquerda'];
$pontosLadoDireito = $totaisProximaDistribuicao['direita'];
$arrClienteGeracao = $clienteLogado->getRedeIndicacaoDiretaCliente(2);

$patrocinadorNome = '';
$patrocinadorChave = '';

if ($clienteLogado->getClienteIndicadorId() != null) :
    $patrocinadorDireto = $clienteLogado->getPatrocinadorDireto();
    $patrocinadorNome = $patrocinadorDireto->getNome();
    $patrocinadorChave = $patrocinadorDireto->getChaveIndicacao();
endif;

if(!$clienteLogado->getPlano() || $clienteLogado->getPlano()->getPlanoClientePreferencial()) :
    redirect(get_url_site() . '/minha-conta/pedidos');
endif;

/** Código para copiar o link do patrocinador */
$linkIndicacao = get_url_site() . '/home/validPatrocinador?codigo_patrocinador=' . $clienteLogado->getChaveIndicacao();

$gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
$htmlRede = $gerenciador->geraHTMLRede($clienteLogado, 'rede-clientes', true);

$gerenciadorBonus = new GerenciadorBonusRedeBinaria(Propel::getConnection(), $logger);
$totaisProximaDistribuicao = $gerenciadorBonus->getTotaisProximaDistribuicaoCliente($clienteLogado);
$pontosTotais = $totaisProximaDistribuicao['total'];
$pontosLadoEsquerdo = $totaisProximaDistribuicao['esquerda'];
$pontosLadoDireito = $totaisProximaDistribuicao['direita'];
$arrClienteGeracao = $clienteLogado->getRedeIndicacaoDiretaCliente(2);

$patrocinadorNome = '';
$patrocinadorChave = '';

if ($clienteLogado->getClienteIndicadorId() != null) :
    $patrocinadorDireto = $clienteLogado->getPatrocinadorDireto();
    $patrocinadorNome = $patrocinadorDireto->getNome();
    $patrocinadorChave = $patrocinadorDireto->getChaveIndicacao();
endif;


if (!$clienteLogado->isMensalidadeEmDia()) :
    exit_403();
endif;

$nomePlano = $clienteLogado->getPlano()->getNome();

$pontosAcumulados = 0;
$nivel = 1;

$colPlanoCarreira = PlanoCarreiraQuery::create()
    ->orderByNivel()
    ->find();

$mesSelecionado = $request->query->get('mes');
$anoSelecionado = !empty($request->query->get('ano')) ? $request->query->get('ano') : date('Y');

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

$startDate = new DateTime();
$endDate = new DateTime();

if(!empty($mesSelecionado) ) :
    $startDate->setDate($startDate->format('Y'), $mesSelecionado, 1);
    $endDate->setDate($endDate->format('Y'), $mesSelecionado, 1);
endif;

if (!empty($anoSelecionado) ) :
    $startDate->setDate($anoSelecionado, $startDate->format('m'), 1);
    $endDate->setDate($anoSelecionado, $endDate->format('m'), 1);
endif;

$startDate->modify('first day of this month');
$startDate->setTime(0,0,0);

$endDate->modify('last day of this month');
$endDate->setTime(23,59,59);

if (empty($mesSelecionado)) :
    $mesSelecionado = date('m');
    $anoSelecionado = date('Y');
endif;

$gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteLogado);

$graduacaoAtual = $gerenciador->getQualificacaoAtualHistorico($mesSelecionado, $anoSelecionado) != null ? $gerenciador->getQualificacaoAtualHistoricoDescricao($mesSelecionado, $anoSelecionado) : 'Sem graduação';
$graduacaoAnterior = ($gerenciador->getQualificacaoMesAnteriorHistoricoDescricao($mesSelecionado, $anoSelecionado)) != '' ? $gerenciador->getQualificacaoMesAnteriorHistoricoDescricao($mesSelecionado, $anoSelecionado): 'Sem graduação' ;
$maiorGraduacao = ($gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao() != '') ? $gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao() : 'Sem graduação';
$statusCliente = $gerenciador->getStatusAtivacao( $mesSelecionado, $anoSelecionado);

$controlePontos = $clienteLogado->getControlePontuacaoMes($mesSelecionado, $anoSelecionado);

$PP = $controlePontos->getPontosPessoais() ?? 0;
$PA = $controlePontos->getPontosAdesao() ?? 0;
$PR = $controlePontos->getPontosRecompra() ?? 0;
$totalPontos = $controlePontos->getPontosTotais();

// PEGAR IMAGEM DA GRADUACAO
$objImagemGraduacaoAnterior = $gerenciador->getQualificacaoMesAnteriorHistorico($mesSelecionado, $anoSelecionado) ? $gerenciador->getQualificacaoMesAnteriorHistorico($mesSelecionado, $anoSelecionado)->getPlanoCarreira()->getImagem() : null;
$objImagemGraduacaoAtual = $gerenciador->getQualificacaoMesHistorico($mesSelecionado, $anoSelecionado) ? $gerenciador->getQualificacaoMesHistorico($mesSelecionado, $anoSelecionado)->getPlanoCarreira()->getImagem() : null;
$objImagemMaiorGraduacao = $gerenciador->getMaiorQualificacaoAnteriorHistorico() ? $gerenciador->getMaiorQualificacaoAnteriorHistorico()->getPlanoCarreira()->getImagem() : null;

// Calcula o percentual de progresso para atingir a próxima graduação do plano de carreira no mês atual
$planoAtual = $gerenciador->getQualificacaoAtualHistorico($mesSelecionado, $anoSelecionado);
$menssagemRequisitos = null;
if ($planoAtual) :
    $planoAtual = $planoAtual->getPlanoCarreira();
    
    $proximoPlano = PlanoCarreiraQuery::create()
        ->filterByNivel($planoAtual->getNivel() + 1)
        ->findOne();

    if ($proximoPlano) :
        $totalPontosAtual = $gerenciador->getTotalPontosPorVlp($proximoPlano, $mesSelecionado, $anoSelecionado);
        
        $percentualProgresso = round($totalPontosAtual * 100 / $proximoPlano->getPontos(), 2);

        // $percentualProgresso = (100 / ($proximoPlano->getAproveitamentoLinha() - $planoAtual->getAproveitamentoLinha())) * ($totalPontosAtual - $planoAtual->getAproveitamentoLinha());
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
            Lembrando que o VPL máximo é de $percentualVPL %<br>
            ";
        endif;
    endif;
endif;

$nivelCarreira = PlanoCarreiraQuery::create()->select(['NIVEL', 'PONTOS'])->find();
$listaCarreira = [];

foreach ($nivelCarreira as $carreira) :
    $listaCarreira[] = [$carreira['PONTOS']];
endforeach;

$diasMesGraficoCarreira = [];
$diasMes = cal_days_in_month(CAL_GREGORIAN, $mesSelecionado, $anoSelecionado);

for ($i=1; $i <= $diasMes; $i++) :
    $diasMesGraficoCarreira[] = $i;
endfor;

$planoCarreiraList = PlanoCarreiraQuery::create()
    ->orderByNivel()
    ->find();

$planos = [];

$planos[0] = 'Sem graduação';
foreach ($planoCarreiraList as $planoCarreira) :
    $planos[$planoCarreira->getNivel()] = $planoCarreira->getGraduacao();
endforeach;

// ARRAY COM DADOS FICTICIOS SENDO MONTADOS NO GRAFICO
$teste = array();
$teste[0] = [1, 1];
$teste[1] = [2, 1];
$teste[2] = [3, 2];
$teste[3] = [4, 2];
$teste[4] = [5, 3];
$teste[5] = [6, 3];
$teste[6] = [7, 4];
$teste[7] = [8, 5];
$teste[8] = [9, 5];
$teste[9] = [10, 5];
$teste[10] = [11, 5];
$teste[11] = [12, 6];
$teste[12] = [13, 6];
$teste[13] = [14, 6];
$teste[14] = [15, 6];
$teste[15] = [16, 7];
$teste[16] = [17, 7];
$teste[17] = [18, 8];
$teste[18] = [19, 9];
$teste[19] = [20, 9];
$teste[20] = [21, 10];
$teste[21] = [22, 10];
$teste[22] = [23, 10];
$teste[23] = [24, 11];
$teste[24] = [25, 11];
$teste[25] = [26, 11];
$teste[26] = [27, 11];
$teste[27] = [28, 12];
$teste[28] = [29, 12];
$teste[29] = [30, 13];
$teste[30] = [31, 13];

$teste2 = array();
$teste2[0] =  [1, 0];
$teste2[1] =  [2, 0];
$teste2[2] =  [3, 0];
$teste2[3] =  [4, 0];
$teste2[4] =  [5, 0];
$teste2[5] =  [6, 0];
$teste2[6] =  [7, 0];
$teste2[7] =  [8, 0];
$teste2[8] =  [9, 0];
$teste2[9] =  [10, 0];
$teste2[10] = [11, 0];
$teste2[11] = [12, 0];
$teste2[12] = [13, 0];
$teste2[13] = [14, 0];
$teste2[14] = [15, 0];
$teste2[15] = [16, 0];
$teste2[16] = [17, 0];
$teste2[17] = [18, 0];
$teste2[18] = [19, 0];
$teste2[19] = [20, 0];
$teste2[20] = [21, 0];
$teste2[21] = [22, 0];
$teste2[22] = [23, 0];
$teste2[23] = [24, 0];
$teste2[24] = [25, 0];
$teste2[25] = [26, 0];
$teste2[26] = [27, 0];
$teste2[27] = [28, 0];
$teste2[28] = [29, 0];
$teste2[29] = [30, 0];
$teste2[30] = [31, 0];
