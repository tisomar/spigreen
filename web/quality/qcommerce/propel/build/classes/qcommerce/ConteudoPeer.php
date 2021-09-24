<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CONTEUDO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ConteudoPeer extends BaseConteudoPeer
{
    
    public static function get($key)
    {
        $object = ConteudoQuery::create()->findOneByChave($key);
        
        if (is_null($object)) {
            throw new Exception('chave não encontrada!');
        }
        
        return $object;
    }

//    public static function retrieveByPKWithI18n($pk)
//    {
//
//        return ConteudoQuery::create()
//            ->filterById($pk)
////            ->joinWithI18n(QPTranslator::getLocale(), Criteria::INNER_JOIN)
//            ->findOne();
//    }

}
