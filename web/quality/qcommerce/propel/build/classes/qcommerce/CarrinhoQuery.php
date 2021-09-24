<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CARRINHO' table.
 *
 * Carrinho
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CarrinhoQuery extends BaseCarrinhoQuery
{

    public function createNew()
    {
        $carrinho = new \Carrinho();
        return $carrinho;
    }
}
