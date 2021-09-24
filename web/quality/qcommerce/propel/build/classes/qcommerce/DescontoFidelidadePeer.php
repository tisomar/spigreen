<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_desconto_fidelidade' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DescontoFidelidadePeer extends BaseDescontoFidelidadePeer
{
    /**
     *
     * Valida qual o desconto dar ao cliente.
     *
     * @param $mesesActive
     * @return DescontoFidelidade|null
     */

    public static function getDescontoFidelidadeActive($mesesActive){
        $return = null;

        $descontoFidelidade = DescontoFidelidadeQuery::create()
            ->filterByMesInicial($mesesActive, Criteria::LESS_EQUAL)
            ->filterByMesFinal($mesesActive, Criteria::GREATER_EQUAL)
            ->findOne();

        if($descontoFidelidade instanceof DescontoFidelidade){
            $return = $descontoFidelidade;
        }

        return $return;
    }
}
