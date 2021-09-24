<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_PEDIDO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.p
 *
 * @package    propel.generator.qcommerce
 */
class PedidoQuery extends BasePedidoQuery
{

    public function filterByClienteNome($v)
    {

        $this->useClienteQuery()
            ->filterByNome('%' . $v . '%')
            ->_or()
            ->filterByNomeFantasia('%' . $v . '%')
            ->_or()
            ->filterByRazaoSocial('%' . $v . '%')
            ->endUse();

        return $this;
    }

    public function filterByClienteCnpj($v)
    {

        return $this->useClienteQuery()
            ->filterByCnpj('%' . $v . '%')
            ->endUse();
    }

    public function filterByClienteCpf($v)
    {

        return $this->useClienteQuery()
            ->filterByCpf('%' . $v . '%')
            ->endUse();
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

    public function filterByStatusHistorico($v)
    {

        if (array_key_exists($v, PedidoPeer::getStatusList())) {

            return $this->filterByStatus($v);
        } else {
            $subQuery = '(SELECT PEDIDO_STATUS_ID FROM qp1_pedido_status_historico
                        WHERE PEDIDO_ID = ID
                        ORDER BY PEDIDO_STATUS_ID DESC
                        LIMIT 1)';

            return $this->add('x', $subQuery . ' = ' . $v, Criteria::CUSTOM)->filterByStatus(PedidoPeer::STATUS_ANDAMENTO);
        }
    }

    public function filterByTipoVenda($v)
    {
        if ($v == 'pontos') {
            return $this->usePedidoFormaPagamentoQuery()->filterByFormaPagamento('PONTOS', Criteria::EQUAL)->endUse();
        } else {
            return $this->usePedidoFormaPagamentoQuery()->filterByFormaPagamento('PONTOS', Criteria::NOT_EQUAL)->endUse();
        }
    }

    /**
     * @param bool $mesmoMes
     * @return PedidoQuery
     */
    public function filterByPagamentoConfirmado($mesmoMes = true) {
        return $this
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
                ->_if($mesmoMes)
                    ->where(sprintf(
                        'MONTH(%s) = MONTH(%s)',
                        PedidoPeer::CREATED_AT,
                        PedidoStatusHistoricoPeer::UPDATED_AT
                    ))
                ->_endif()
            ->endUse();
    }

    public function filterByDataPagamentoPeriodo($dataInicio, $dataFim)
    {
        return $this
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
                ->filterByUpdatedAt(['min' => $dataInicio, 'max' => $dataFim])
            ->endUse();
    }

    public function filterByDataPagamento($data, $criteria)
    {
        return $this
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
                ->filterByUpdatedAt($data, $criteria)
            ->endUse();
    }

    public function filterByFilialDistribuicao($data) {

        if($data != '0') :
            if($data == 'retirada_loja_MT') {
                return $this
                    ->filterByFrete('retirada_loja', Criteria::EQUAL)
                    ->filterByCentroDistribuicaoId('3', Criteria::EQUAL);
            }

            if($data == 'retirada_loja_GO') {
                return $this
                    ->filterByFrete('retirada_loja', Criteria::EQUAL)
                    ->filterByCentroDistribuicaoId(2, Criteria::EQUAL);
            }
            
            if($data == 'retirada_loja_ES') :
                return $this
                ->filterByFrete('retirada_loja', Criteria::EQUAL)
                ->filterByCentroDistribuicaoId('1', Criteria::EQUAL);
            else:
                return $this
                    ->filterByCentroDistribuicaoId($data, Criteria::EQUAL)
                    ->filterByFrete('retirada_loja', Criteria::NOT_EQUAL);
            endif;
        endif;
    }
}
