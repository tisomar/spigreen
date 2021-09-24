<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_PRODUTO_FAIXA_DESCONTO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoFaixaDesconto extends BaseProdutoFaixaDesconto
{

    public function verificaIncompatibilidade($faixaId, $produtoId)
    {
        $objFaixaDesconto = FaixaDescontoQuery::create()->findPk($faixaId);

        if ($objFaixaDesconto instanceof FaixaDesconto) {

            $c = new Criteria();
            $c1 = $c->getNewCriterion(FaixaDescontoPeer::QUANTIDADE_MINIMA, $objFaixaDesconto->getQuantidadeMinima(), Criteria::LESS_EQUAL);
            $c2 = $c->getNewCriterion(FaixaDescontoPeer::QUANTIDADE_MAXIMA, $objFaixaDesconto->getQuantidadeMinima(), Criteria::GREATER_EQUAL);

            $c1->addAnd($c2);

            $c3 = $c->getNewCriterion(FaixaDescontoPeer::QUANTIDADE_MINIMA, $objFaixaDesconto->getQuantidadeMaxima(), Criteria::GREATER_EQUAL);
            $c4 = $c->getNewCriterion(FaixaDescontoPeer::QUANTIDADE_MAXIMA, $objFaixaDesconto->getQuantidadeMinima(), Criteria::LESS_EQUAL);

            $c3->addAnd($c4);

            $c1->addOr($c3);

            $c->add($c1);

            $c->addJoin(FaixaDescontoPeer::ID, ProdutoFaixaDescontoPeer::FAIXA_DESCONTO_ID);
            $c->add(ProdutoFaixaDescontoPeer::PRODUTO_ID, $produtoId);

            $arrFaixaDescontoExistentes = FaixaDescontoPeer::doSelect($c);

            if (count($arrFaixaDescontoExistentes) == 0) {
                return true;
            }

            return false;
        }

        return false;
    }

}
