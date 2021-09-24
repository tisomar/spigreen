<?php
// reference the Dompdf namespace
use Dompdf\Dompdf;

if (!isset($_class)) :
    trigger_error('você deve definir a classe $_class');
endif;

$classQueryName = $_class . 'Query';

if (!isset($preQuery)) :
    $preQuery = null;
endif;

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/actions/' . $router->getAction() . '/filter.basic.action.php';


if ($container->getRequest()->query->has('exportar')) :
    $objects   = $query_builder->find();

    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    
    $content = 'Cliente;Indicacao_Indireta;Recompra;Lideranca;Total' . PHP_EOL;
    foreach ($objects as $dados) :
        $clienteNome = $dados->getCliente()->getNome();  
        $row = [
            '"' . htmlspecialchars($clienteNome) . '"',
            '"' . number_format($dados->getTotalPontosAdesao(), '2', ',', '') . '"',
            '"' . number_format($dados->getTotalPontosRecompra(), '2', ',', '') . '"',
            '"' . number_format($dados->getTotalPontosLideranca(), '2', ',', '') . '"',
            '"' . number_format($dados->getTotalPontos(), '2', ',', '') . '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('bonus_desempenho_relatorio_%s.csv', date('Y-m-d H-i-s'));

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
endif;

if ($container->getRequest()->query->has('pdf')) :
    $objects   = $query_builder->find();

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    $dados = '';
    $data = '';
    foreach ($objects as $object) : /* @var $object DistribuicaoCliente */
        $data = date('d/m/Y', strtotime($object->getData()));
        $indicacaoIndireta = number_format($object->getTotalPontosAdesao(), '2', ',', '');
        $recompra = number_format($object->getTotalPontosRecompra(), '2', ',', '');
        $lideranca = number_format($object->getTotalPontosLideranca(), '2', ',', '');
        $total = number_format($object->getTotalPontos(), '2', ',', '');

        $dados .=
            "<tr>
                <td data-title='Nome'> {$object->getCliente()->getNomeCompleto()} </td>
                <td data-title='PontosAdesao'>R$ {$indicacaoIndireta} </td>
                <td data-title='PontosRecompra'>R$ {$recompra} </td>
                <td data-title='PontosLideranca'>R$ {$lideranca} </td>
                <td data-title='Pontos'>R$ {$total} </td>
            </tr>";
    endforeach;
    
    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório distribuíçao</h2><br>
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
                <th>Indicação Indireta</th>
                <th>Recompra</th>
                <th>Liderança</th>
                <th>Total R$</th>
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
    $dompdf->stream('relatorio_distribuicao.pdf');
    exit();
endif;

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);
