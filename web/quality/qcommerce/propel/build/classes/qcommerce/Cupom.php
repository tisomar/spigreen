<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_CUPOM' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Cupom extends BaseCupom
{

    /**
     * Converte o valor para float se estiver formatado
     *
     * @param float $v
     * @return Cupom
     */
    public function setValorDesconto($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' ', '%'), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorDesconto($v);
    }

    /**
     * Converte o valor para float se estiver formatado
     *
     * @param float $v
     * @return Cupom
     */
    public function setValorMinimoCarrinho($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorMinimoCarrinho($v);
    }

    /**
     * Retorna o valor de desconto formatado
     *
     * @return string
     */
    public function getValorDescontoFormatado()
    {

        if ($this->getTipoDesconto() == CupomPeer::TIPO_DESCONTO_PORCENTAGEM)
        {
            $format = "%s%%";
            $valor = $this->getValorDesconto();
        }
        else
        {
            $format = "R$ %s";
            $valor = format_money($this->getValorDesconto());
        }

        return sprintf($format, $valor);
    }

    /**
     * Converte a data para um formato válido para banco de dados
     *
     * @param mixed $v
     * @return Cupom
     */
    public function setDataInicial($v)
    {
        $v = data_mysql($v);
        return parent::setDataInicial($v);
    }

    /**
     * Converte a data para um formato válido para banco de dados
     *
     * @param mixed $v
     * @return Cupom
     */
    public function setDataFinal($v)
    {
        $v = data_mysql($v);
        return parent::setDataFinal($v);
    }

    /**
     * Verifica se o cupom foi utilizado pelo cliente informado no parâmetro $cliente_id
     *
     * @param $cliente_id
     * @return bool
     */
    public function isUsedClienteId($cliente_id)
    {
        return PedidoQuery::create()
                ->filterByClienteId($cliente_id)
                ->filterByCupom($this)
                ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
            ->count() > 0;
    }

    /**
     * Verifica se o cupom está expirado
     *
     * @return bool
     */
    public function isExpired()
    {
        if ($this->getDataFinal() === null)
        {
            return false;
        }
        else
        {
            $hoje = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $validade = DateTime::createFromFormat('d/m/Y H:i:s', $this->getDataFinal('d/m/Y 23:59:59'));

            $intervalo = $hoje->diff($validade);

            return $intervalo->invert == 1;
        }

        return true;
    }

    /**
     * Verifica se o cupom está ativo
     *
     * @return bool
     */
    public function isActive()
    {
        $hoje = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
        $inicio = DateTime::createFromFormat('d/m/Y H:i:s', $this->getDataInicial('d/m/Y 00:00:00'));

        $intervalo = $hoje->diff($inicio);

        return $intervalo->invert == 1 && !$this->isExpired();
    }

    /**
     * Verifica se o cupom é valido para um determinado cliente ou para todos
     *
     * @param null $cliente_id
     * @return bool
     */
    public function isValidCupom($cliente_id = null)
    {
        return CupomPeer::isValid($this->getCupom(), $cliente_id);
    }

}
