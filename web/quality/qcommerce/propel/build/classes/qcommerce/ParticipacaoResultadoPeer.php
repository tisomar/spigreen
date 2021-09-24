<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_participacao_resultado' table.
 *
 * Tabela onde serão gravados as participações nos resultados
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ParticipacaoResultadoPeer extends BaseParticipacaoResultadoPeer
{
    /**
     * 
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW     => 'Aguardando Preview',
            ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW    => 'Processando Preview',
            ParticipacaoResultado::STATUS_PREVIEW                => 'Preview',
            ParticipacaoResultado::STATUS_AGUARDANDO             => 'Aguardando',
            ParticipacaoResultado::STATUS_PROCESSANDO            => 'Processando',
            ParticipacaoResultado::STATUS_DISTRIBUIDO            => 'Distribuído',
            ParticipacaoResultado::STATUS_CANCELADO              => 'Cancelado'
        );
    }
}
