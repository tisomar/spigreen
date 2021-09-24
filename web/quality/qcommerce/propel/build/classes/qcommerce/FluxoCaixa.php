<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_fluxo_caixa' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class FluxoCaixa extends BaseFluxoCaixa
{
    public function setDataVencimento($v)
    {
        $v .= ' 00:00:00';
        return parent::setDataVencimento($v); // TODO: Change the autogenerated stub
    }
}
