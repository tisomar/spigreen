<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_retirada_loja' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class RetiradaLoja extends BaseRetiradaLoja
{
    public function setValor($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' ', '%'), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValor($v);
    }

    protected static function cleanupSlugPart($slug, $replacement = '-')
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $replacement, $clean);
        return $clean;
    }

    public function getPrazoExtenso() {
        return plural($this->getPrazo(), '%s dia útil', '%s dias úteis');
    }
}
