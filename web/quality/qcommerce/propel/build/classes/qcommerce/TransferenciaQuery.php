<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_transferencia' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TransferenciaQuery extends BaseTransferenciaQuery
{

    public function filterByClienteNome($v) {
        $cliente = ClienteQuery::create()
            ->filterByNome('%' . $v . '%')
            ->_or()
            ->filterByNomeFantasia('%' . $v . '%')
            ->_or()
            ->filterByRazaoSocial('%' . $v . '%')
            ->findOne();

        return $this->filterByClienteRemetenteId($cliente->getId())->_or()->filterByClienteDestinatarioId($cliente->getId());
    } 

  

    public function filterByDataInicial($v)
    {

        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        $v = str_replace('%', '', $v);

        $v .= ' 00:00:00';

        return $this->filterByData($v, Criteria::GREATER_EQUAL);
    }

    public function filterByDataFinal($v)
    {
        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        $v = str_replace('%', '', $v);
        $v .= ' 23:59:59';

        return $this->filterByData($v, Criteria::LESS_EQUAL);
    }

    public function filterByCodigoPatrocinador($v) {
        $cliente = ClienteQuery::create()
            ->filterByChaveIndicacao($v)
            ->findOne();

        $v = intval(str_replace('%', '', $v));
        return $this->filterByClienteRemetenteId($cliente->getId())->_or()->filterByClienteDestinatarioId($cliente->getId());
    }
}
