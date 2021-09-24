<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_GALERIA' table.
 *
 * Galerias de imagem
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class GaleriaPeer extends BaseGaleriaPeer
{
    
    /**
     * retorna uma tag select para campo mostrar
     * @param string $strValueSelected
     * @param array $arrAttributtes
     * @return string
     */
    public static function getFormSelect($strValueSelected, $arrAttributtes = array()) {

        $c = new Criteria();
        $c->addAscendingOrderByColumn(self::NOME);

        return get_form_select_object(self::doSelect($c), $strValueSelected, 'getId', 'getNome', $arrAttributtes, array('' => 'Nenhuma'));
    }
    
    
}
