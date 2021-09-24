<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_FAQ' table.
 *
 * Perguntas Frequentes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Faq extends BaseFaq {

    const SIM = 1;
    const NAO = 0;

    /**
     * Retorna o texto de acordo se é para mostar ou nao o destaque
     * @return string
     */
    public function getDescMostrar() {
        return self::getDescConstMostrar($this->getMostrar());
    }

    /**
     * Retorna descricao para constanstes de mostrar
     * @param string $strMostrar
     * @return string
     */
    public static function getDescConstMostrar($strMostrar) {
        switch ($strMostrar) {
            case self::SIM : $strRet = 'Sim';
                break;
            case self::NAO : $strRet = 'Não';
                break;
            default : $strRet = '';
                break;
        }
        return $strRet;
    }
}
