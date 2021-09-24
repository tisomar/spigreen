<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_transportadora_regiao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TransportadoraRegiao extends BaseTransportadoraRegiao
{

    public function setCepInicial($v) {
        $v = preg_replace('/[^0-9]/', '', $v);
        return parent::setCepInicial($v);
    }

    public function setCepFinal($v) {
        $v = preg_replace('/[^0-9]/', '', $v);
        return parent::setCepFinal($v);
    }
}
