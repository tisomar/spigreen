<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_resgate_premios_acumulados' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ResgatePremiosAcumuladosPeer extends BaseResgatePremiosAcumuladosPeer
{
   public static function getSituacaoList()
   {
       return array(
           ResgatePremiosAcumulados::SITUACAO_PENDENTE => 'Pendente',
           ResgatePremiosAcumulados::SITUACAO_EFETUADO => 'Efetuado',
           ResgatePremiosAcumulados::SITUACAO_NAOEFETUADO => 'Não efetuado',
           ResgatePremiosAcumulados::SITUACAO_EXTORNADO => 'Extornado'
       );
   }
}
