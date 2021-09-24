<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_FRETE_CEP' table.
 *
 * Tabela dos descontos de frete para capital e interior com base em faixas de CEP
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class FreteCepPeer extends BaseFreteCepPeer
{
    /**
     * retorna uma tag select para campo ativo
     * @param string $strValueSelected
     * @param array $arrOptions
     * @param array $arrAttributtes
     * @return string
     */
    public static function getFormSelectAtivo($strValueSelected, $arrOptions = false, $arrAttributtes = array()) {
        $arrAttributtes['name']  = isset($arrAttributtes['name'])    ? $arrAttributtes['name']    : 'frete[ATIVO]';
        $arrAttributtes['id']    = isset($arrAttributtes['id'])      ? $arrAttributtes['id']      : 'ativo';
        $arrAttributtes['title'] = isset($arrAttributtes['title'])   ? $arrAttributtes['title']   : 'Indica se o desconto est&aacute; ativo ou n&atilde;o';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false) {
            $arrOptions = array(
                1 => "Sim",
                0 => "N&atilde;o",
            );
        }
        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }
}
