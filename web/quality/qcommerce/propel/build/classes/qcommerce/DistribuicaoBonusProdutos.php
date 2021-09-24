<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_distribuicao_bonus_produtos' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuicaoBonusProdutos extends BaseDistribuicaoBonusProdutos
{
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
}
