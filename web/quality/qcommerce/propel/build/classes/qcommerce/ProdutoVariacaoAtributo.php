<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_produto_variacao_atributo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoVariacaoAtributo extends BaseProdutoVariacaoAtributo
{
    public function delete(\PropelPDO $con = null)
    {
        if (!is_null($this->getProdutoVariacao())) {
            $this->getProdutoVariacao()->delete();
        }
        return parent::delete($con);
    }
}
