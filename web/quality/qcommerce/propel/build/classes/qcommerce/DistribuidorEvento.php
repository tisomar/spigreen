<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_distribuidor_evento' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuidorEvento extends BaseDistribuidorEvento
{
    const STATUS_ANDAMENTO  = 'ANDAMENTO';
    const STATUS_FINALIZADO = 'FINALIZADO';

    public function setStatus($v)
    {
        if (!in_array($v, array(self::STATUS_ANDAMENTO, self::STATUS_FINALIZADO))) {
            throw new InvalidArgumentException('Status inválido.');
        }

        return parent::setStatus($v);
    }

    public static function getChoicesStatus()
    {
        return array(
            self::STATUS_ANDAMENTO => 'Andamento',
            self::STATUS_FINALIZADO => 'Finalizado'
        );
    }

    public function setByArray($array, $type = BasePeer::TYPE_FIELDNAME, &$erros = array())
    {
        if (isset($array['RECORRENCIA'])) {

            switch ($array['RECORRENCIA']) {
                case 'dia':
                    $array['DATA'] = new DateTime('+1 day');
                    break;
                case 'semana':
                    $array['DATA'] = new DateTime('+1 week');
                    break;
                case 'quinzena':
                    $array['DATA'] = new DateTime('+14 days');
                    break;
                case 'mes':
                    $array['DATA'] = new DateTime('+1 month');
                    break;
                case 'personalizado':
                    if (empty($array['DATA'])) {
                        $erros[] = 'Data da recorrência não informada.';
                    }
                    break;
                default:
                    $erros[] = 'Recorrência inválida.';
                    break;
            }
        }

        if (!empty(($array['DATA']))) {
            if ($array['DATA'] instanceof DateTime) {
                $array['DATA']->setTime(0, 0, 0);
            } else {
                $dt = DateTime::createFromFormat('d/m/Y', $array['DATA']);
                if ($dt instanceof DateTime) {
                    $dt->setTime(0, 0, 0);
                    $array['DATA'] = $dt;
                } else {
                    $erros[] = 'Data inválida.';
                }
            }
        }

        return parent::setByArray($array, $type);
    }

    public function isFinalizado()
    {
        return $this->getStatus() === self::STATUS_FINALIZADO;
    }

    public function isEmAndamento()
    {
        return $this->getStatus() === self::STATUS_ANDAMENTO;
    }

    /**
     *
     * @return boolean
     */
    public function isAtrasado()
    {
        if (!$this->isEmAndamento()) {
            return false;
        }

        $t1 = new DateTime($this->getData('Y-m-d'));
        $t2 = new DateTime(date('Y-m-d'));

        return  $t1 < $t2;
    }

    /**
     *
     * @return bool
     */
    public function isDoDia()
    {
        $t1 = new DateTime($this->getData('Y-m-d'));
        $t2 = new DateTime(date('Y-m-d'));

        return $t1 == $t2;
    }
}
