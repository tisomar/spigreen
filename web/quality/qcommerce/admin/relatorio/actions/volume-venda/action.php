<?php
use Dompdf\Dompdf;

include QCOMMERCE_DIR . '/admin/relatorio/helpers/functions.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';


/**
 * Define o agrupamento.
 * Se a diferença de dias for menor ou igual a 30 dias, agrupa por dia.
 * Do contrário, efetua o agrupamento por mês.
 */

$groupByOptions = array(
   'month' => 'MES, ANO',
   'day' => 'DIA, MES, ANO',
   'hour' => 'HORA, DIA, MES, ANO',
);

if ($startDate->diff($endDate)->days == 0) {
   $groupBy = "hour";
} elseif ($startDate->diff($endDate)->days <= 30) {
   $groupBy = "day";
} else {
   $groupBy = 'month';
}

// Efetua a consulta dos pedidos e monta os totalizadores
$con = Propel::getConnection();

$startDate->setTime(0, 0, 0, 0);
$endDate->setTime(23, 59, 59, 999999);

$sql = "
   SELECT
       HORA,
       MES,
       ANO,
       DIA,
       SUM(QUANTIDADE_ITENS) as ITENS,
       SUM(QUANTIDADE_KITS) as KITS,
       SUM(VALOR_TOTAL) as TOTAL,
       SUM(VALOR_ENTREGA) as FRETE,
       COUNT(PEDIDO) as PEDIDOS
   FROM (
       SELECT
          YEAR(psh.UPDATED_AT) as ANO
           , MONTH(psh.UPDATED_AT) as MES
           , DAY(psh.UPDATED_AT) as DIA
           , HOUR(psh.UPDATED_AT) as HORA
           , COALESCE(p.VALOR_ITENS, 0) + COALESCE(p.VALOR_ENTREGA, 0) as VALOR_TOTAL
           , COALESCE(p.VALOR_ENTREGA, 0) as VALOR_ENTREGA
           , SUM(ep.QUANTIDADE) as QUANTIDADE_ITENS
           , (
               SELECT count(pi.QUANTIDADE)
               FROM
                  qp1_pedido_item pi,
                  qp1_produto_variacao pv,
                  qp1_produto pr
               WHERE pi.PEDIDO_ID = p.ID
                 AND pi.PRODUTO_VARIACAO_ID = pv.ID
                 AND pv.PRODUTO_ID = pr.ID
                 AND pr.PLANO_ID IS NOT NULL
           ) as QUANTIDADE_KITS
           , p.ID as PEDIDO

       FROM qp1_pedido p
       JOIN qp1_pedido_status_historico psh ON psh.PEDIDO_ID = p.ID AND psh.PEDIDO_STATUS_ID = 1 AND psh.IS_CONCLUIDO = 1
       LEFT JOIN qp1_estoque_produto ep ON ep.PEDIDO_ID = p.ID
       
       WHERE p.CLASS_KEY = 1
         AND p.STATUS <> 'CANCELADO'
         AND psh.UPDATED_AT BETWEEN '{$startDate->format('Y-m-d H:i:s:u')}' AND '{$endDate->format('Y-m-d H:i:s:u')}'
         AND ep.PEDIDO_ID = p.ID

       GROUP BY p.ID

       ORDER BY psh.UPDATED_AT DESC
           , p.ID DESC
           , psh.PEDIDO_STATUS_ID DESC
   ) as relatorio

   GROUP BY $groupByOptions[$groupBy]

   ORDER BY ANO, MES, DIA, HORA
";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

