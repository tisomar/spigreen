<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_configuracao_pontuacao_mensal' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ConfiguracaoPontuacaoMensal extends BaseConfiguracaoPontuacaoMensal
{
    public function setValorCompra($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorCompra($v);
    }
}
