<?php
$clienteLogado = ClientePeer::getClienteLogado(true);
$gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteLogado);

if(!$clienteLogado->getPlano() || $clienteLogado->getPlano()->getPlanoClientePreferencial()) :
    redirect(get_url_site() . '/minha-conta/pedidos');
endif;

if (!$clienteLogado->isMensalidadeEmDia()) :
    exit_403();
endif;

$mes = new DateTime('');
$mesSelecionado = $mes->format('m');
$anoSelecionado = $mes->format('Y');

$mesHistoriAtual = new DateTime('');
$mesHistoriAtualSelecionado = $mesHistoriAtual->format('n');
$anoHistoriAtualSelecionado = $mesHistoriAtual->format('Y');

$mesHistoricoAnterior = new DateTime('last month');
$mesHistoricoSelecionadoAnterior = $mesHistoricoAnterior->format('n');
$anoHistoricoSelecionadoAnterior = $mesHistoricoAnterior->format('Y');

$historicoAtual = PlanoCarreiraHistoricoQuery::create()
    ->filterByCliente($clienteLogado)
    ->filterByMes($mesHistoriAtualSelecionado)
    ->filterByAno($anoHistoriAtualSelecionado)
    ->findOne();

$historicoAnterior = PlanoCarreiraHistoricoQuery::create()
    ->filterByCliente($clienteLogado)
    ->filterByMes($mesHistoricoSelecionadoAnterior)
    ->filterByAno($anoHistoricoSelecionadoAnterior)
    ->findOne();

$graduacaoSelectList = [];

$graduacaoSelected = '';
if($historicoAtual != null) :
    $graduacaoAtual = $gerenciador->getQualificacaoMesHistorico($mesSelecionado, $anoSelecionado)->getPlanoCarreira();
    if ( $graduacaoAtual->getNivel() < 8) :
        $graduacaoSelectList['atual'] = 'Graduação atual';
        $graduacaoSelected = 'atual';
        $graduacaoBanner = $graduacaoAtual->getBannerGraduacao();
    endif;
endif;

if($historicoAnterior != null) :
    $graduacaoAnterior = $gerenciador->getQualificacaoMesAnteriorHistorico($mesSelecionado, $anoSelecionado)->getPlanoCarreira();
    if ( $graduacaoAnterior->getNivel() < 8) :
        $graduacaoSelectList['anterior'] = 'Graduação anterior';
        if($graduacaoSelected == '') :
            $graduacaoSelected = 'anterior';
            $graduacaoBanner = $graduacaoAnterior->getBannerGraduacao();
        endif;
    endif;
endif;


$maiorGraduacao = $gerenciador->getMaiorQualificacaoAnteriorHistorico();
if($maiorGraduacao != null): 
    if ( $maiorGraduacao->getPlanoCarreira()->getNivel() < 8) :
        $graduacaoSelectList['maior'] = 'Maior graduação';
        if($graduacaoSelected == '') :
            $graduacaoSelected = 'maior';
            $graduacaoBanner = $maiorGraduacao->getPlanoCarreira()->getBannerGraduacao();
        endif;
    endif;
endif;

if ($request->getMethod() == 'POST') {
    $graduacaoSelected  = $request->request->get('selectGraduacao');

    switch ($graduacaoSelected) {
        case 'anterior':
            $graduacaoBanner = $graduacaoAnterior->getBannerGraduacao();
            break;
        case 'maior':
            $graduacaoBanner = $maiorGraduacao->getPlanoCarreira()->getBannerGraduacao();
            break;
        default:
            $graduacaoBanner = $graduacaoAtual->getBannerGraduacao();
            break;
    }
}