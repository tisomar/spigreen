<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_NEWSLETTER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class NewsletterQuery extends BaseNewsletterQuery
{
    public function filterByDataDe($v) {

        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        return $this->filterByDataCadastro($v . ' 00:00:00', Criteria::GREATER_EQUAL);
    }

    public function filterByDataAte($v) {
        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        return $this->filterByDataCadastro($v . ' 23:59:59', Criteria::LESS_EQUAL);
    }
}
