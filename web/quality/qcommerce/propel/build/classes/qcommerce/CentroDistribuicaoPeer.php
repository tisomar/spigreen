<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_centro_distribuicao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CentroDistribuicaoPeer extends BaseCentroDistribuicaoPeer
{

    public static function getCentrosDistribuicaoList()
    {
        $query = CentroDistribuicaoQuery::create()
            ->filterByStatus(true)
            ->select(array('Id', 'Descricao'))
            ->orderById()
            ->find()
            ->toArray();

        return array_column($query, 'Descricao', 'Id');
    }

}
