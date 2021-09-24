<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_regiao_frete_gratis' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class FreteGratisRegiao extends BaseFreteGratisRegiao
{
    /**
     * @param int $v
     * @return FreteGratisRegiao
     */
    public function setCepInicial($v) {
        $v = preg_replace('/[^0-9]/', '', $v);

        return parent::setCepInicial($v);
    }

    /**
     * @param int $v
     * @return FreteGratisRegiao
     */
    public function setCepFinal($v) {
        $v = preg_replace('/[^0-9]/', '', $v);

        return parent::setCepFinal($v);
    }

    /**
     * @param float $v
     * @return FreteGratisRegiao
     */
    public function setValorMinimo($v) {
        $v = preg_replace('/[^0-9\.,]/', '', $v);
        $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);

        return parent::setValorMinimo($v);
    }
}
