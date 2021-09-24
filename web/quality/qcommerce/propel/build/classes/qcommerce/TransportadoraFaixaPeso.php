<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_transportadora_faixa_peso' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TransportadoraFaixaPeso extends BaseTransportadoraFaixaPeso
{

    public function getValorFormatado() {
        if ($this->getTipo() == TransportadoraFaixaPesoPeer::TIPO_PORCENTAGEM) {
            return (int) $this->getValor() . '%';
        } else {
            return 'R$ ' . format_money($this->getValor());
        }
    }

    public function setValor($v) {
        $v = preg_replace('/[^0-9\.,]/', '', $v);
        $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        return parent::setValor($v);
    }

    public function calcularFrete($valor, $peso) {

        switch ($this->getTipo()) {

            case TransportadoraFaixaPesoPeer::TIPO_PORCENTAGEM:
                $valor = $valor * ($this->getValor() / 100);
                break;

            case TransportadoraFaixaPesoPeer::TIPO_PRECO_FIXO:
                $valor = $this->getValor();
                break;

            case TransportadoraFaixaPesoPeer::TIPO_PRECO_KG:
                $valor = $this->getValor() * ($peso/1000);
                break;

        }

        return $valor;
    }

}
