<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_hotsite' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class HotsitePeer extends BaseHotsitePeer
{
    public function clientePossuiHotsite(Cliente $cliente)
    {
        return HotsiteQuery::create()->filterByCliente($cliente)->find()->count() > 0;
    }
}