$totalizadores = array(
   'valor_total_venda' => 0,
   'valor_total_frete' => 0,
   'numero_total_pedidos' => 0,
   'numero_total_itens' => 0
);

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
   if ($groupBy == 'month') {
       $date = date("m/Y", mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
   } elseif ($groupBy == 'day') {
       $date = date("d/m", mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
   } elseif ($groupBy == 'hour') {
       $date = date("H\h", mktime($rs->HORA, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
   }

   $key = $map[$date];
   $dataValorVenda[$key][1] = $rs->TOTAL;
   
   $dataValorVendaExport[$key][1] = $rs->TOTAL;
   $dataValorVendaExport[$key][2] = $rs->PEDIDOS;
   $dataValorVendaExport[$key][3] = $rs->ITENS;
   $dataValorVendaExport[$key][4] = $rs->FRETE;

   $totalizadores['valor_total_venda'] += $rs->TOTAL;
   $totalizadores['valor_total_frete'] += $rs->FRETE;
   $totalizadores['numero_total_pedidos'] += $rs->PEDIDOS;
   $totalizadores['numero_total_itens'] += $rs->ITENS;
}

$sqlKits = "
  SELECT
    pr.NOME,
    COUNT(pi.QUANTIDADE) QUANTIDADE
  FROM
    qp1_pedido p,
    qp1_pedido_status_historico psh,
    qp1_pedido_item pi,
    qp1_produto_variacao pv,
    qp1_produto pr,
    qp1_plano pl
  WHERE p.ID = psh.PEDIDO_ID
    AND psh.PEDIDO_STATUS_ID = 1
    AND psh.IS_CONCLUIDO = 1
    AND p.ID = pi.PEDIDO_ID
    AND pi.PRODUTO_VARIACAO_ID = pv.ID
    AND pv.PRODUTO_ID = pr.ID
    AND psh.UPDATED_AT BETWEEN '{$startDate->format('Y-m-d H:i:s:u')}' AND '{$endDate->format('Y-m-d H:i:s:u')}'
    AND pr.PLANO_ID = pl.ID
    AND p.STATUS <> 'CANCELADO'
  GROUP BY pl.ID, pl.DESCRICAO, pr.NOME
  ORDER BY pr.PESO DESC";

$stmtKits = $con->prepare($sqlKits);
$rsKits = $stmtKits->execute();

if ($container->getRequest()->query->has('exportar')) {
   if (count($dataValorVendaExport) == 0) :
      $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
      redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
      exit;
   endif;

   $content = 'periodo;valor;numero_total_pedidos;numero_total_itens;valor_entrega' . PHP_EOL;

   foreach ($dataValorVendaExport as $i => $data) :

      $periodo = str_replace('<br>', '', $ticks[$i][1]);
      $valorTotal = format_money($data[1]);
      $totalPedidos = $data[1] !== 0 ? $data[2] : 0;
      $totalItens = $data[1] !== 0 ? $data[3] : 0;
      $valorEntrega = format_money($data[1] !== 0 ? $data[4] : 0);

      $row = [
         '"' . $periodo  . '"',
         '"' . $valorTotal . '"',
         '"' . $totalPedidos . '"',
         '"' . $totalItens. '"',
         '"' . $valorEntrega . '"',
      ];

      $content .= implode(';', $row) . PHP_EOL;
   endforeach; 

   // Pegando a codificação atual de $content (provavelmente UTF-8)
   $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

   // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
   $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

   // Criando nome para o arquivo
   $filename = sprintf('relatorio_volume_venda_%s.csv', date('Y-m-d H-i-s'));

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
   // instantiate and use the dompdf class
   $dompdf = new Dompdf();

   if (count($dataValorVendaExport) == 0) :
      $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
      redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
      exit;
   endif;

   $dados = '';
   $data = '';

   foreach ($dataValorVendaExport as $i => $object) : /* @var $object Extrato */
      $periodo = str_replace('<br>', '', $ticks[$i][1]);
      $valorTotal = format_money($object[1]);
      $totalPedidos = $object[1] !== 0 ? $object[2] : 0;
      $totalItens = $object[1] !== 0 ? $object[3] : 0;
      $valorEntrega = format_money($object[1] !== 0 ? $object[4] : 0);
         
      $data = date('d/m/Y');
      $dados .=
         "<tr>
            <td data-title='data'> {$periodo} </td>
            <td data-title='valorTotal'> {$valorTotal} </td>
            <td data-title='totalPedidos'> {$totalPedidos} </td>
            <td data-title='totalItens'> {$totalItens} </td>
            <td data-title='valorEntrega'> {$valorEntrega} </td>
         </tr>";
   endforeach;
      
   $html = "
       <span style='text-align: right;'>Data geração: {$data}</span>
       <span  style='font-family:arial; text-align:center'> 
           <h2>Relatório de volume de faturamento</h2><br>
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
                  <th>Data</th>
                  <th>Valor Total</th>
                  <th>Total pedidos</th>
                  <th>Total itens</th>
                  <th>Valor entrega</th>
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
   $dompdf->stream('relatorio_volume_venda.pdf');
   exit();
endif;