<?php

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_produto_atributo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoAtributoPeer extends BaseProdutoAtributoPeer {

    CONST TYPE_OUTROS = 0;
    CONST TYPE_COR = 1;

    public static function getTypeList() {
        return array(
            self::TYPE_COR => 'Cor<br><i class="text-muted">Permite associar a uma cor da biblioteca de cores</i>',
            self::TYPE_OUTROS => 'Outro',
        );
    }

}
