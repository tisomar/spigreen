<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_email_log' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class EmailLog extends BaseEmailLog
{
    public function getStatusLabel() {
        if ($this->getStatus() == EmailLogPeer::STATUS_ENVIADO) {
            return '<span class="text-success"><i class="icon-check-sign"></i> '. EmailLogPeer::getNameByStatus($this->getStatus()) .'</span>';
        } elseif ($this->getStatus() == EmailLogPeer::STATUS_PENDENTE) {
            return '<span class="text-danger"><i class="icon-times-circle"></i> '. EmailLogPeer::getNameByStatus($this->getStatus()) .'</span>';
        }
    }
}
