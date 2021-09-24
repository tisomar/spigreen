<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_categoria' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CategoriaQuery extends BaseCategoriaQuery
{
    /**
     * Filtra categorias que possam todas as categorias antescessoras disponíveis
     *
     * @return ModelCriteria
     */
    public function filterByParentDisponivel($v) {
        return $this->where("0 ". ($v ? Criteria::EQUAL : Criteria::NOT_EQUAL) . " (".self::sqlCountParentIndisponivel().")");
    }

    public static function sqlCountParentIndisponivel() {
        return "SELECT COUNT(1)
                    FROM qp1_categoria parent
                    WHERE
                    (
                        parent.NR_LFT < qp1_categoria.NR_LFT
                        AND parent.NR_RGT > qp1_categoria.NR_RGT
                        AND parent.DISPONIVEL = 0
                        AND parent.NR_LVL > 0
                    )";
    }

}
