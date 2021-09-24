<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_pedido_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */

class PedidoItem extends BasePedidoItem
{
    /**
     * @param PropelPDO|null $con
     * @throws PropelException
     */
    public function postSave(\PropelPDO $con = null)
    {
        parent::postSave($con);
        $this->getPedido()->calculateItemsValorTotal()->save();
    }

    /**
     * @param PropelPDO|null $con
     * @throws PropelException
     */
    public function postDelete(\PropelPDO $con = null)
    {
        parent::postDelete($con);
        $this->getPedido()->calculateItemsValorTotal()->save();
    }

    /**
     * @param Produto|null $v
     * @return $this
     */
    public function setProduto(Produto $v = null)
    {
        $this->setPeso($v->getPeso());
        $this->setValorUnitario($v->getValorDesconto());

        return $this;
    }

    /**
     * @return float|int
     */
    public function getValorTotal()
    {
        return $this->getQuantidade() * $this->getValorUnitario();
    }
    
}
