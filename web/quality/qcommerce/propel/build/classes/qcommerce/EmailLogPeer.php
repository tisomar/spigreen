<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_email_log' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class EmailLogPeer extends BaseEmailLogPeer
{
    # Status para quando o e-mail não foi enviado
    CONST STATUS_PENDENTE = 1;

    # Status para quando o e-mail foi enviado
    CONST STATUS_ENVIADO = 2;

    /**
     * Retorna a lista de status disponível
     * @return array
     */
    public static function getStatusList() {
        return array (
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_ENVIADO => 'Enviado',
        );
    }

    /**
     * Retorna o status traduzido com base no parâmetro informado
     * @param $status
     * @return String|null
     */
    public static function getNameByStatus($status) {
        $list = self::getStatusList();
        if (isset($list[$status])) {
            return $list[$status];
        }
        return null;
    }

}
