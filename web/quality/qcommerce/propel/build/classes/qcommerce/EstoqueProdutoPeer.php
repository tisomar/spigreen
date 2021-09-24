<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_estoque_produto' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class EstoqueProdutoPeer extends BaseEstoqueProdutoPeer
{

     public static function getQuantidadeEstoqueDisponivel(ProdutoVariacao $objProdutoVariacao, $centroDistribuicaoId = null, $withReserved = false){

        $produto = $objProdutoVariacao->getProduto();

        if (!$withReserved && Config::get('estoque.contar_reservado'))
            $withReserved = true;

        if($produto->isProdutoSimples()) {

            return self::getQuantidadeEstoqueDisponivelByVariacao($objProdutoVariacao, $centroDistribuicaoId, $withReserved);
        } else {
            $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

            $menorQtdEstoqueComposto = null;

            foreach ($arrProdutoCompostos as $objProdutoComposto) {
                /** @var $objProdutoComposto ProdutoComposto */


                $qtdEstoque = self::getQuantidadeEstoqueDisponivelByVariacao($objProdutoComposto->getProdutoVariacao(), $centroDistribuicaoId, $withReserved);
                $qtdEstoqueCompoe = $objProdutoComposto->getEstoqueQuantidade();

                if ($qtdEstoque > 0 && $qtdEstoque >= $qtdEstoqueCompoe && (is_null($menorQtdEstoqueComposto) || $qtdEstoque < $menorQtdEstoqueComposto)) {
                    $menorQtdEstoqueComposto = $qtdEstoque;
                }
            }

            if(is_null($menorQtdEstoqueComposto)){
                return 0;

            } else {
                return $menorQtdEstoqueComposto;
            }
        }

    }

    /**
     * @param ProdutoVariacao $objProdutoVariacao
     * @param bool $withReserved
     * @return int|null
     * @throws PropelException
     */
    public static function getQuantidadeEstoqueDisponivelByVariacao(ProdutoVariacao $objProdutoVariacao, $centroDistribuicaoId = null, $withReserved = false){

        $entradas = $saidasConfirmadas = $saidasReservadas = 0;
        $entradas = self::getQuantidadeEntradaEstoque($objProdutoVariacao, $centroDistribuicaoId);
        $saidasConfirmadas = self::getQuantidadeSaidaConfirmadaEstoque($objProdutoVariacao, $centroDistribuicaoId);
        if ($withReserved) :
            $saidasReservadas = self::getQuantidadeSaidaReservadaEstoque($objProdutoVariacao, $centroDistribuicaoId);
        endif;

        return $entradas - $saidasConfirmadas - $saidasReservadas;

    }

    /**
     * @param ProdutoVariacao $objProdutoVariacao
     * @return int|null
     * @throws PropelException
     */

    public static function getQuantidadeEntradaEstoque(ProdutoVariacao $objProdutoVariacao, $centroDistribuicaoId = null){

        if(!$objProdutoVariacao instanceof ProdutoVariacao)
            return null;

        $query = EstoqueProdutoQuery::create()
            ->addAsColumn('total', 'SUM(QUANTIDADE)')
            ->filterByProdutoVariacao($objProdutoVariacao)
            ->filterByOperacao('ENTRADA')
            ->groupByProdutoId();

        if($centroDistribuicaoId != null) :
            $query->filterByCentrodistribuicaoId($centroDistribuicaoId);
        endif;

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (int)$row['total'];
        }

        return 0;
    }

    /**
     * @param ProdutoVariacao $objProdutoVariacao
     * @return int|null
     * @throws PropelException
     */

    public static function getQuantidadeSaidaConfirmadaEstoque(ProdutoVariacao $objProdutoVariacao, $centroDistribuicaoId = null){
       
        if(!$objProdutoVariacao instanceof ProdutoVariacao)
            return null;

        $query = EstoqueProdutoQuery::create()
            ->addAsColumn('total', 'SUM(QUANTIDADE)')
            ->filterByProdutoVariacao($objProdutoVariacao)
            ->filterByOperacao('SAIDA');
            //->filterByConfirmado(true);

        if($centroDistribuicaoId != null) :
            $query->filterByCentrodistribuicaoId($centroDistribuicaoId);
        endif;

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (int)$row['total'];
        }

        return 0;
    }

    /**
     * @param ProdutoVariacao $objProdutoVariacao
     * @return int|null
     * @throws PropelException
     */

    public static function getQuantidadeSaidaReservadaEstoque(ProdutoVariacao $objProdutoVariacao, $centroDistribuicaoId = null){

        if(!$objProdutoVariacao instanceof ProdutoVariacao)
            return null;

            $query = PedidoItemQuery::create()
                ->addAsColumn('total', 'SUM(QUANTIDADE)')
                ->filterByProdutoVariacao($objProdutoVariacao)
                ->where('0 = (
                    SELECT COUNT(1)
                        FROM qp1_pedido_status_historico
                    WHERE 
                        qp1_pedido_status_historico.PEDIDO_STATUS_ID = 2
                        AND qp1_pedido_status_historico.IS_CONCLUIDO = 1
                        AND qp1_pedido_status_historico.PEDIDO_ID = qp1_pedido_item.PEDIDO_ID
                    )  
                    AND qp1_pedido_item.PEDIDO_ID in (
                    SELECT 
                        ID
                    FROM qp1_pedido
                    WHERE qp1_pedido.CLASS_KEY = 1
            )');

            if($centroDistribuicaoId != null) :
                $query->usePedidoQuery()
                    ->filterByCentroDistribuicaoId($centroDistribuicaoId)
                ->enduse();
            endif;

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (int)$row['total'];
        }

        return 0;
    }
}
