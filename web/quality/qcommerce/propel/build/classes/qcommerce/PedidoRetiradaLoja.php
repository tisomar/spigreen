<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_pedido_retirada_loja' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PedidoRetiradaLoja extends BasePedidoRetiradaLoja
{
    public function getPrazoExtenso() {
        return plural($this->getPrazo(), '%s dia &uacute;til', '%s dias &uacute;teis');
    }

    public function setValor($v)
    {
        if (!is_numeric($v))
        {
            $v = str_replace(array('R$', ' ', '%'), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValor($v);
    }
}
