<?php
use Dompdf\Dompdf;
use Doctrine\Common\Collections\Criteria;
use PFBC\Element\DateTime;

$_class = 'Cliente';
$preQuery = ClienteQuery::create()->filterByVago(0)->orderByNome('ASC');

$gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);
$inicio = date('1-1-2018 00:00:00');
$dataDe = new \DateTime($inicio);
$dataAte = new \DateTime();

$dataFiltro = ['min' => $dataDe , 'max' => $dataAte];

if(!empty($request->query->get('filter')) ) :
    $request->query->set('is_filter', true);
endif;

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;
$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/bonus-disponiveis/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();
    
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Cliente;Total bônus disponíveis;Toral transferência enviadas;Toral transferência recebida;Total resgate;Total pagamento com bônus' . PHP_EOL;
    foreach ($objects as $object) :

        $clienteNome = $object->getNomeCompleto();
        $clientId = $object->getId();

        $totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($object, $dataFiltro['min'], $dataFiltro['max'], 'INDICACAO_DIRETA');

        // ENVIADAS
        $transferenciasEnviadas = TransferenciaQuery::create()
        ->select(['somaenviadas'])
        ->filterByClienteRemetenteId($clientId)
        ->filterByData($dataFiltro)
        ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somaenviadas')
        ->findOne();

        // RECEBIDAS
        $transferenciasRecebidas = TransferenciaQuery::create()
        ->select(['somarecebidas'])
        ->filterByClienteDestinatarioId($clientId)
        ->filterByData($dataFiltro)
        ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somarecebidas')
        ->findOne();

        // TotalResgate 
        $totalResgate = ResgateQuery::create()
        ->select(['somaResgate'])
        ->filterByClienteId($clientId)
        ->filterByData($dataFiltro)
        ->filterBySituacao('EFETUADO')
        ->withColumn("SUM(VALOR)", 'somaResgate')
        ->findOne();

        // Total Pagamento de pedidos com bônus
        $pagementoPedidosComBonus = ExtratoQuery::create()
        ->select(['somaPagamentos'])
        ->filterByTipo('PAGAMENTO_PEDIDO')
        ->filterByClienteId($clientId)
        ->withColumn("SUM(PONTOS)", 'somaPagamentos')
        ->filterByData($dataFiltro)
        ->filterByOperacao('-')
        ->findOne();

        $row = [
            '"' . htmlspecialchars($clienteNome) . '"',
            '"R$ ' . number_format($totalPontosDisponiveis, '2', ',', '.') . '"',
            '"R$ ' . number_format($transferenciasEnviadas, '2', ',', '.') . '"',
            '"R$ ' . number_format($transferenciasRecebidas, '2', ',', '.') . '"',
            '"R$ ' . number_format($totalResgate, '2', ',', '.') . '"',
            '"R$ ' . number_format($pagementoPedidosComBonus, '2', ',', '.') . '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('bonus_disponiveis_%s.csv', date('Y-m-d H-i-s'));

    // Definindo header de saída
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Expires: 0");
    header("Pragma: public");

    // Enviando headers para o browser
    $fp = fopen('php://output', 'w');

    fwrite($fp, $content);
    fclose($fp);
    exit();
}

// PDF Export
if ($container->getRequest()->query->has('pdf')) :
    // $query_builder->select(array('Nome', 'Telefone', 'Email', 'Aniversario'/*, 'Idade'*/));

    $objects = $query_builder->find();
    // var_dump($objects);
    
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    $dados = '';
    $data = '';

    foreach ($objects as $object) : /* @var $object Resgate */
        $clienteNome = $object->getNomeCompleto();
        $clientId = $object->getId();

        $totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($object, $dataFiltro['min'], $dataFiltro['max'], 'INDICACAO_DIRETA');

        // ENVIADAS
        $transferenciasEnviadas = TransferenciaQuery::create()
        ->select(['somaenviadas'])
        ->filterByClienteRemetenteId($clientId)
        ->filterByData($dataFiltro)
        ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somaenviadas')
        ->findOne();

        // RECEBIDAS
        $transferenciasRecebidas = TransferenciaQuery::create()
        ->select(['somarecebidas'])
        ->filterByClienteDestinatarioId($clientId)
        ->filterByData($dataFiltro)
        ->withColumn("SUM(QUANTIDADE_PONTOS)", 'somarecebidas')
        ->findOne();

        // TotalResgate 
        $totalResgate = ResgateQuery::create()
        ->select(['somaResgate'])
        ->filterByClienteId($clientId)
        ->filterByData($dataFiltro)
        ->filterBySituacao('EFETUADO')
        ->withColumn("SUM(VALOR)", 'somaResgate')
        ->findOne();

        // Total Pagamento de pedidos com bônus
        $pagementoPedidosComBonus = ExtratoQuery::create()
        ->select(['somaPagamentos'])
        ->filterByTipo('PAGAMENTO_PEDIDO')
        ->filterByClienteId($clientId)
        ->withColumn("SUM(PONTOS)", 'somaPagamentos')
        ->filterByData($dataFiltro)
        ->filterByOperacao('-')
        ->findOne();

        $totalPontosDisponiveis = number_format($totalPontosDisponiveis, '2', ',', '.') ?? 0;
        $transferenciasEnviadas = number_format($transferenciasEnviadas, '2', ',', '.') ?? 0;
        $transferenciasRecebidas = number_format($transferenciasRecebidas, '2', ',', '.') ?? 0;
        $totalResgate = number_format($totalResgate, '2', ',', '.') ?? 0;
        $pagementoPedidosComBonus = number_format($pagementoPedidosComBonus, '2', ',', '.') ?? 0;

        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td data-title='Nome'>$clienteNome</td>
                <td data-title='TotalBonusDisponiveis'>R$ $totalPontosDisponiveis </td>
                <td data-title='TransferenciasEnviadas'>R$ $transferenciasEnviadas </td>
                <td data-title='TransferenciasRecebidas'>R$ $transferenciasRecebidas </td>
                <td data-title='TotalResgate'>R$ $totalResgate </td>
                <td data-title='PagementoPedidosComBonus'>R$ $pagementoPedidosComBonus </td>
            </tr>";
    endforeach;

    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório de Bônus disponíveis</h2><br>
        </span>

        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align: left;
            }
        </style>
            
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='table table-hover table-striped'>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Total bônus disponíveis</th>
                    <th>Toral transferência enviadas</th>
                    <th>Toral transferência recebida</th>
                    <th>Total resgate</th>
                    <th>Total pagamento com bônus</th>
                </tr>
            </thead>
            <tbody>
                $dados
            <tbody>
        </table>";

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('relatorio_bonus_disponiveis.pdf');
    exit();
endif;

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$listaClientes = ClienteQuery::create()
    ->orderByNome()
    ->find()
    ->toArray();