<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_produto_atributo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoAtributo extends BaseProdutoAtributo
{
    
    public function isCor() {
        return $this->getType() == ProdutoAtributoPeer::TYPE_COR;
    }
    
}
