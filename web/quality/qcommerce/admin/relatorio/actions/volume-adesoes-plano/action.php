<?php

if ($container->getRequest()->query->has('exportar')) :
    $con = Propel::getConnection();

    $sql = "
        SELECT 
            pla.id PLANO_ID,
            pla.NOME PLANO,
            COUNT(VALOR_TOTAL_PEDIDO) QTD_PEDIDOS,
            SUM(VALOR_TOTAL_PEDIDO) VALOR_TOTAL_PLANO
          FROM (
                SELECT (SELECT SUM(pedite.VALOR_UNITARIO * pedite.QUANTIDADE)
                          FROM qp1_pedido_item pedite
                          JOIN qp1_produto_variacao provar ON provar.ID = pedite.PRODUTO_VARIACAO_ID
                          JOIN qp1_produto pro ON pro.ID = provar.PRODUTO_ID and pro.PLANO_ID IS NOT NULL
                         WHERE pedite.PEDIDO_ID = ped.ID
                        ) + COALESCE(ped.VALOR_ENTREGA, 0) - COALESCE(pfp.VALOR_DESCONTO, 0) - COALESCE(ped.VALOR_CUPOM_DESCONTO, 0) VALOR_TOTAL_PEDIDO,
                        cli.PLANO_ID PLANO_ID
                  FROM qp1_pedido ped
                  JOIN qp1_pedido_status_historico stahist ON stahist.PEDIDO_ID = ped.ID
                  JOIN qp1_pedido_forma_pagamento pfp ON pfp.PEDIDO_ID = ped.ID
                  JOIN qp1_cliente cli ON cli.ID = ped.CLIENTE_ID
                 WHERE ped.STATUS <> 'CANCELADO'
                   AND stahist.PEDIDO_STATUS_ID = 1
                   AND stahist.IS_CONCLUIDO = 1
                   AND pfp.STATUS = 'APROVADO'
                   AND stahist.UPDATED_AT BETWEEN '{$container->getRequest()->query->get('data_inicial')}' AND '{$container->getRequest()->query->get('data_final')}'
          ) TABELA
          LEFT JOIN qp1_plano pla ON pla.ID = TABELA.PLANO_ID
         WHERE pla.ID IS NOT NULL
         GROUP BY pla.ID
    ";

    $stmt = $con->prepare($sql);
    $rs = $stmt->execute();

    $totalizadores = [
        'valor_total_plano' => 0,
        'qtd_pedidos_plano' => 0,
    ];

    $dataValorTotal = array();
    $dataQtdPedidos = array();

    while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) :
        $dataValorTotal[] = [
            'label' => $rs->PLANO,
            'data' => $rs->VALOR_TOTAL_PLANO
        ];

        $dataQtdPedidos[] = [
            'label' => $rs->PLANO,
            'data' => $rs->QTD_PEDIDOS
        ];

        $totalizadores['valor_total_plano'] += $rs->VALOR_TOTAL_PLANO;
        $totalizadores['qtd_pedidos_plano'] += $rs->QTD_PEDIDOS;
    endwhile;

    $dados = [];

    foreach ($dataValorTotal as $i => $data) :
        $data['data'] = $data['data'] ? $data['data'] : '0';

        $dados[] = [
            $data['label'],
            $dataQtdPedidos[$i]['data'] . ' (' . round((($dataQtdPedidos[$i]['data'] * 100) / $totalizadores['qtd_pedidos_plano'])) . '%)',
            'R$ ' . $data['data'] . ' (' . round((($data['data'] * 100) / $totalizadores['valor_total_plano'])) . '%)'
        ];
    endforeach;

    $dados[] = [
        'Total',
        $totalizadores['qtd_pedidos_plano'],
        'R$ '. $totalizadores['valor_total_plano']
    ];

    $content = 'Plano;NumeroDePedidos;Valor' . PHP_EOL;

    foreach ($dados as $dadosLinha) :
        $content .= implode(';', $dadosLinha) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('volume_adesoes_plano__%s.csv', date('Y-m-d H-i-s'));

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