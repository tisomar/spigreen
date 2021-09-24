<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_plano' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Plano extends BasePlano
{
    /**
     * 
     * @return float
     */
    public function getValor()
    {
        if ($produto = $this->getProduto()) {
            return $produto->getValor();
        }
        
        return 0.0;
    }
    
    /**
     * 
     * @return Produto|null
     * @throws PropelException
     */
    public function getProduto()
    {
        return $this->getProdutoRelatedByProdutoId();
    }
    
    
    /**
     * 
     * @return Produto|null
     * @throws PropelException
     */
    public function getProdutoInicial()
    {
        return $this->getProdutoRelatedByProdutoId();
    }

    /**
     * @return Produto|null
     * @throws PropelException
     */
    public function getProdutoRelacionado()
    {
        $produto = ProdutoQuery::create()
            ->filterByPlanoId($this->getId())
            ->filterByDisponivel(true)
            ->findOne();

        return $produto;
    }

}
