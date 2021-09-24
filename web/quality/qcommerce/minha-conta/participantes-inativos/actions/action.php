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

$participantesRede = $clienteLogado->getParticipantesRede( null, null, $clienteSearch );

$findStatusDate = clone $dtInicio;
$findStatusMes = $findStatusDate->format('m');
$findStatusAno = $findStatusDate->format('Y');

$clientesInativos = [];
foreach( $participantesRede as $clienteRede ) :
    
    $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteRede);
    $statusCliente = $gerenciador->getStatusAtivacao( $findStatusMes, $findStatusAno );

    if(!$statusCliente) :
        $clientesInativos[] = $clienteRede;
    endif;
endforeach;