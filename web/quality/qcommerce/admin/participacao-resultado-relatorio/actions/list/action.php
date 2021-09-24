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
    $objects = $query_builder->find();
    
    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;
    
    $content = 'Cliente;Valor' . PHP_EOL;
    foreach ($objects as $dados) :
        $clienteNome = $dados->getCliente()->getNome();  

        $row = [
            '"' . htmlspecialchars($clienteNome) . '"',
            '"' . number_format($dados->getTotalPontos(), '2', ',', '') . '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('bonus_destaque_relatorio_%s.csv', date('Y-m-d H-i-s'));

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
    foreach ($objects as $object) : /* @var $object DistribuicaoCliente */
        $data = date('d/m/Y', strtotime($object->getData()));
        $bonus = number_format(escape($object->getTotalPontos()), '2', ',', '');
        $dados .=
            "<tr>
                <td data-title='Nome'> {$object->getCliente()->getNomeCompleto()}</td>
                <td data-title='Bonus'>R$ {$bonus}</td>
            </tr>";
    endforeach;
    
    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório Bônus Destaque</h2><br>
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
                    <th>Bônus</th>
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
    $dompdf->stream('relatorio_bonus_destaque.pdf');
    exit();
endif;

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);
