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

$startDateBonus = clone $startDate;  
$startDateBonus = $startDateBonus->modify('+1 month');

$endDateBonus = clone $endDate;
$endDateBonus = $endDateBonus->modify('+1 month');

[$bonusRecompra, $bonusLiderenca, $bonusIndireto] = getBonusLiderancaRecompraIndireto($startDateBonus->format('Y-m-d H:i:s'), $endDateBonus->format('Y-m-d H:i:s'));

// Valores de venda e faturamento
[$dataValorVenda, $totalizadoresVenda] = getValorVenda($startDate->format('Y-m-d H:i:s:u'), $endDate->format('Y-m-d H:i:s:u'), $groupByOptions[$groupBy], $groupBy, $con, $map);
[$dataValorFaturamento, $totalizadoresFaturamento] = getValorFaturamento($startDate->format('Y-m-d H:i:s:u'), $endDate->format('Y-m-d H:i:s:u'), $groupByOptions[$groupBy], $groupBy, $con, $map);

// Valores de bônus expansão
$bonusDireto = getBonusDireto($startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'));
$totalBonusExpransao = $bonusDireto + $bonusIndireto;

// Valores de outros bônus
$bonusHotsite = getBonusHotsite($startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'));
$bonusPreferencial = getBonusPreferencial($startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'));

$bonusAceleracao = getBonusAceleracao($startDateBonus->format('Y-m-d H:i:s'), $endDateBonus->format('Y-m-d H:i:s'));
$bonusDesempenho = getBonusDesempenho($startDateBonus->format('Y-m-d H:i:s'), $endDateBonus->format('Y-m-d H:i:s'));
$resgatePremiosEmDinheiro = getTotalPremiosAcumuladosEmDinheiro($startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'));
$fundoReservaPremiacao = ($totalizadoresVenda['valor_total_venda'] * Config::get('percet_fundo_reserva_premios')) / 100 ;
$totalBonus = $totalBonusExpransao + $bonusHotsite + $bonusPreferencial + $bonusRecompra + $bonusLiderenca + $bonusAceleracao + $bonusDesempenho + $fundoReservaPremiacao;
$percentSobreFaturamento = $totalBonus / $totalizadoresVenda['valor_total_venda'] * 100;

// Custo logistica
$custoProdutosLogistica = $totalizadoresVenda['valor_entrega'] * Config::get('percet_rentabilidade_produtoss_logistica') / 100;

// bonus produtos
[$master, $supervisor, $gerente, $executivo] = getDistribuicaoBonusProdutos($startDateBonus->format('Y-m-d H:i:s'), $endDateBonus->format('Y-m-d H:i:s'));
$impostoSobreBonusProdutosMaster = Config::get('imposto_bonus_produtos_master');
$impostoSobreBonusProdutosSupervisor = Config::get('imposto_bonus_produtos_supervisor');
$impostoSobreBonusProdutosGerente = Config::get('imposto_bonus_produtos_gerente');
$impostoSobreBonusProdutosExecutivo = Config::get('imposto_bonus_produtos_executivo');
$total_bonus_produtos = ($impostoSobreBonusProdutosMaster * $master) + ($impostoSobreBonusProdutosSupervisor * $supervisor) + ($impostoSobreBonusProdutosGerente * $gerente) + ($impostoSobreBonusProdutosExecutivo * $executivo);

// Total bonus distribuidos 
$total_bonus_distribuidos = $total_bonus_produtos + $custoProdutosLogistica + $totalBonus;
$perc_bonus_distribuidos = $total_bonus_distribuidos / $totalizadoresVenda['valor_total_venda'] * 100;

if ($container->getRequest()->query->has('exportar')) {
    switch ($container->getRequest()->query->get('range')) {
        case 'custom':
            $data = $container->getRequest()->query->get('startDate') . ' - ' . $container->getRequest()->query->get('startDate');
            break;
        case 'last-week':
            $data = 'Ultima semana';
            break;
        case 'last-month':
            $data = 'Ultimo mẽs';
            break;
        case 'last-year':
            $data = 'Ultimo ano';
            break;
    }

    if (count($dataValorVendaExport) == 0) :    
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
     endif;

    $rowVenda = [
        '"' . 'Total de vendas (faturamento total)' . '"',
        '"' . $data . '"',
        '"' . 'R$ ' . format_money($totalizadoresVenda['valor_total_venda'])  . '"',
    ];
    $content = implode(';', $rowVenda) . PHP_EOL;

    $rowFaturamento = [
        '"' . 'total de faturamento (vendas s/ uso de bônus)' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($totalizadoresFaturamento['valor_total_faturamento'])  . '"',
    ];

    $content .= implode(';', $rowFaturamento) . PHP_EOL;
    $content .= '' . PHP_EOL;

    $rowBonusDireto = [
        '"' . 'Bônus de expansão direto' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusDireto)  . '"',
    ];

    $content .= implode(';', $rowBonusDireto) . PHP_EOL;

    $rowBonusIndireto = [
        '"' . 'Bônus de expansão indireto' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusIndireto)  . '"',
    ];

    $content .= implode(';', $rowBonusIndireto) . PHP_EOL;

    $rowTotalBonusExpansao = [
        '"' . 'TOTAL DE BONUS EXPANSÃO' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($totalBonusExpransao)  . '"',
    ];

    $content .= implode(';', $rowTotalBonusExpansao) . PHP_EOL;

    $rowBonusHotSite = [
        '"' . 'Bônus de vendas hot site' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusHotsite)  . '"',
    ];
    $content .= implode(';', $rowBonusHotSite) . PHP_EOL;

    $rowBonusPreferencial = [
        '"' . 'Bônus de cliente preferencial' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusPreferencial)  . '"',
    ];
    $content .= implode(';', $rowBonusPreferencial) . PHP_EOL;

    $rowBonusRecompra = [
        '"' . 'Bônus produtividade (recompras)' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusRecompra)  . '"',
    ];
    $content .= implode(';', $rowBonusRecompra) . PHP_EOL;
   
    $rowBonusLideranca = [
        '"' . 'Bônus liderança' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusLiderenca)  . '"',
    ];
    $content .= implode(';', $rowBonusLideranca) . PHP_EOL;
    
    $rowBonusAceleracao = [
        '"' . 'Bônus aceleração' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusAceleracao)  . '"',
    ];
    $content .= implode(';', $rowBonusAceleracao) . PHP_EOL;
    
    $rowBonusDesempenho = [
        '"' . 'Bônus desempenho' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($bonusDesempenho)  . '"',
    ];
    $content .= implode(';', $rowBonusDesempenho) . PHP_EOL;

    $rowBonusPremiosAcumulados = [
        '"' . 'Entrega de "Prêmios pontos acumulados' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($resgatePremiosEmDinheiro)  . '"',
    ];
    $content .= implode(';', $rowBonusPremiosAcumulados) . PHP_EOL;
    
    $rowFundoReserva = [
        '"' . 'Fundo de reserva para premiações' . '"',
        '"' . Config::get('percet_fundo_reserva_premios') . ' %' . '"',
        '"' . 'R$ ' . format_money($fundoReservaPremiacao)  . '"',
    ];
    $content .= implode(';', $rowFundoReserva) . PHP_EOL;

    $rowTotalBonus = [
        '"' . 'sub-Total de bônus' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($totalBonus)  . '"',
    ];
    $content .= implode(';', $rowTotalBonus) . PHP_EOL;
   
    $rowTotalBonus = [
        '"' . '% sobre o faturamento' . '"',
        '"' . '' . '"',
        '"' . format_money($percentSobreFaturamento) . ' % ' . '"',
    ];
    $content .= implode(';', $rowTotalBonus) . PHP_EOL;
    $content .= '' . PHP_EOL;

    $rowTotalValorEntrega = [
        '"' . 'total pagamento frete' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($totalizadoresVenda['valor_entrega'])  . '"',
    ];
    $content .= implode(';', $rowTotalValorEntrega) . PHP_EOL;
   
    $rowCustoProdutoLogistica = [
        '"' . 'Custo produtos/logística  + rentabilidade ' . '"',
        '"' . Config::get('percet_rentabilidade_produtoss_logistica') . ' %' . '"',
        '"' . 'R$ ' . format_money($custoProdutosLogistica)  . '"',
    ];
    $content .= implode(';', $rowCustoProdutoLogistica) . PHP_EOL;
    $content .= '' . PHP_EOL;

    $rowBonusProdutosMaster = [
        '"' . 'MASTER' . '"',
        '"' . $master . '"',
        '"' . format_money($impostoSobreBonusProdutosMaster * $master) . '"' 
    ];
    $content .= implode(';', $rowBonusProdutosMaster) . PHP_EOL;
   
    $rowBonusProdutosSupervisor = [
        '"' . 'SUPERVISOR' . '"',
        '"' . $supervisor . '"',
        '"' . format_money($impostoSobreBonusProdutosSupervisor * $supervisor) . '"' 
    ];
    $content .= implode(';', $rowBonusProdutosSupervisor) . PHP_EOL;
   
    $rowBonusProdutosGerente = [
        '"' . 'GERENTE' . '"',
        '"' . $gerente . '"',
        '"' . format_money($impostoSobreBonusProdutosGerente * $gerente) . '"' 
    ];
    $content .= implode(';', $rowBonusProdutosGerente) . PHP_EOL;
   
    $rowBonusProdutosExecutivo = [
        '"' . 'EXECUTIVO' . '"',
        '"' . $executivo . '"',
        '"' . format_money($impostoSobreBonusProdutosExecutivo * $executivo) . '"' 
    ];
    $content .= implode(';', $rowBonusProdutosExecutivo) . PHP_EOL;

    $rowBonusProtudoTotal = [
        '"' . 'Bônus Produtos Total' . '"',
        '"' . '' . '"',
        '"' . format_money($total_bonus_produtos) . '"' 
    ];
    $content .= implode(';', $rowBonusProtudoTotal) . PHP_EOL;
    $content .= '' . PHP_EOL;

    $rowTotalBonusDistribuido = [
        '"' . 'TOTAL DE BÔNUS DISTRIBUIDOS' . '"',
        '"' . '' . '"',
        '"' . 'R$ ' . format_money($total_bonus_distribuidos)  . '"',
    ];
    $content .= implode(';', $rowTotalBonusDistribuido) . PHP_EOL;

    $rowTotalBonusDistribuido = [
        '"' . 'PERCENTUAL DE BÔNUS DISTRIBUIDOS' . '"',
        '"' . '' . '"',
        '"' . $perc_bonus_distribuidos . ' % '. '"',
    ];
    $content .= implode(';', $rowTotalBonusDistribuido) . PHP_EOL;
     
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('relatorio_faturamento_vendas_%s.csv', date('Y-m-d H-i-s'));

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

    $valor_total_venda = format_money($totalizadoresVenda['valor_total_venda']);
    $totalizadoresFaturamento = format_money($totalizadoresFaturamento['valor_total_faturamento']);
    $total_bonus_distribuidos = format_number($total_bonus_distribuidos);
    $impostoSobreBonusProdutosMaster = format_money($impostoSobreBonusProdutosMaster * $master);
    $impostoSobreBonusProdutosSupervisor = format_money($impostoSobreBonusProdutosSupervisor * $supervisor);
    $impostoSobreBonusProdutosGerente = format_money($impostoSobreBonusProdutosGerente * $gerente);
    $impostoSobreBonusProdutosExecutivo = format_money($impostoSobreBonusProdutosExecutivo * $executivo);
    $total_bonus_produtos = format_money($total_bonus_produtos);

    $totalBonus = format_number($totalBonus);
    $fundoReservaPremiacao = format_number($fundoReservaPremiacao);
    $resgatePremiosEmDinheiro = format_number($resgatePremiosEmDinheiro);
    $bonusDesempenho = format_number($bonusDesempenho);
    $bonusAceleracao = format_number($bonusAceleracao);
    $bonusLiderenca = format_number($bonusLiderenca);
    $bonusRecompra = format_number($bonusRecompra);
    $bonusPreferencial = format_number($bonusPreferencial);
    $bonusDireto = format_number($bonusDireto);
    $bonusHotsite = format_number($bonusHotsite);
    $bonusIndireto = format_number($bonusIndireto);
    $totalBonusExpransao = format_number($totalBonusExpransao);
    $valorEntrega = format_number($totalizadoresVenda['valor_entrega']);

    $custoProdutosLogistica = format_number($custoProdutosLogistica);
    $percentLogistica = Config::get('percet_rentabilidade_produtoss_logistica') . ' %';
    $percent_premio_acumulado = Config::get('percet_fundo_reserva_premios') . ' %';

    $dados = '';
    $data = date('d/m/Y');
        
    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório de venda e faturamento</h2><br>
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
            <tbody>
                <tr>
                    <td data-title='Data'> 
                        Total de vendas (faturamento total) 
                    </td>
                    <td data-title='Data'> 
                        $valor_total_venda
                    </td>
                </tr>
                <tr>
                    <td data-title='Data'> 
                        total de faturamento (vendas s/ uso de bônus)
                    </td>
                    <td data-title='Data'> 
                        $totalizadoresFaturamento
                    </td>
                </tr>
            <tbody>
        </table>

        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='table table-hover table-striped'>
            <tbody>
                <tr>
                    <td colspan='3'><h3>Valores de bônus expansão</h3></td>
                </tr>
                <tr>
                    <td> bônus diretos </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusDireto 
                    </td>
                </tr>
                <tr>
                    <td> bônus indiretos </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusIndireto 
                    </td>
                </tr>
                <tr>
                    <td>  total bônus expansão ( bônus direto + bônus indireto) </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $totalBonusExpransao 
                    </td>
                </tr>
                <tr>
                    <td colspan='3'><h3>Valores de outros bônus</h3></td>
                </tr>
                <tr>
                    <td> bônus hot site </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusHotsite 
                    </td>
                </tr>
                <tr>
                    <td> bônus cliente preferêncial </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusPreferencial 
                    </td>
                </tr>
                <tr>
                    <td> bônus recompra </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusRecompra 
                    </td>
                </tr>
                <tr>
                    <td> bônus liderança </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusLiderenca 
                    </td>
                </tr>
                <tr>
                    <td> bônus aceleração </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusAceleracao 
                    </td>
                </tr>
                <tr>
                    <td> bônus desempenho </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $bonusDesempenho 
                    </td>
                </tr>
                <tr>
                    <td> bônus pontos acumulados </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $resgatePremiosEmDinheiro 
                    </td>
                </tr>
                <tr>
                    <td> fundo de reserva para premiação </td>
                    <td> $percent_premio_acumulado </td>
                    <td data-title='Data'> 
                        R$ $fundoReservaPremiacao 
                    </td>
                </tr>
                <tr>
                    <td> total Bônus </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $totalBonus 
                    </td>
                </tr>
                <tr>
                    <td> % sobre o faturamento </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        $percentSobreFaturamento %
                    </td>
                </tr>

                <tr>
                    <td colspan='3'><h3>BÕnus frete</h3></td>
                </tr>
                <tr>
                    <td> total pagamento frete </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $valorEntrega 
                    </td>
                </tr>
                <tr>
                    <td> custo produto logistica </td>
                    <td> $percentLogistica </td>
                    <td data-title='Data'> 
                        R$ $custoProdutosLogistica 
                    </td>
                </tr>

                <tr>
                    <td colspan='3'><h3>Bônus produtos</h3></td>
                </tr>
                <tr>
                    <td> MASTER </td>
                    <td> $master </td>
                    <td data-title='Data'> 
                        R$ $impostoSobreBonusProdutosMaster 
                    </td>
                </tr>
                <tr>
                    <td> SUPERVISOR </td>
                    <td> $supervisor </td>
                    <td data-title='Data'> 
                        R$ $impostoSobreBonusProdutosSupervisor   
                    </td>
                </tr>
                <tr>
                    <td> GERENTE </td>
                    <td> $gerente </td>
                    <td data-title='Data'> 
                        R$ $impostoSobreBonusProdutosGerente   
                    </td>
                </tr>
                <tr>
                    <td> EXECUTIVO </td>
                    <td> $executivo </td>
                    <td data-title='Data'> 
                        R$ $impostoSobreBonusProdutosExecutivo   
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>Total bônus produtos</td>
                    <td data-title='Descricao'> 
                        R$ $total_bonus_produtos
                    </td>
                </tr>

                <tr>
                    <td colspan='3'><h3>Total bônus distribuídos</h3></td>
                </tr>
                <tr>
                    <td> total de bônus distribuídos </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        R$ $total_bonus_distribuidos 
                    </td>
                </tr>
                <tr>
                    <td> % de bônus distribuídos </td>
                    <td>  </td>
                    <td data-title='Data'> 
                        $perc_bonus_distribuidos %   
                    </td>
                </tr>
            </tbody>
        </table>
    ";

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('relatorio_volume_faturamento.pdf');
    exit();
