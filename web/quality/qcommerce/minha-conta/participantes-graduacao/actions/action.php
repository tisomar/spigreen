<?php
$clienteLogado = ClientePeer::getClienteLogado(true);
$gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteLogado);

$dtInicio = new DateTime('first day of last month');
$dtFim = new DateTime('last day of last month');

if(!$clienteLogado->getPlano() || $clienteLogado->getPlano()->getPlanoClientePreferencial()) :
    redirect(get_url_site() . '/minha-conta/pedidos');
endif;

if (!$clienteLogado->isMensalidadeEmDia()) :
    exit_403();
endif;


if ($inicio = $request->query->get('inicio')) :
    $dtInicio = DateTime::createFromFormat('d/m/Y', $inicio);
    if (!$dtInicio) :
        FlashMsg::danger('Data inicial é inválida.');
    else :
        $dtInicio->setTime(0, 0, 0);
    endif;
endif;

if ($fim = $request->query->get('fim')) :
    $dtFim = DateTime::createFromFormat('d/m/Y', $fim);

    if (!$dtFim) :
        FlashMsg::danger('Data final é inválida.');
    else :
        $dtFim->setTime(23, 59, 59);
    endif;
endif;

$clienteSearch = null;
if ($container->getRequest()->getMethod() == 'POST'):
    $clienteSearch = $container->getRequest()->request->get('cliente') ?? null;
endif;

$participantesRede = $clienteLogado->getParticipantesRede( $dtInicio, $dtFim, $clienteSearch );

$findStatusDate = clone $dtInicio;
$findStatusMes = $findStatusDate->format('m');
$findStatusAno = $findStatusDate->format('Y');

$clienteRedeGraduacao = [];
foreach( $participantesRede as $clienteRede ) :
    
    $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteRede);
    
    $graduacaoAtual = $gerenciador->getQualificacaoAtualHistorico($findStatusMes, $findStatusAno) != null ? $gerenciador->getQualificacaoAtualHistoricoDescricao($findStatusMes, $findStatusAno) : 'Sem graduação';
    $maiorGraduacao = ($gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao() != '') ? $gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao() : 'Sem graduação';

    $objImagemGraduacaoAtual = $gerenciador->getQualificacaoMesHistorico($findStatusMes, $findStatusAno) ? $gerenciador->getQualificacaoMesHistorico($findStatusMes, $findStatusAno)->getPlanoCarreira()->getImagem() : null;
    $objImagemMaiorGraduacao = $gerenciador->getMaiorQualificacaoAnteriorHistorico() ? $gerenciador->getMaiorQualificacaoAnteriorHistorico()->getPlanoCarreira()->getImagem() : null;

    $controlePontuacao = $clienteRede->getControlePontuacaoMes($findStatusMes, $findStatusAno);
    $PP = $controlePontuacao->getPontosPessoais() ?? 0;
    $vml = $controlePontuacao->getPontosTotais() ?? 0;

    $clienteRedeGraduacao[] = [
        'Nome' => $clienteRede->getNomeCompleto(),
        'Telefone' => $clienteRede->getTelefone(),
        'Email' => $clienteRede->getEmail(),
        'Graduacao' => $graduacaoAtual,
        'MaiorGraduacao' => $maiorGraduacao,
        'GraduacaoPathIcon' => $objImagemGraduacaoAtual,
        'MaiorGraduacaoPathIcon' => $objImagemMaiorGraduacao,
        'pontos' => $vml
    ];
endforeach;

// PAGINACAO DO ARRAY DE CLIENTES
$perpage = 10;
$countResults = count($clienteRedeGraduacao);
$pagigas = $countResults / $perpage;
$activePage = $_GET['page'] ?? 1 ;
$PrevPage = $activePage - 1 == 0 ? $activePage : $activePage - 1;
$nextPage = $activePage + 1;
$index = !empty($_GET['page']) ?  $_GET['page'] * $perpage : 0;
$clienteRedeData = array_slice($clienteRedeGraduacao, $index, $perpage);


