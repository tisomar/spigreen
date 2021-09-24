<?php
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
?>

<div class="table-responsive">
    <table class="table table-hover table-striped table-report">
        <thead>
        <tr>
            <th>Código Patrocinador</th>
            <th>Nome</th>
            <th>Total Pontos</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $stmt->fetch()) :
            ?>
            <tr>
                <td data-title="Código Patrocinador"><?= $row['CHAVE_INDICACAO'] ?></td>
                <td data-title="Nome"><?= $row['NOME'] ?></td>
                <td data-title="Total em Compra"><?= format_money($row['TOTAL_PONTOS']) ?></td>
            </tr>
            <?php
        endwhile;

        if ($stmt->rowCount() == 0) :
            ?>
            <tr>
                <td colspan="3">
                    Nenhum registro disponível
                </td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>

    </table>
</div>

<style>
    .table-report {
        width: 100%;
        border: none;
        border-collapse: collapse;
    }

    .table-report th,
    .table-report td {
        padding: 0;
    }
</style>

<?php unset($_SESSION['SQL_RELATORIO_ESTOQUE']); ?>
