<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_PRODUTO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoQuery extends BaseProdutoQuery
{
    public function search($terms) {

        if (!is_array($terms)) {
            $terms = explode(" ", $terms);
        }

        $this->filterByTags(
            array_map(
                function($v) { return "%$v%"; },
                $terms
            ), Criteria::CONTAINS_SOME)
            ->_or();

        #$orderBy = array();
        $condition = array();
        #$limit = count($terms);

        foreach ($terms as $i => $termo) {

            $clause['NOME']         = ProdutoPeer::NOME . " " . Criteria::LIKE . " '%" . $termo . "%'";
            #$clause['DESCRICAO']    = ProdutoPeer::DESCRICAO . " " . Criteria::LIKE . " '%" . $termo . "%'";

            $condition[0][] = $name0 = 'cond' . $i;
            #$condition[1][] = $name1 = 'cond' . ($i + $limit);
            #$condition[2][] = $name2 = 'cond' . ($i + ($limit * 2));

            $this->condition($name0, $clause['NOME']);
            #$this->condition($name1, $clause['NOME']);
            #$this->condition($name2, $clause['DESCRICAO']);

            #$orderBy[] = $clause['NOME'];
        }


        #$this->addDescendingOrderByColumn('(' . implode(' AND ', $orderBy) . ')');
        #foreach ($orderBy as $clause) {
            #$this->addDescendingOrderByColumn('(' . $clause . ')');
        #}

        $this->where($condition[0], Criteria::LOGICAL_AND);
        #$this->_or()->where($condition[1], Criteria::LOGICAL_OR);
        #$this->_or()->where($condition[2], Criteria::LOGICAL_OR);

        return $this;
    }

    /**
     * @param $v
     * @return ModelCriteria
     */
    public function filterByCategoriaId($v) {
        $v = preg_replace('/[^0-9]/', '', $v);
        return $this->useProdutoCategoriaQuery()
            ->filterByCategoriaId($v)
            ->endUse();
    }

    /**
     * @param $v
     * @return ModelCriteria
     */
    public function filterByReferencia($v)
    {
        return $this->useProdutoVariacaoQuery()
            ->filterBySku('%' . $v . '%')
            ->endUse();
    }

    /**
     * @return ModelCriteria
     */
    public function filterByEmPromocao()
    {
        return $this->useProdutoVariacaoQuery()
                        ->filterByIsMaster(true)
                        ->filterByValorPromocional(0, Criteria::GREATER_THAN)
                    ->endUse();
    }

    /**
     * Filter by availability.
     *
     * @param bool $v
     * @return mixed
     */
    public function filterByDisponivel($v = true)
    {
        $v = str_replace('%', '', $v);

        if (!ClientePeer::isAuthenticad()):
            $this->filterByTipoClienteVisualizacao('AMBOS');
        endif;

        return $this->useProdutoVariacaoQuery()
            ->addDescendingOrderByColumn(ProdutoVariacaoPeer::ESTOQUE_ATUAL . ' > 0')
            ->filterByIsMaster(true)
            ->filterByDisponivel($v)
            ->endUse();
    }

    /**
     * Filter by kits.
     *
     * @return $this
     * @throws PropelException
     */
    public function filterKits()
    {
        //busca os ids de planos associados a produtos(kits) disponiveis.
        $planos = ProdutoQuery::create()
            ->select(array('PlanoId'))
            ->filterByPlanoId(null, Criteria::NOT_EQUAL)
            ->orderByValor()
            ->filterByDisponivel(true)
            ->find()
            ->toArray();

        $planosNaoExibir = array();
        if ($planos):
            //se o cliente não estiver logado ou não possuir um plano contratado, só vamos exibir o "plano 1" (menor valor)
            if (!ClientePeer::isAuthenticad() || !($planoCliente = ClientePeer::getClienteLogado(true)->getPlano())):
                array_shift($planos);

                foreach ($planos as $arr):
                    $planosNaoExibir[] = $arr['PlanoId'];
                endforeach;
            else:
                //O cliente já possui um plano. Neste caso não vamos exibir planos menores que o plano que o cliente ja possui.
                foreach ($planos as $arr):
                    $objPlano = PlanoQuery::create()->findPk($arr['PlanoId']);

                    if ($objPlano && $objPlano->getValor() <= $planoCliente->getValor()):
                        $planosNaoExibir[] = $arr['PlanoId'];;
                    endif;
                endforeach;
            endif;
        endif;

        /*if ($planosNaoExibir) {
            $this->filterByPlanoId(null);
            $this->_or();
            $this->filterByPlanoId($planosNaoExibir, Criteria::NOT_IN);
        }*/

        return $this;
    }

    /**
     * Order by value.
     *
     * @param string $criteria
     * @return ModelCriteria
     * @throws PropelException
     */
    public function orderByValor($criteria = Criteria::ASC)
    {
        $field = "CASE WHEN " . ProdutoVariacaoPeer::VALOR_PROMOCIONAL . " " . Criteria::EQUAL . " 0"
            . " THEN " . ProdutoVariacaoPeer::VALOR_BASE
            . " ELSE " . ProdutoVariacaoPeer::VALOR_PROMOCIONAL
            . " END";
        $this->withColumn($field, 'VALOR');

        return $this->useProdutoVariacaoQuery()
            ->filterByIsMaster(true)
            ->withColumn($field, 'VALOR_VENDA')
            ->orderBy('VALOR_VENDA', $criteria)
            ->endUse();
    }

    /**
     * Filter by highlight.
     *
     * @param bool $destaque
     * @param null $comparison
     * @return ProdutoQuery
     */
    public function filterByDestaque($destaque = true, $comparison = null) {
        return parent::filterByDestaque($destaque, $comparison);
    }

    /**
     * @param bool $v
     * @return $this
     */
    public function filterByCategoriaDisponivel($v = true) {
        $v = (int) $v;
        $subQuery = "
                SELECT COUNT(1)
                FROM qp1_produto_categoria
                JOIN qp1_categoria ON qp1_produto_categoria.CATEGORIA_ID = qp1_categoria.ID
                WHERE qp1_produto.ID = qp1_produto_categoria.PRODUTO_ID
                AND qp1_categoria.DISPONIVEL = $v
                AND (" . CategoriaQuery::sqlCountParentIndisponivel() . ") = 0
            ";
        $this->where("0 <> ($subQuery)");

        return $this;

    }

    /**
     * @param null $marcaId
     * @param null $comparison
     * @return ProdutoQuery
     */
    public function filterByMarcaId($marcaId = null, $comparison = null) {
        return parent::filterByMarcaId(preg_replace('/[^0-9]/', '', $marcaId), $comparison);
    }
}
