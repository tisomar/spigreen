<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_controle_pontuacao_cliente' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ControlePontuacaoClientePeer extends BaseControlePontuacaoClientePeer
{

    public function getPontuacaoRecompraClientes($startDate, $endDate) {

        $con = Propel::getConnection();

        $sql = "
        SELECT
            cli.ID,
            IF(cli.CNPJ IS NULL, cli.NOME, cli.RAZAO_SOCIAL) NOME,
            cli.TELEFONE,
            cli.EMAIL,
            cli.CREATED_AT,
            cli.DATA_ATIVACAO,
            -- PONTOS DE COMPRAS PESSOAIS
            (SELECT IFNULL(SUM(peditem.VALOR_PONTOS_UNITARIO * peditem.QUANTIDADE), 0)
                FROM qp1_pedido ped
                JOIN qp1_pedido_item peditem ON peditem.PEDIDO_ID = ped.ID
                JOIN qp1_produto_variacao provar ON provar.ID = peditem.PRODUTO_VARIACAO_ID
                JOIN qp1_produto pro ON pro.ID = provar.PRODUTO_ID
                JOIN qp1_pedido_status_historico stahist ON stahist.PEDIDO_ID = ped.ID
                WHERE ped.CLIENTE_ID =  cli.ID
                AND ped.STATUS <> 'CANCELADO'
                AND ped.CREATED_AT BETWEEN '{$startDate}' AND '{$endDate}'
                AND pro.PLANO_ID IS NULL
                AND stahist.PEDIDO_STATUS_ID = 1
                AND stahist.IS_CONCLUIDO = 1) 
            -- +
            -- PONTOS DE COMPRAS DE CLIENTES FINAIS
            -- (SELECT IFNULL(SUM(peditem.VALOR_PONTOS_UNITARIO * peditem.QUANTIDADE), 0)
            --  FROM qp1_pedido ped
            --  JOIN qp1_pedido_item peditem ON peditem.PEDIDO_ID = ped.ID
            --  JOIN qp1_produto_variacao provar ON provar.ID = peditem.PRODUTO_VARIACAO_ID
            --  JOIN qp1_produto pro ON pro.ID = provar.PRODUTO_ID
            --  JOIN qp1_pedido_status_historico stahist3 ON stahist3.PEDIDO_ID = ped.ID
            --  WHERE ped.HOTSITE_CLIENTE_ID = cli.ID
            --      AND ped.STATUS <> 'CANCELADO'
            --      AND ped.CREATED_AT BETWEEN '{$startDate}' AND '{$endDate}'
            --      AND pro.PLANO_ID IS NULL
            --      AND stahist3.PEDIDO_STATUS_ID = 1
            --      AND stahist3.IS_CONCLUIDO = 1) 
            TOTAL_PONTOS
            FROM qp1_cliente cli
            JOIN qp1_plano pla ON pla.ID = cli.PLANO_ID
            WHERE cli.VAGO = 0
            AND pla.PLANO_CLIENTE_PREFERENCIAL = 0
        ORDER BY 2
        ";

        $stmt = $con->prepare($sql);
        $result = $stmt->execute();

        $arResult = [];
        while ($result = $stmt->fetch(PDO::FETCH_OBJ)) :
            if($result->TOTAL_PONTOS) :
                $arResult[] = $result;
            endif;
        endwhile;

        return $arResult;
    }
}
