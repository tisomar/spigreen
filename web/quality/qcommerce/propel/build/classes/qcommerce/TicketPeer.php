<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_ticket' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TicketPeer extends BaseTicketPeer
{
   const STATUS_PENDENTE = 'PENDENTE';
   const STATUS_FINALIZADO = 'FINALIZADO';
   const STATUS_EM_ANDAMENTO = 'EMANDAMENTO';

   public static function getStatusList($v = null) {
      return array(
         self::STATUS_FINALIZADO => 'Finalizado',
         self::STATUS_EM_ANDAMENTO => 'Em andamento',
         self::STATUS_PENDENTE => 'Pendente',
      );
   }
}
