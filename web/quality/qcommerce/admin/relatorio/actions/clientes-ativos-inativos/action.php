<?php

use Dompdf\Dompdf;

$_class = 'Cliente';

$preQuery = ClienteQuery::create()
    ->filterByVago(false)
    ->usePlanoQuery()
        ->filterByPlanoClientePreferencial(false)
    ->endUse();

$filtros = $request->query->get('filter',[]);

$mesPesquisa = $filtros['MesPesquisa'] ?? date('m');
$anoPesquisa = $filtros['AnoPesquisa'] ?? date('Y');

$dataInicio = date_create_from_format('Y-m-d', "{$anoPesquisa}-{$mesPesquisa}-01");
$dataInicio->setTime(0, 0, 0);

$dataFim = clone $dataInicio;
$dataFim->modify('last day of this month');
$dataFim->setTime(23, 59, 59);

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer = $_class::PEER;

$query_builder = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/clientes-ativos-inativos/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {

    $objects = $query_builder->find();

    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }

    $content = 'Status;Total de Pontos;Nome;Email;Telefone;Plano;Data de Cadastro;Data de Ativação' . PHP_EOL;

    foreach ($objects as $clienteAtivoInativo) :
        $situCliente = (ClientePeer::getClienteAtivoMensal($clienteAtivoInativo->getId()) == true) ? 'Ativo' : 'Inativo';
        $pontosPessoais = $clienteAtivoInativo->getControlePontuacaoMes($mesPesquisa, $anoPesquisa)->getPontosPessoais();
        $row = [
            '"' . $situCliente . '"',
            '"' . number_format($pontosPessoais, 0, ',', '.') . '"',
            '"' . htmlspecialchars(utf8_encode($clienteAtivoInativo->getNome())) . '"',
            '"' . $clienteAtivoInativo->getEmail() . '"',
            '"' . $clienteAtivoInativo->getTelefone() . '"',
            '"' . $clienteAtivoInativo->getPlano()->getNome() . '"',
            '"' . $clienteAtivoInativo->getCreatedAt('d/m/Y H:i:s') . '"',
            '"' . $clienteAtivoInativo->getDataAtivacao('d/m/Y H:i:s') . '"'
        ];

        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('clientes_ativos_inativos_%s.csv', date('Y-m-d H-i-s'));

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

// PDF
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

    /* @var $object Cliente */
    foreach ($objects as $clienteAtivoInativo) :
        $data = date('d/m/Y');
        $situCliente = (ClientePeer::getClienteAtivoMensal($clienteAtivoInativo->getId()) == true) ? 'Ativo' : 'Inativo';
        $pontosPessoais = $clienteAtivoInativo->getControlePontuacaoMes($mesPesquisa, $anoPesquisa)->getPontosPessoais();

        $dados .=
            "<tr>
                <th data-title='Total de Pontos'>{$situCliente}</th>
                <th data-title='Total de Pontos'>" . number_format($pontosPessoais, 0, ',', '.') . "</th>
                <th data-title='Nome'>{$clienteAtivoInativo->getNome()}</th>
                <th data-title='E-mail'>{$clienteAtivoInativo->getEmail()}</th>
                <th data-title='Telefone'>{$clienteAtivoInativo->getTelefone()}</th>
                <th data-title='Plano'>{$clienteAtivoInativo->getPlano()->getNome()}</th>
                <th data-title='Data de Cadastro'>{$clienteAtivoInativo->getCreatedAt('d/m/Y H:i:s')}</th>
                <th data-title='Data da Ativação'>{$clienteAtivoInativo->getDataAtivacao('d/m/Y H:i:s')}</th>
        </tr>";
    endforeach;

    $html = "
    <span style='text-align: right;'>Data geração: {$data}</span>
    <span  style='font-family:arial; text-align:center'> 
        <h2>Relatório de Clientes Ativos e Inativos</h2><br>
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
                <th>Situação</th>
                <th>Total de Pontos</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Plano</th>
                <th>Data de Cadastro</th>
                <th>Data de Ativação</th>
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
    $dompdf->stream('relatorio_clientes_ativos_inativos.pdf');
    exit();
endif;

$page = $request->query->get('page', 1);

$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

