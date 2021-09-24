<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_extrato' table.
 *
 * Tabela com os registros de entrada e saida de pontos
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ExtratoQuery extends BaseExtratoQuery
{
    /**
     * @param $v
     * @return ExtratoQuery
     */
    public function filterByDataInicial($v)
    {

        if (strpos($v, '/') !== false) :
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        endif;

        $v .= ' 00:00:00';

        return $this->filterByData($v, Criteria::GREATER_EQUAL);
    }

    /**
     * @param $v
     * @return ExtratoQuery
     */
    public function filterByDataFinal($v)
    {
        if (strpos($v, '/') !== false) :
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        endif;

        $v .= ' 23:59:59';

        return $this->filterByData($v, Criteria::LESS_EQUAL);
    }
}
