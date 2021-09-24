<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_suporte' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class SuportePeer extends BaseSuportePeer
{
    public static function getMostrarList()
    {
        return array(
            "1" => 'Sim',
            "0" => 'Não',
        );
    }
    
    public static function getTipoList()
    {
        return array(
            Suporte::TIPO_TEXTO => 'Texto',
            Suporte::TIPO_VIDEO => 'Vídeo',
            Suporte::TIPO_ARQUIVO => 'Arquivo',
            Suporte::TIPO_VIDEO_AULA => 'Vídeo Aula'
        );
    }
}
