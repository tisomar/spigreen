<?php

use Doctrine\Common\Collections\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_produto_venda_estatistica' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoVendaEstatisticaPeer extends BaseProdutoVendaEstatisticaPeer
{
    static function getEstatisticaProduto($start, $end, $produtoId = null)
    {

        $estatistica = ProdutoQuery::create('p')
            ->select(['TOTAL_VENDA', 'ProdutoVariacao.ID', 'p.ID', 'p.NOME', 'ProdutoVariacao.SKU', 'VARIACAO'])
            ->withColumn('IFNULL(SUM(qp1_pedido_item.quantidade), 0)', 'TOTAL_VENDA')
            ->withColumn(
                "(SELECT group_concat(' ', DESCRICAO, '')
                    FROM qp1_produto_variacao_atributo pva 
                   WHERE pva.PRODUTO_VARIACAO_ID = qp1_produto_variacao.ID)",
                'VARIACAO'
            )
            ->join('p.ProdutoVariacao')
            ->join('ProdutoVariacao.PedidoItem')
            ->join('PedidoItem.Pedido')
            ->join('Pedido.PedidoStatusHistorico')
            ->where("status <> 'CANCELADO'")
            ->where("qp1_pedido.created_at >= '{$start->format('Y-m-d H:i:s')}'")
            ->where("qp1_pedido.created_at <= '{$end->format('Y-m-d H:i:s')}'")
            ->condition('cond1', 'pedido_status_id between 2 and 5')
            ->condition('cond2', 'is_concluido = 0')
            ->condition('cond3', 'pedido_status_id = 5')
            ->condition('cond4', 'is_concluido = 1')
            ->combine(['cond1', 'cond2'], 'and', 'cond12')
            ->combine(['cond3', 'cond4'], 'and', 'cond34')
            ->where(['cond12', 'cond34'], 'or');

        if (!empty($produtoId)):
            $estatistica = $estatistica->where('p.id = ?', $produtoId);
        else:
            $estatistica = $estatistica->groupBy('ProdutoVariacao.id');
        endif;

        $estatistica = $estatistica
            ->orderBy('TOTAL_VENDA', 'DESC');

        return $produtoId ? $estatistica->findOne() : $estatistica->findAll();
    }
}
