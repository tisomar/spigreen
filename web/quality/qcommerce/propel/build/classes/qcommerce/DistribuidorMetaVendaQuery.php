<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_distribuidor_meta_venda' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuidorMetaVendaQuery extends BaseDistribuidorMetaVendaQuery
{
    /**
     *
     * @param Cliente $cliente
     * @param DateTime|null $mes
     * @return DistribuidorMetaVenda|null
     */
    public static function getMetaVendaDistribuidorNoMes(Cliente $cliente, DateTime $mes = null)
    {
        if (null === $mes) {
            $mes = new DateTime();
        }

        $query = DistribuidorMetaVendaQuery::create()
            ->filterByCliente($cliente)
            ->filterByDataInicial($mes, Criteria::LESS_EQUAL)
            ->filterByDataFinal($mes, Criteria::GREATER_EQUAL);

        return $query->findOne();
    }
}
