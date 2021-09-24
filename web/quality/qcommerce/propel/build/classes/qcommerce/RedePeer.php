<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_REDE' table.
 *
 * Redes sociais pertencentes ao site
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class RedePeer extends BaseRedePeer
{
    CONST ATIVO_SIM = 1;
    CONST ATIVO_NAO = 0;

    public static function getOptionsList($fieldName)
    {

        switch ($fieldName) {
            
            case self::ATIVO:
                $options = array(
                    self::ATIVO_SIM => 'Sim',
                    self::ATIVO_NAO => 'Não',
                );
                break;
                
            default:
                $options = array();
                break;
                
        }
        
        return is_array($options) ? $options : array();
    }

    private static $redes;

    public static function getAtivos() {
        if (self::$redes == null) {
            self::$redes = RedeQuery::create()->filterByAtivo(RedePeer::ATIVO_SIM)->orderByOrdem()->find();
        }
        return self::$redes;
    }
}
