<?php

use Dompdf\Dompdf;
use phpDocumentor\Reflection\Types\Array_;

$sql = isset($_SESSION['SQL_RELATORIO_ESTOQUE']) ? $_SESSION['SQL_RELATORIO_ESTOQUE'] : null;

$dateAtual = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
$primeiroDiaMes = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-01');

$dataInicial = $primeiroDiaMes->format('Y-m-d');
$dataFinal = $dateAtual->format('Y-m-d');
$parametro = ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal();

$queryClientesZerar = "
        SELECT cli.CHAVE_INDICACAO,
               IF(cli.CNPJ IS NULL, cli.NOME, cli.RAZAO_SOCIAL) NOME,
               COALESCE(ped.TOTAL, 0) AS TOTAL_PONTOS
          FROM qp1_cliente cli
          LEFT JOIN (SELECT p.CLIENTE_ID,
                            COALESCE(SUM(p.VALOR_PONTOS),0) AS TOTAL
                       FROM qp1_pedido p
                      WHERE p.CREATED_AT BETWEEN '{$dataInicial} 00:00:00' AND '{$dataFinal} 23:59:59'
                        AND p.STATUS <> 'CANCELADO'
                        AND EXISTS (SELECT 1
                                      FROM qp1_pedido_status_historico psh
                                     WHERE psh.PEDIDO_ID = p.ID
                                       AND psh.PEDIDO_STATUS_ID = 1 -- Aguardando Pagamento
                                       AND psh.IS_CONCLUIDO = 1)
                      GROUP BY p.CLIENTE_ID) ped
            ON cli.ID = ped.CLIENTE_ID
         WHERE cli.TREE_LEFT > 0
           AND cli.VAGO = 0
           AND cli.PLANO_ID IS NOT NULL
           AND COALESCE(ped.TOTAL, 0) < {$parametro}
         ORDER BY TOTAL_PONTOS DESC,
                  NOME";

$con = Propel::getConnection();

$stmt = $con->query($queryClientesZerar);
$stmt->execute();

$query_builder = $stmt;

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/clientes-sem-compra/filter.basic.action.query.php';

if ($container->getRequest()->query->has('sql_estoque') && $container->getRequest()->query->get('sql_estoque') == true) {
    $_SESSION['SQL_RELATORIO_ESTOQUE'] = $_SESSION['QUERY_ESTOQUE'];
    unset($_SESSION['QUERY_ESTOQUE']);
    redirect('/admin/relatorio/controle-estoque/');
}

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder;

    if (count(array($objects)) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    $content = 'CodigoPatrocinador;Nome;TotalPontos' . PHP_EOL;
    foreach ($objects as $cliente) :
        $dados = [
            '"' . $cliente['CHAVE_INDICACAO'] . '"',
            '"' . $cliente['NOME'] . '"',
            '"' . $cliente['TOTAL_PONTOS'] . '"',
        ];
        $content .= implode(';', $dados) . PHP_EOL;
    endforeach;
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('clientes_sem_compra_%s.csv', date('Y-m-d H-i-s'));

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
    $objects = $query_builder;

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    if (count(array($objects)) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    $dados = '';
    $data = '';

    foreach ($objects as $cliente) : /* @var $object Cliente */

        $data = date('d/m/Y');
        $dados .=
            "<tr>
            <td data-title='Nome'> {$cliente['CHAVE_INDICACAO']} </td>
            <td data-title='Telefone'> {$cliente['NOME']} </td>
            <td data-title='Email'> {$cliente['TOTAL_PONTOS']} </td>
        </tr>";
    endforeach;

    $html = "
    <span style='text-align: right;'>Data geração: {$data}</span>
    <span  style='font-family:arial; text-align:center'> 
        <h2>Relatório de Clientes sem Compra</h2>

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
                <th>Código Patrocinador</th>
                <th>Nome</th>
                <th>Total Pontos</th>
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
    $dompdf->stream('relatorio_clientes_sem_compra.pdf');

    exit();
endif;

// $page = $request->query->get('page', 1);
// $pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 50);
// $container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
