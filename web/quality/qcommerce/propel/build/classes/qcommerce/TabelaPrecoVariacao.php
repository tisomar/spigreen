<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_tabela_preco_variacao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TabelaPrecoVariacao extends BaseTabelaPrecoVariacao
{
    public function setValorBase($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorBase($v);
    }

    public function setValorPromocional($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        return parent::setValorPromocional($v);
    }

    public function getValor()
    {
        return ($this->getValorPromocional() > 0) ? $this->getValorPromocional() : $this->getValorBase();
    }
    
}
