<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_ticket' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Ticket extends BaseTicket
{
   public function getStatusDescricao($addIcon = false)
   {

      $template = '<h4><span class="label label-%s label-lg">%s</span></h4>';

      $label = '';
      $content = '';

      $statusList = TicketPeer::getStatusList();

      if (isset($statusList[$this->getStatus()])) {
         if ($addIcon) {
            switch ($this->getStatus()) {
               case TicketPeer::STATUS_FINALIZADO:
                  $content .= '<span class="icon-ok"></span> ';
                  $label = 'success';
                  break;
               case TicketPeer::STATUS_PENDENTE:
                  $content .= '<span class="icon-time"></span> ';
                  $label = 'danger';
                  break;
               case TicketPeer::STATUS_EM_ANDAMENTO:
                  $content .= '<span class="icon-time"></span> ';
                  $label = 'warning';
                  break;
            }
         }
         $content .= $statusList[$this->getStatus()];
      }

      if (!isset($label)) {
         $label = 'label-default';
      }

      return sprintf($template, $label, $content);
   }

   public function getStatusLabel()
   {
      $options = array(
         TicketPeer::STATUS_PENDENTE => array(
            'label' => 'danger',
            'icon' => 'icon-time',
            'title' => 'Pendente'
         ),
         TicketPeer::STATUS_FINALIZADO => array(
            'label' => 'success',
            'icon' => 'icon-ok',
            'title' => 'Finalizado'
         ),
         TicketPeer::STATUS_EM_ANDAMENTO => array(
            'label' => 'warning',
            'icon' => 'icon-ban-circle',
            'title' => 'Em andamento'
         ),
      );

      $title = $label = $icon = null;

      extract($options[$this->getStatus()]);

      return label($title, $label, $icon);
   }
}
