<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_resgate' table.
 *
 * Tabela com as solicitacoes de resgate de pontos dos clientes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ResgatePeer extends BaseResgatePeer
{
    public static function getSituacaoList()
    {
        return array(
            Resgate::SITUACAO_PENDENTE => 'Pendente',
            Resgate::SITUACAO_EFETUADO => 'Efetuado',
            Resgate::SITUACAO_NAOEFETUADO => 'Não efetuado'
        );
    }
}
