<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_extrato' table.
 *
 * Tabela com os registros de entrada e saida de pontos
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Extrato extends BaseExtrato
{
    const TIPO_BONUS_DESTAQUE           = 'BONUS_DESTAQUE';
    const TIPO_BONUS_DESEMPENHO         = 'BONUS_DESEMPENHO';
    const TIPO_BONUS_ACELERACAO         = 'BONUS_ACELERACAO';
    const TIPO_BONUS_FRETE              = 'BONUS_FRETE';
    const TIPO_CLIENTE_PREFERENCIAL     = 'CLIENTE_PREFERENCIAL';
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
    const TIPO_TRANSFERENCIA            = 'TRANSFERENCIA';
    const TIPO_DISTRIBUICAO_REDE        = 'DISTRIBUICAO_REDE';
    const TIPO_VENDA_HOTSITE            = 'VENDA_HOTSITE';

    const TIPO_INDICACAO                = 'INDICACAO';
    const TIPO_VENDA_DISTRIBUIDOR       = 'VENDA_DISTRIBUIDOR';

    
    const QUANTIDADE_NIVEIS_BONUS_INDICACAO_INDIRETA = 4;
    
    protected static $tipos = array(
            self::TIPO_BONUS_DESTAQUE,
            self::TIPO_BONUS_DESEMPENHO,
            self::TIPO_BONUS_ACELERACAO,
            self::TIPO_BONUS_FRETE,
            self::TIPO_CLIENTE_PREFERENCIAL,
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
            self::TIPO_VENDA_DISTRIBUIDOR,
            self::TIPO_TRANSFERENCIA,
            self::TIPO_DISTRIBUICAO_REDE,
            self::TIPO_VENDA_HOTSITE
    );

    public static $tiposDesc = array(
        self::TIPO_BONUS_DESTAQUE => 'B??nus destaque',
        self::TIPO_BONUS_DESEMPENHO => 'B??nus desempenho',
        self::TIPO_BONUS_ACELERACAO => 'B??nus acelera????o',
        self::TIPO_BONUS_FRETE => 'B??nus frete',
        self::TIPO_CLIENTE_PREFERENCIAL => 'Cliente preferencial',
        self::TIPO_INDICACAO_DIRETA => 'Indica????o direta',
        self::TIPO_INDICACAO_INDIRETA => 'Indica????o indireta',
        self::TIPO_RESIDUAL => 'Recompra',
        self::TIPO_RESGATE => 'Resgate',
        self::TIPO_SISTEMA => 'Sistema',
        self::TIPO_REDE_BINARIA => 'Rede Bin??ria',
        self::TIPO_PARTICIPACAO_RESULTADOS => 'Participa????o de resultados',
        self::TIPO_PAGAMENTO_PEDIDO => 'Pagamento de pedido',
        self::TIPO_PAGAMENTO_PARCIAL_PEDIDO => 'Pagamento parcial de pedido',
        self::TIPO_VENDA_FRANQUEADO => 'Venda de franqueado',
        self::TIPO_PLANO_CARREIRA => 'Plano de carreira',
        self::TIPO_INDICACAO => 'Indica????o',
        self::TIPO_VENDA_DISTRIBUIDOR => 'Hotsite Distribuidor',
        self::TIPO_TRANSFERENCIA => 'Transfer??ncia de B??nus',
        self::TIPO_DISTRIBUICAO_REDE => 'Distribui????o de Rede',
        self::TIPO_VENDA_HOTSITE => 'B??nus Hotsite'
    );
    
    protected static $operacoes = array('+', '-');

    public function preInsert(\PropelPDO $con = null) 
    {
        if (!$this->getData()) :
            $this->setData(new Datetime());
        endif;
        
        if (!$this->getDataExpiracao() && '+' === $this->getOperacao()) :
            $this->setDataExpiracao(new Datetime('+1 year'));
        endif;
        
        return parent::preInsert($con);
    }


    public function setTipo($v) 
    {
        if (!in_array($v, self::$tipos)) :
            throw new InvalidArgumentException('Tipo inv??lido.');
        endif;
        
        parent::setTipo($v);
    }
    
    public function setOperacao($v) 
    {
        if (!in_array($v, self::$operacoes)) :
            throw new InvalidArgumentException('Opera????o inv??lida.');
        endif;
        
        parent::setOperacao($v);
    }

    public function getTipoDesc(){

        $retorno = $this->getTipo();

        if (isset(self::$tiposDesc[$this->getTipo()])) :
            $retorno = self::$tiposDesc[$this->getTipo()];
        endif;

        return $retorno;
    }

}