<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_distribuicao' table.
 *
 * Tabela onde serão gravados as distribuicoes mensais de pontos para as redes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuicaoPeer extends BaseDistribuicaoPeer
{
    /**
     * 
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            Distribuicao::STATUS_AGUARDANDO_PREVIEW     => 'Aguardando Preview',
            Distribuicao::STATUS_PROCESSANDO_PREVIEW    => 'Processando Preview',
            Distribuicao::STATUS_PREVIEW                => 'Preview',
            Distribuicao::STATUS_AGUARDANDO             => 'Aguardando',
            Distribuicao::STATUS_PROCESSANDO            => 'Processando',
            Distribuicao::STATUS_DISTRIBUIDO            => 'Distribuído',
            Distribuicao::STATUS_CANCELADO              => 'Cancelado'
        );
    }
}
