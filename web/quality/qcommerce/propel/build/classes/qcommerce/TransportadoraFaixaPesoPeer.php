<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_transportadora_faixa_peso' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TransportadoraFaixaPesoPeer extends BaseTransportadoraFaixaPesoPeer
{

    CONST TIPO_PRECO_FIXO = 'PRECO_FIXO';
    CONST TIPO_PRECO_KG = 'PRECO_KG';
    CONST TIPO_PORCENTAGEM = 'PORCENTAGEM';

    private static $tipoList = array(
        self::TIPO_PRECO_FIXO => 'Valor Fixo',
        self::TIPO_PRECO_KG => 'Valor por Kg',
        self::TIPO_PORCENTAGEM => 'Porcentagem',
    );

    public static function getListBy($field) {

        switch ($field) {

            case self::TIPO:
                return self::$tipoList;
            break;
        }

        return array();

    }

    public static function getTipoListAdmin() {

        return array(
            self::TIPO_PRECO_FIXO => 'Valor Fixo<br><span class="text-muted">O valor do frete é baseado em uma tabela de intervalos de peso com preço fixo por intervalo. Ex.: a partir de 10kg cobrar R$ 25,00, a partir de 15kg R$ 30,00, etc.</span>',
            self::TIPO_PORCENTAGEM => 'Porcentagem<br><span class="text-muted">O valor do frete é baseado em uma tabela de intervalos de peso, com porcentagem com base no valor do pedido, por intervalo. Ex.: a partir de 10kg cobrar 3% do valor do pedido, a partir de 20kg cobrar 5% do valor do pedido, etc.</span>',
            self::TIPO_PRECO_KG => 'Valor por Kg<br><span class="text-muted">O cálculo do frete é efetuado pela multiplicação do peso do produto pelo valor do kg cobrado, baseado em uma tabela de intervalo de pesos, ex: faixa a partir de 10kg cobrar R$ 1,00 por kg, faixa a partir de 20kg cobrar R$ 1,50 por kg, etc.</span>',
        );

    }

}
