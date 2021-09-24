<?php
    
    require_once __DIR__ . '/../../../includes/security.php';

require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());
$utilizacaoContaMFW = null;
if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getListas();
        if ($result->isSucesso()) {
            $utilizacaoConta = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('2- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}

    $cliente = ClientePeer::getClienteLogado();

$atividadesPeriodo = true;

if (isset($_POST['dataInicial'])) {
    $atividadesPeriodo = true;
    $dataInicial = DateTime::createFromFormat('d/m/Y', $_POST['dataInicial']);
} else {
    $dataInicial = DateTime::createFromFormat('d/m/Y', date('01/m/Y'));
}
    $dataInicial->setTime(0, 0, 0);
    
if (isset($_POST['dataFinal'])) {
    $atividadesPeriodo = true;
    $dataFinal = DateTime::createFromFormat('d/m/Y', $_POST['dataFinal']);
} else {
    $dataFinal = DateTime::createFromFormat('d/m/Y', date('t/m/Y'));
}
    $dataFinal->setTime(23, 59, 59);
    
if ($atividadesPeriodo) {
    $eventosDia = DistribuidorEventoQuery::create()
        ->filterByCliente($cliente)
        ->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO)
        ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
        ->filterByData($dataFinal, Criteria::LESS_EQUAL)
        ->orderByData(Criteria::ASC)
        ->find();
} else {
    $fim = new DateTime();
    $fim->setTime(23, 59, 59);
    $eventosDia = DistribuidorEventoQuery::create()
        ->filterByCliente($cliente)
        ->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO)
        ->orderByData(Criteria::ASC)
        ->filterByData($fim, Criteria::LESS_EQUAL)
        ->find();
}


$objMetaVenda = DistribuidorMetaVendaQuery::getMetaVendaDistribuidorNoMes($cliente);
if ($objMetaVenda) {
    $metaVenda = $objMetaVenda->getMeta();
} else {
    $metaVenda = DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor($cliente)->getMetaVendasMensal();
}

$inicio = new DateTime('first day of this month');
    $inicio->setTime(0, 0, 0);
    $fim = new DateTime('last day of this month');
    $fim->setTime(23, 59, 59);


$vendasMes = ClienteDistribuidorQuery::getValorVendasPeriodo($cliente, $inicio, $fim);

$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());

    $utilizacaoContaMFW = null;


if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
        $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->getUtilizacaoConta();
        if ($result->isSucesso()) {
            $utilizacaoContaMFW = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('3- Não foi possível ler o extrato do Mailforweb.');
    }
}


$queryAniversariantes = ClienteDistribuidorQuery::create()->filterByClienteId(ClientePeer::getClienteLogado()->getId())->filterByAniversariantesMes();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pagerAniversariantes = new QPropelPager($queryAniversariantes, 'ClienteDistribuidorPeer', 'doSelect', $page);

    $txtQtdContato = "Você já exportou <b>" . number_format($utilizacaoContaMFW['total_contatos'], 2, ',', '.') . '</b> de <b>' . number_format($utilizacaoContaMFW['max_contatos'], 2, ',', '.') . '</b> no Mail4Web.';


    $atividadesAbertas = 0;
    $atividadesFechadas = 0;
    $valorEmAberto = 0;
    $valorFechado = 0;

if ($atividadesPeriodo) {
    $atividades = $query = DistribuidorEventoQuery::create()
    ->filterByCliente(ClientePeer::getClienteLogado())
    ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
    ->filterByData($dataFinal, Criteria::LESS_EQUAL)
    ->find();
} else {
    $atividades = $query = DistribuidorEventoQuery::create()
    ->filterByCliente(ClientePeer::getClienteLogado())
    ->find();
}

    /* @var $atividade DistribuidorEvento */
foreach ($atividades as $atividade) {
    if ($atividade->getStatus() == DistribuidorEvento::STATUS_ANDAMENTO) {
        $atividadesAbertas++;
        $valorEmAberto += max(0, $atividade->getValor());
    } else {
        $atividadesFechadas++;
        $valorFechado += max(0, $atividade->getValor());
    }
}

    $criteria = ClienteDistribuidorQuery::create()->filterByClienteRedefacilId(null, Criteria::EQUAL);

    $clientes = ClientePeer::getClienteLogado()->getClienteDistribuidors($criteria);
    $clienteSemAtividade = 0;

    $c = new Criteria();
    $c->add('qp1_distribuidor_evento.STATUS', DistribuidorEvento::STATUS_ANDAMENTO);
    
    //if($atividadesPeriodo) {
    //    $c->add('qp1_distribuidor_evento.DATA', $dataInicial, Criteria::GREATER_EQUAL);
    //    $c->add('qp1_distribuidor_evento.DATA', $dataFinal, Criteria::LESS_EQUAL);
    //}

foreach ($clientes as $clienteD) {
    if (count($clienteD->getDistribuidorEventos($c)) == 0) {
        $clienteSemAtividade++;
    }
}
$clientesPendentes = ClienteDistribuidorQuery::create()
    ->addNomeCompletoColumn()
    ->addUltimaCompraColumn()
    ->filterByStatus(ClienteDistribuidor::PENDENTE, Criteria::EQUAL)
    ->filterByClienteRedefacilId(null, Criteria::EQUAL)
    ->filterByTipoLead(null, Criteria::NOT_EQUAL)
    ->filterByCliente(ClientePeer::getClienteLogado())->find();

$eventosDia = DistribuidorEventoQuery::create()
    ->filterByCliente($cliente)
    ->filterByData($inicio, Criteria::GREATER_EQUAL)
    ->filterByData($fim, Criteria::LESS_EQUAL)
    ->limit(100)
    ->find();
