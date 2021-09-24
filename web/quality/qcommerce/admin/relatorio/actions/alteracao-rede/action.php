<?php

use Dompdf\Dompdf;

$_class = 'AlteracaoRede';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer = $_class::PEER;

$preQuery = $classQueryName::create();

$query_builder = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();

    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }

    $content = 'Cliente Movido;Cliente Destino;Usuario Responsavel;Descricao;Data' . PHP_EOL;
    foreach ($objects as $object) :
        $clienteModivo  =  $clienteMover = ClienteQuery::create()->filterById($object->getClienteMovido())->findOne();
        $clienteDestino =  $clienteMover = ClienteQuery::create()->filterById($object->getClienteDestino())->findOne();

        $row = [
            '"' . utf8_encode($clienteModivo->getNomeCompleto()) . '"',
            '"' . utf8_encode($clienteDestino->getNomeCompleto()) . '"',
            '"' . utf8_encode($object->getUpdater()) . '"',
            '"' . $object->getDescricao() . '"',
            '"' . $object->getData('d/m/Y H:i') . '"'
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('alteracao_rede_%s.csv', date('Y-m-d H-i-s'));

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

if ($container->getRequest()->query->has('pdf')) :
    $objects = $query_builder->find();

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    $dados = '';
    $data = '';

    foreach ($objects as $dadoClientes) : /* @var $object DistribuicaoCliente */
        $clienteModivo  =  $clienteMover = ClienteQuery::create()->filterById($dadoClientes->getClienteMovido())->findOne();
        $clienteDestino =  $clienteMover = ClienteQuery::create()->filterById($dadoClientes->getClienteDestino())->findOne();

        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td data-title='Nome Cliente Movido'> {$clienteModivo->getNomeCompleto()} </td>
                <td data-title='Nome Cliente Destino'> {$clienteDestino->getNomeCompleto()} </td>
                <td data-title='Usuario Responsavel'> {$dadoClientes->getUpdater() } </td>
                <td data-title='Descricao'> {$dadoClientes->getDescricao()} </td>
                <td data-title='Data'> {$dadoClientes->getData('d/m/Y H:i')} </td>
            </tr>";
    endforeach;

    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório de Alteracão de Rede</h2><br>
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
                    <th>Nome Cliente Movido</th>
                    <th>Nome Cliente Destino</th>
                    <th>Usuário responsável</th>
                    <th>Descrição</th>
                    <th>Data</th>
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
    $dompdf->stream('relatorio_alteracao_rede.pdf');
    exit();
endif;

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$listaClientesQuery = ClienteQuery::create()
    ->select(['ID', 'NOME'])
    ->withColumn(ClientePeer::ID, 'ID')
    ->withColumn(
        sprintf(
            'IF(%s IS NOT NULL, %s, %s)',
            ClientePeer::CNPJ,
            ClientePeer::RAZAO_SOCIAL,
            ClientePeer::NOME
        ),
        'NOME'
    )
    ->filterByVago(0)
    ->addAscendingOrderByColumn('NOME')
    ->find()
    ->toArray();

$listaClientesMovidos = [
    '' => 'Selecione o cliente movido'
];

$listaClientesDestino = [
    '' => 'Selecione o cliente destino'
];

foreach ($listaClientesQuery as $cliente) :
    $listaClientesMovidos[$cliente['ID']] = $cliente['NOME'];
    $listaClientesDestino[$cliente['ID']] = $cliente['NOME'];
endforeach;