endif;

function getBonusDireto($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByTipo(Extrato::TIPO_INDICACAO_DIRETA, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->filterByObservacao('%Bônus de Equipe Direta. Pedido%', Criteria::LIKE)
        ->filterByData(['min' => $min, 'max' => $max])
        ->find()->toArray();
        
    return $bonus[0];
}

function getBonusIndiretos($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByTipo(Extrato::TIPO_INDICACAO_INDIRETA, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->filterByObservacao('%Bônus de Equipe Indireta. Pedido%', Criteria::LIKE)
        ->filterByData(['min' => $min, 'max' => $max])
        ->find()->toArray();

    return $bonus[0];
}
    
function getBonusHotsite($min, $max) {
    $bonus = ExtratoQuery::create()
    ->select(['total_pontos'])
    ->withColumn('SUM(PONTOS)', 'total_pontos')
    ->filterByTipo(Extrato::TIPO_VENDA_HOTSITE, Criteria::EQUAL)
    ->filterByData(['min' => $min, 'max' => $max])
    ->filterByOperacao('+')
    ->find()->toArray();
    
    return $bonus[0];
}

function getBonusPreferencial($min, $max) {
    $bonus = ExtratoQuery::create()
    ->select(['total_pontos'])
    ->withColumn('SUM(PONTOS)', 'total_pontos')
    ->filterByData(['min' => $min, 'max' => $max])
    ->filterByTipo(Extrato::TIPO_CLIENTE_PREFERENCIAL, Criteria::EQUAL)
    ->filterByOperacao('+')
    ->find()->toArray();
    
    return $bonus[0];
}

function getBonusRecompra($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByData(['min' => $min, 'max' => $max])
        ->filterByTipo(Extrato::TIPO_RESIDUAL, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->find()->toArray();

    return $bonus[0];
}

function getBonusLideranca($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByData(['min' => $min, 'max' => $max])
        ->filterByTipo(Extrato::TIPO_DISTRIBUICAO_REDE, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->find()->toArray();

    return $bonus[0];
}

function getBonusLiderancaRecompraIndireto($min, $max) {
    $bonus = DistribuicaoClienteQuery::create()
        ->select(['total_recompra', 'total_lideranca', 'total_indireto'])
        ->withColumn('SUM(TOTAL_PONTOS_RECOMPRA)', 'total_recompra')
        ->withColumn('SUM(TOTAL_PONTOS_LIDERANCA)', 'total_lideranca')
        ->withColumn('SUM(TOTAL_PONTOS_ADESAO)', 'total_indireto')
        ->filterByData(['min' => $min, 'max' => $max])
        ->useClienteQuery()
        ->endUse()
        ->useDistribuicaoQuery()
            ->filterByStatus(Distribuicao::STATUS_DISTRIBUIDO, Criteria::EQUAL)
        ->endUse()
        ->find()->toArray();
        
    return [$bonus[0]['total_recompra'], $bonus[0]['total_lideranca'], $bonus[0]['total_indireto']];
}

function getBonusAceleracao($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByData(['min' => $min, 'max' => $max])
        ->filterByTipo(Extrato::TIPO_BONUS_ACELERACAO, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->find()->toArray();

    return $bonus[0];
}

function getBonusDesempenho($min, $max) {
    $bonus = ExtratoQuery::create()
        ->select(['total_pontos'])
        ->withColumn('SUM(PONTOS)', 'total_pontos')
        ->filterByData(['min' => $min, 'max' => $max])
        ->filterByTipo(Extrato::TIPO_BONUS_DESEMPENHO, Criteria::EQUAL)
        ->filterByOperacao('+')
        ->find()->toArray();

    return $bonus[0];
}

function getTotalPremiosAcumuladosEmDinheiro($min, $max) {
    $total = ResgatePremiosAcumuladosQuery::create()
        ->select(['premio'])
        ->filterBySelecionado('DINHEIRO', Criteria::EQUAL)
        ->filterBySituacao('EFETUADO', Criteria::EQUAL)
        ->filterByData(['min' => $min, 'max' => $max])
        ->find()->toArray();

    $valor_total = 0;
    foreach($total as $tot) :
        $valor = str_replace('R$ ', '', $tot);
        $valor = str_replace(['.', ',00'], '', $valor);
        $valor_total += $valor;
    endforeach; 

    return $valor_total;
}

function getDistribuicaoBonusProdutos($min, $max) {
    $bonus = ExtratoBonusProdutosQuery::create()
        ->filterByOperacao('-', Criteria::EQUAL)
        ->filterByData(['min' => $min, 'max' => $max])
        ->find();

    $master = 0;
    $supervisor = 0;
    $gerente = 0;
    $executivo = 0;
    foreach($bonus as $distribuicao) :

        switch ($distribuicao->getPlanoCarreiraId()) {
            case 1:
                $master ++;
                break;
            case 2:
                $supervisor ++;
                break;
            case 3:
                $gerente ++;
                break;
            case 4:
                $executivo ++;
                break;
        }
    endforeach;

    return [$master, $supervisor, $gerente, $executivo];
}

function getValorVenda($min, $max, $grupOptions, $groupBy, $con, $map) {
    
    $sqlVenda = "   
    SELECT
        HORA,
        MES,
        ANO,
        DIA,
        SUM(QUANTIDADE_ITENS) as ITENS,
        SUM(VALOR_TOTAL) as TOTAL_VENDA,
        SUM(VALOR_ENTREGA) as VALOR_ENTREGA,
        COUNT(PEDIDO) as PEDIDOS
        FROM (
            SELECT
            YEAR(psh.UPDATED_AT) as ANO
                , MONTH(psh.UPDATED_AT) as MES
                , DAY(psh.UPDATED_AT) as DIA
                , HOUR(psh.UPDATED_AT) as HORA
                , COALESCE(p.VALOR_ITENS, 0) + COALESCE(p.VALOR_ENTREGA, 0) as VALOR_TOTAL
                , COALESCE(p.VALOR_ENTREGA, 0) as VALOR_ENTREGA
                , SUM(ip.QUANTIDADE) as QUANTIDADE_ITENS
                , p.ID as PEDIDO

            FROM qp1_pedido p
            JOIN qp1_pedido_item ip ON ip.PEDIDO_ID = p.ID
            JOIN qp1_pedido_status_historico psh ON psh.PEDIDO_ID = p.ID AND psh.PEDIDO_STATUS_ID = 1 AND psh.IS_CONCLUIDO = 1

            WHERE p.CLASS_KEY = 1
                AND p.STATUS <> 'CANCELADO'
                AND ip.PLANO_ID IS NULL
                AND psh.UPDATED_AT
                BETWEEN '{$min}'
                AND '{$max}'

        GROUP BY p.ID

        ORDER BY psh.UPDATED_AT DESC
            , p.ID DESC
            , psh.PEDIDO_STATUS_ID DESC
    ) as relatorio

    GROUP BY $grupOptions

    ORDER BY ANO, MES, DIA, HORA
    ";

    $stmtVenda = $con->prepare($sqlVenda);
    $rsVenda = $stmtVenda->execute();

    $totalizadoresVenda = array(
        'valor_total_venda' => 0,
        'valor_entrega' => 0
    );

    while ($rsVenda = $stmtVenda->fetch(PDO::FETCH_OBJ)) {
        if ($groupBy == 'month') {
            $date = date('m/Y', mktime(0, 0, 0, $rsVenda->MES, $rsVenda->DIA, $rsVenda->ANO));
        } elseif ($groupBy == 'day') {
            $date = date('d/m', mktime(0, 0, 0, $rsVenda->MES, $rsVenda->DIA, $rsVenda->ANO));
        } elseif ($groupBy == 'hour') {
            $date = date('H\h', mktime($rsVenda->HORA, 0, 0, $rsVenda->MES, $rsVenda->DIA, $rsVenda->ANO));
        }

        $key = $map[$date];
        $dataValorVenda[$key][1] = $rsVenda->TOTAL_VENDA;

        $dataValorVendaExport[$key][1] = $rsVenda->TOTAL_VENDA;
        $dataValorVendaExport[$key][5] = $rsVenda->VALOR_ENTREGA;

        $totalizadoresVenda['valor_total_venda'] += $rsVenda->TOTAL_VENDA;
        $totalizadoresVenda['valor_entrega'] += $rsVenda->VALOR_ENTREGA;
    }

    return [$dataValorVenda, $totalizadoresVenda];
}

function getValorFaturamento($min, $max, $grupOptions, $groupBy, $con, $map) {
    
    $formasPagamentoBonus = implode(', ', array_map(function($x) {
        return "'$x'";
    }, [
        PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
        PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
        PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE
    ]));

    $sqlFaturamento = "
        SELECT
            HORA,
            MES,
            ANO,
            DIA,
            SUM(VALOR_TOTAL) as TOTAL_FATURAMENTO,
            SUM(ENTREGA) AS VALOR_ENTREGA
        FROM (
            SELECT 
                YEAR(psh.UPDATED_AT) as ANO,
                MONTH(psh.UPDATED_AT) as MES,
                DAY(psh.UPDATED_AT) as DIA,
                HOUR(psh.UPDATED_AT) as HORA,
                COALESCE(pfp.VALOR_PAGAMENTO, p.VALOR_ITENS + p.VALOR_ENTREGA - p.VALOR_CUPOM_DESCONTO) as VALOR_TOTAL,
                p.VALOR_ENTREGA as ENTREGA
            FROM qp1_pedido p
            JOIN qp1_pedido_item ip ON ip.PEDIDO_ID = p.ID
            JOIN qp1_pedido_status_historico psh ON psh.PEDIDO_ID = p.ID AND psh.PEDIDO_STATUS_ID = 1 AND psh.IS_CONCLUIDO = 1
            JOIN qp1_pedido_forma_pagamento pfp ON pfp.PEDIDO_ID = p.ID
            
            WHERE p.CLASS_KEY = 1
                AND p.STATUS <> 'CANCELADO'
                AND ip.PLANO_ID IS NULL
                AND psh.UPDATED_AT
                BETWEEN '{$min}'
                AND '{$max}'
                AND pfp.FORMA_PAGAMENTO NOT IN ($formasPagamentoBonus)
                AND pfp.STATUS = 'APROVADO'

            GROUP BY p.ID

            ORDER BY psh.UPDATED_AT DESC,
                p.ID DESC,
                psh.PEDIDO_STATUS_ID DESC
        ) as relatorio

        GROUP BY $grupOptions

        ORDER BY ANO, MES, DIA, HORA
    ";

    $stmt = $con->prepare($sqlFaturamento);
    $rs = $stmt->execute();

    $totalizadoresFaturamento = array(
        'valor_total_faturamento' => 0,
        'valor_entrega' => 0
    );

    while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {

        if ($groupBy == 'month') {
            $date = date('m/Y', mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
        } elseif ($groupBy == 'day') {
            $date = date('d/m', mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
        } elseif ($groupBy == 'hour') {
            $date = date('H\h', mktime($rs->HORA, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
        }

        $key = $map[$date];
        $dataValorFaturamento[$key][1] = $rs->TOTAL_FATURAMENTO;

        $dataValorFaturamentoExport[$key][1] = $rs->TOTAL_FATURAMENTO;
        $dataValorFaturamentoExport[$key][5] = $rs->VALOR_ENTREGA;

        $totalizadoresFaturamento['valor_total_faturamento'] += $rs->TOTAL_FATURAMENTO;
        $totalizadoresFaturamento['valor_entrega'] += $rs->VALOR_ENTREGA;
    }

    return [$dataValorFaturamento, $totalizadoresFaturamento];
}