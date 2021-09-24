<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_permissao_modulo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PermissaoModuloPeer extends BasePermissaoModuloPeer
{
    
    public static function getModulosContratados($level = array('min' => 1, 'max' => 2)) {
        
        return PermissaoModuloQuery::create()
                ->filterByMostrar(1)
                ->filterByTreeLevel($level)
                ->orderByTreeLeft()
            ->find();
        
    }
    
}
