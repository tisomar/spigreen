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
class PedidoStatusPeer extends BasePedidoStatusPeer
{
    CONST METODO_OBRIGATORIO = 'OBRIGATORIO';
    CONST METODO_ENTREGA = 'ENTREGA';
    CONST METODO_RETIRADA = 'RETIRADA';

    /**
     * Define o ID do registro correspondente ao Status "Aguardando pagamento"
     */
    CONST STATUS_ID_AGUARDANDO_PAGAMENTO = 1;

}
