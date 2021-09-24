<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_distribuicao' table.
 *
 * Tabela onde serão gravados as distribuicoes mensais de pontos para as redes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Distribuicao extends BaseDistribuicao
{
    const STATUS_AGUARDANDO_PREVIEW     = 'AGUARDANDO_PREVIEW';
    const STATUS_PROCESSANDO_PREVIEW    = 'PROCESSANDO_PREVIEW';
    const STATUS_PREVIEW                = 'PREVIEW';
    const STATUS_AGUARDANDO             = 'AGUARDANDO';
    const STATUS_PROCESSANDO            = 'PROCESSANDO';
    const STATUS_DISTRIBUIDO            = 'DISTRIBUIDO';
    const STATUS_CANCELADO              = 'CANCELADO';
    
    protected static $statuses = array(
        self::STATUS_AGUARDANDO_PREVIEW,
        self::STATUS_PROCESSANDO_PREVIEW,
        self::STATUS_PREVIEW,
        self::STATUS_AGUARDANDO,
        self::STATUS_PROCESSANDO,
        self::STATUS_DISTRIBUIDO,
        self::STATUS_CANCELADO
    );
    
    public function __construct() {
        parent::__construct();
        
        $this->setTotalPontos(0);
    }
    
    public function setStatus($v) 
    {
        if (!in_array($v, self::$statuses)) {
            throw new InvalidArgumentException('Status inválido.');
        }
        
        return parent::setStatus($v);
    }
    
    /**
     * 
     * @return string
     */
    public function getStatusDesc()
    {
        $status = $this->getStatus();
        $list = DistribuicaoPeer::getStatusList();
        
        return isset($list[$status]) ? $list[$status] : '';
    }
    
    public function setData($v) 
    {
        if (is_string($v)) {
            $v = DateTime::createFromFormat('d/m/Y', $v);
            if (!$v) {
                throw new InvalidArgumentException('Data inválida.');
            }
            $v->setTime(0, 0, 0);
        }
        
        return parent::setData($v);
    }

    public function setDataInicio($v)
    {
        if (is_string($v)) {
            $v = DateTime::createFromFormat('d/m/Y', $v);
            if (!$v) {
                throw new InvalidArgumentException('Data inválida.');
            }
            $v->setTime(0, 0, 0);
        }

        return parent::setDataInicio($v);
    }

    public function setDataFinal($v)
    {
        if (is_string($v)) {
            $v = DateTime::createFromFormat('d/m/Y', $v);
            if (!$v) {
                throw new InvalidArgumentException('Data inválida.');
            }
            $v->setTime(0, 0, 0);
        }

        return parent::setDataFinal($v);
    }

    public function isCancelable()
    {
        $cancelableStatuses = [
            Distribuicao::STATUS_AGUARDANDO_PREVIEW,
            Distribuicao::STATUS_PROCESSANDO_PREVIEW,
            Distribuicao::STATUS_PREVIEW,
            Distribuicao::STATUS_AGUARDANDO
        ];

        return in_array($this->getStatus(), $cancelableStatuses);
    }
}
