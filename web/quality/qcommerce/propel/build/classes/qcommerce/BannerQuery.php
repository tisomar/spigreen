<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_BANNER' table.
 *
 * Banners do sistema
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class BannerQuery extends BaseBannerQuery
{

    /**
     * Retorna uma coleção de banners ativos.
     * 
     * @param string $type      define o tipo de banner
     * @param integer $limit    define o número máximo de banners
     * 
     * @return PropelObjectCollection
     */
    public static function  findBannerByType($type = null, $limit = 5)
    {
        
        $allTypes = BannerPeer::getTipoList();
        
        if (is_string($type) && !isset($allTypes[$type])) {
            trigger_error('Tipo de banner não encontrado');
        }
        
        if (!is_numeric($limit)) {
            $limit = 5;
        }
        
        return BannerQuery::create()
                    ->_if(is_string($type))
                        ->filterByTipo($type)
                    ->_endif()
                    ->filterByMostrar(Banner::SIM)
                    ->orderByOrdem(Criteria::ASC)
                    ->limit($limit)
                ->find();
    }
}
