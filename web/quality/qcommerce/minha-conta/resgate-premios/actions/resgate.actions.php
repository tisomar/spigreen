<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$con = Propel::getConnection();
$gerenciador = new GerenciadorPontosAcumulados($con = Propel::getConnection(), $logger);

$cliente = ClientePeer::getClienteLogado(true);
$clienteId = $cliente->getId();

$premiosList = PremiosAcumuladosQuery::create()->orderByPontosResgate(Criteria::ASC)->find();

$pontosReservados = $gerenciador->getTotalPontosReservaResgatePremiacao($cliente);
$pontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($cliente);

$maxGraduacaoCliente = $gerenciador->getMaxGraduacao($clienteId);
$maxGraduacaoClienteDesc = PlanoCarreiraQuery::create()
->filterByID($maxGraduacaoCliente, Criteria::EQUAL)
->findOne()->getGraduacao();

if ($request->request->has('resgate')) :

    $premioSolicitado = $request->request->get('resgate')['PREMIO'];
    $pontosResgate = $request->request->get('resgate')['PONTOS'];

    $tipoPremioDinheiro = strpos($premioSolicitado, '$');

    if($tipoPremioDinheiro == false) {
        $tipoPremio = 'PREMIO';
    }else{
        $tipoPremio = 'DINHEIRO';
    }

    $resgatesPendentes = new ResgatePremiosAcumulados();
    $resgatesPendentes->setData(date('Y-m-d H:i:s'));
    $resgatesPendentes->setCliente($cliente);
    $resgatesPendentes->setPontosResgate($pontosResgate);
    $resgatesPendentes->setPremio($premioSolicitado);
    $resgatesPendentes->setSelecionado($tipoPremio);
    $resgatesPendentes->save();

    FlashMsg::success('Solicitação cadastrada com sucesso.');
    redirect('/minha-conta/resgate-premios');
endif;


