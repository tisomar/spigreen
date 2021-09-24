<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CLIENTE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ClienteQuery extends BaseClienteQuery
{
    public function filterByNomeRazaoSocial($v)
    {
        return $this->filterByNome('%' . $v . '%', Criteria::LIKE)->_or()->filterByRazaoSocial('%' . $v . '%', Criteria::LIKE);
    }

    public function filterByCpfCnpj($v)
    {
        return $this->filterByCpf('%' . $v . '%', Criteria::LIKE)->_or()->filterByCnpj('%' . $v . '%', Criteria::LIKE);
    }

    public function filterByEqualCpfCnpj($v)
    {
        return $this->filterByCpf($v)->_or()->filterByCnpj($v);
    }
    
    public function filterByMesAniversario($mes)
    {
        return $this->add('mes_aniversario', sprintf("MONTH(%s) = %s", ClientePeer::DATA_NASCIMENTO, $mes), Criteria::CUSTOM);
    }

    public function filterByCidade($cidade)
    {
        return $this
            ->useEnderecoQuery()
                ->filterByCidadeId($cidade)
            ->endUse();
    }

    /**
     * @param $situacao
     * @return $this
     * @throws Exception
     */
    public function filterBySituacaoPlano($situacao)
    {
        switch ($situacao) {
            case 'inadimplentes':
                $this->filterByLivreMensalidade(false);
                $this->filterByVencimentoMensalidade(new DateTime('now'), Criteria::LESS_THAN);
                break;
            case 'em_dia':
                $this->filterByLivreMensalidade(true);
                $this->_or();
                $this->filterByVencimentoMensalidade(new DateTime('now'), Criteria::GREATER_EQUAL);
                break;
        }
        
        return $this;
    }

    public function filterByTipoCliente($valuePlano)
    {
        switch ($valuePlano) {
            case '%c_plano%':
                $this->filterByPlanoId(null, Criteria::NOT_EQUAL);
                break;
            case '%s_plano%':
                $this->filterByPlanoId(null, Criteria::EQUAL);
                break;
        }

        return $this;
    }

    public function filterByDataDe($v)
    {

        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        $v .= ' 00:00:00';

        return $this->filterByCreatedAt($v, Criteria::GREATER_EQUAL);
    }

    public function filterByDataAte($v)
    {
        if (strpos($v, '/') !== false) {
            $v = format_data($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        $v .= ' 23:59:59';

        return $this->filterByCreatedAt($v, Criteria::LESS_EQUAL);
    }

    public function orderByNomeRazaoSocial() {
        return $this->addAscendingOrderByColumn(sprintf(
            'IF(%s IS NULL, %s, %s)',
            ClientePeer::CNPJ,
            ClientePeer::NOME,
            ClientePeer::RAZAO_SOCIAL
        ));
    }
    
}
