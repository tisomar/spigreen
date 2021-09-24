<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_extrato_individual' table.
 *
 * Tabela com os registros de entrada e saida de pontos
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ExtratoIndividual extends BaseExtratoIndividual
{

    const TIPO_INDICACAO_DIRETA         = 'INDICACAO_DIRETA';
    const TIPO_INDICACAO_INDIRETA       = 'INDICACAO_INDIRETA';
    const TIPO_RESIDUAL                 = 'RESIDUAL';
    const TIPO_RESGATE                  = 'RESGATE';
    const TIPO_SISTEMA                  = 'SISTEMA';
    const TIPO_REDE_BINARIA             = 'REDE_BINARIA';
    const TIPO_PARTICIPACAO_RESULTADOS  = 'PARTICIPACAO_RESULTADOS';
    const TIPO_PAGAMENTO_PEDIDO         = 'PAGAMENTO_PEDIDO';
    const TIPO_PAGAMENTO_PARCIAL_PEDIDO = 'PAGAMENTO_PARCIAL_PEDIDO';
    const TIPO_VENDA_FRANQUEADO         = 'VENDA_FRANQUEADO';
    const TIPO_PLANO_CARREIRA           = 'PLANO_CARREIRA';

    const TIPO_INDICACAO                = 'INDICACAO';
    const TIPO_VENDA_DISTRIBUIDOR       = 'VENDA_DISTRIBUIDOR';


    const QUANTIDADE_NIVEIS_BONUS_INDICACAO_INDIRETA = 4;

    protected static $tipos = array(
        self::TIPO_INDICACAO_DIRETA,
        self::TIPO_INDICACAO_INDIRETA,
        self::TIPO_RESIDUAL,
        self::TIPO_RESGATE,
        self::TIPO_SISTEMA,
        self::TIPO_REDE_BINARIA,
        self::TIPO_PARTICIPACAO_RESULTADOS,
        self::TIPO_PAGAMENTO_PEDIDO,
        self::TIPO_PAGAMENTO_PARCIAL_PEDIDO,
        self::TIPO_VENDA_FRANQUEADO,
        self::TIPO_PLANO_CARREIRA,
        self::TIPO_INDICACAO,
        self::TIPO_VENDA_DISTRIBUIDOR
    );

    protected static $tiposDesc = array(
        self::TIPO_INDICACAO_DIRETA => 'Indicação direta',
        self::TIPO_INDICACAO_INDIRETA => 'Indicação indireta',
        self::TIPO_RESIDUAL => 'Recompra',
        self::TIPO_RESGATE => 'Resgate',
        self::TIPO_SISTEMA => 'Sistema',
        self::TIPO_REDE_BINARIA => 'Rede Binária',
        self::TIPO_PARTICIPACAO_RESULTADOS => 'Participação de resultados',
        self::TIPO_PAGAMENTO_PEDIDO => 'Pagamento de pedido',
        self::TIPO_PAGAMENTO_PARCIAL_PEDIDO => 'Pagamento parcial de pedido',
        self::TIPO_VENDA_FRANQUEADO => 'Venda de franqueado',
        self::TIPO_PLANO_CARREIRA => 'Plano de carreira',
        self::TIPO_INDICACAO => 'Indicação',
        self::TIPO_VENDA_DISTRIBUIDOR => 'Hotsite Distribuidor'
    );

    protected static $operacoes = array('+', '-');


    public function preInsert(\PropelPDO $con = null)
    {
        if (!$this->getData()) {
            $this->setData(new Datetime());
        }

        if (!$this->getDataExpiracao() && '+' === $this->getOperacao()) {
            $this->setDataExpiracao(new Datetime('+1 year'));
        }

        return parent::preInsert($con);
    }


    public function setTipo($v)
    {
        if (!in_array($v, self::$tipos)) {
            throw new InvalidArgumentException('Tipo inválido.');
        }

        parent::setTipo($v);
    }

    public function setOperacao($v)
    {
        if (!in_array($v, self::$operacoes)) {
            throw new InvalidArgumentException('Operação inválida.');
        }

        parent::setOperacao($v);
    }

    public function getTipoDesc(){

        $retorno = $this->getTipo();

        if (isset(self::$tiposDesc[$this->getTipo()])) {
            $retorno = self::$tiposDesc[$this->getTipo()];
        }

        return $retorno;
    }
}
