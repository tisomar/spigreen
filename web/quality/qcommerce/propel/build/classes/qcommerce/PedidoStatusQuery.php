<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_pedido_status' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PedidoStatusQuery extends BasePedidoStatusQuery
{

    public function filterByFrete($frete) {

        if ($frete == 'retirada_loja') {
            return $this->filterByMetodo(array(PedidoStatusPeer::METODO_OBRIGATORIO, PedidoStatusPeer::METODO_RETIRADA), Criteria::IN);
        } else {
            return $this->filterByMetodo(array(PedidoStatusPeer::METODO_OBRIGATORIO, PedidoStatusPeer::METODO_ENTREGA), Criteria::IN);
        }

    }
}
