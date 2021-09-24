<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_tabela_preco' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TabelaPrecoPeer extends BaseTabelaPrecoPeer
{

    CONST TIPO_OPERACAO_DESCONTAR = 1;
    CONST TIPO_OPERACAO_ACRESCENTAR = 2;

    public static function getProdutoVariacaoNaoAdicionado($tabelaId, $con = null) {

        if (null == $con) {
            $con = Propel::getConnection();
        }

        $sql = "
            SELECT
                pv.ID as ID,
                pv.VALOR_BASE as VALOR_BASE,
                pv.VALOR_PROMOCIONAL as VALOR_PROMOCIONAL
            FROM qp1_produto_variacao pv
            JOIN qp1_produto p ON pv.PRODUTO_ID = p.ID
            WHERE p.DATA_EXCLUSAO IS NULL
            AND pv.DATA_EXCLUSAO IS NULL
            AND pv.ID NOT IN (
                SELECT tpv.PRODUTO_VARIACAO_ID
                FROM qp1_tabela_preco_variacao tpv
                WHERE tpv.TABELA_PRECO_ID = " . $tabelaId . "
            )
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function addProdutoVariacaoToTabelaPreco(BaseTabelaPreco $objTabelaPreco, $oProdutoVariacao) {

        $produtoVariacaoId = $oProdutoVariacao->getId();

        $oTabelaPrecoVariacao = TabelaPrecoVariacaoQuery::create()
            ->filterByProdutoVariacaoId($produtoVariacaoId)
            ->filterByTabelaPrecoId($objTabelaPreco->getId())
            ->findOne();

        if (is_null($oTabelaPrecoVariacao)) {
            $oTabelaPrecoVariacao = new TabelaPrecoVariacao();
            $oTabelaPrecoVariacao->setProdutoVariacaoId($produtoVariacaoId);
            $oTabelaPrecoVariacao->setTabelaPrecoId($objTabelaPreco->getId());
        }

        if ($objTabelaPreco->getAtualizarAutomaticamente()) {
            if ($objTabelaPreco->getTipoOperacao() == TabelaPrecoPeer::TIPO_OPERACAO_ACRESCENTAR) {
                $oTabelaPrecoVariacao->setValorBase(aplicarPercentualAcrescimo($oProdutoVariacao->getValorBase(), $objTabelaPreco->getPorcentagem()));
                $oTabelaPrecoVariacao->setValorPromocional(aplicarPercentualAcrescimo($oProdutoVariacao->getValorPromocional(), $objTabelaPreco->getPorcentagem()));
            } else {
                $oTabelaPrecoVariacao->setValorBase(aplicarPercentualDesconto($oProdutoVariacao->getValorBase(), $objTabelaPreco->getPorcentagem()));
                $oTabelaPrecoVariacao->setValorPromocional(aplicarPercentualDesconto($oProdutoVariacao->getValorPromocional(), $objTabelaPreco->getPorcentagem()));
            }
        } else {
            if ($oTabelaPrecoVariacao->isNew()) {
                $oTabelaPrecoVariacao->setValorBase($oProdutoVariacao->getValorBase());
                $oTabelaPrecoVariacao->setValorPromocional($oProdutoVariacao->getValorPromocional());
            }
        }

        $oTabelaPrecoVariacao->save();

    }

    public static function addProdutoVariacaoNaoAdicionado(BaseTabelaPreco $objTabelaPreco) {

        $listProdutoVariacaoId = TabelaPrecoPeer::getProdutoVariacaoNaoAdicionado($objTabelaPreco->getId());
        $ids = array_column($listProdutoVariacaoId, 'ID');
        $collProdutoVariacao = ProdutoVariacaoQuery::create()->filterById($ids, Criteria::IN)->find();
        foreach ($collProdutoVariacao as $objProdutoVariacao) {
            TabelaPrecoPeer::addProdutoVariacaoToTabelaPreco($objTabelaPreco, $objProdutoVariacao);
        }

    }

    /**
     * Atualiza os preços dos produtos de uma determinada tabela.
     * @param BaseTabelaPreco $objTabelaPreco
     */
    public static function updateProdutoVariacao(BaseTabelaPreco $objTabelaPreco) {

        $hayProdutoVariacao = ProdutoVariacaoQuery::create()
            ->filterByDataExclusao(null, Criteria::EQUAL)
            ->useTabelaPrecoVariacaoQuery()
            ->filterByTabelaPrecoId($objTabelaPreco->getId())
            ->endUse()
            ->find();

        foreach ($hayProdutoVariacao as $produtoVariacao) {
            TabelaPrecoPeer::addProdutoVariacaoToTabelaPreco($objTabelaPreco, $produtoVariacao);
        }



    }

}
