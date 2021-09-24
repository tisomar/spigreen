<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_PRODUTO_CATEGORIA' table.
 *
 * Categoria de Produtos
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoCategoria extends BaseProdutoCategoria
{

    public function __construct($categoria_id = null, $produto_id = null)
    {
        parent::__construct();
        
        if (!is_null($produto_id))
        {
            $this->setProdutoId($produto_id);
        }
        if (!is_null($categoria_id))
        {
            $this->setCategoriaId($categoria_id);
        }
    }

}
