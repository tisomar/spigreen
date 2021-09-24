<?php

use Monolog\Logger;

/**
 * Description of GerenciadorParticipacaoResultados
 *
 * @author André Garlini
 */
class GerenciadorParticipacaoResultados extends GerenciadorPontos
{
    
    public function __construct(\PropelPDO $con, Logger $logger)
    {
        parent::__construct($con, $logger);
    }
    
    /**
     *
     * @param ParticipacaoResultado $participacaoResultado
     * @throws Exception
     */
    public function geraPreview(ParticipacaoResultado $participacaoResultado)
    {
        if ($participacaoResultado->getStatus() != ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW) {
            throw new LogicException('Preview desta participação resultados já foi gerado.');
        }
        
        $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW);
        $participacaoResultado->setTipo(ParticipacaoResultado::TIPO_DESTAQUE);
        $participacaoResultado->save($this->con);
        
        try {
            $this->con->beginTransaction();
            $this->criaParticipacaoClientes($participacaoResultado);
            
            $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_PREVIEW);
            $participacaoResultado->save($this->con);
            
            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) {
                $this->con->rollBack();
            }
            throw $ex;
        }
    }
    
    /**
     *
     * @param ParticipacaoResultado $participacaoResultado
     * @throws Exception
     */
    public function confirmaParticipacaoResultado(ParticipacaoResultado $participacaoResultado)
    {
        if ($participacaoResultado->getStatus() != ParticipacaoResultado::STATUS_AGUARDANDO) {
            if (in_array($participacaoResultado->getStatus(), array(ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW, ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW, ParticipacaoResultado::STATUS_PREVIEW))) {
                throw new LogicException('Preview desta participação resultados ainda não já foi gerado.');
            } else {
                throw new LogicException('Esta participação resultados já foi gerada.');
            }
        }
        
        $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_PROCESSANDO);
        $participacaoResultado->setTipo(ParticipacaoResultado::TIPO_DESTAQUE);
        $participacaoResultado->save($this->con);
        
        $this->con->beginTransaction();
        try {
            //busca os registros de distribuicao_cliente gerados durante o preview e gera o extrato para cada cliente.
            $query = ParticipacaoResultadoClienteQuery::create()
                                        ->filterByParticipacaoResultado($participacaoResultado)
                                        ->orderById();
            
            foreach ($query->find($this->con) as $participacaoResultadoCliente) { /* @var $participacaoResultadoCliente ParticipacaoResultadoCliente */
                $extrato = $this->geraExtratoCliente($participacaoResultadoCliente);
            }
            
            $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_DISTRIBUIDO);
            $participacaoResultado->save($this->con);
            
            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) {
                $this->con->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * Retorna o total de clientes qualificados a participação dos resultados.
     *
     * @param null $minDate
     * @param null $maxDate
     * @return int
     * @throws Exception
     */
    public function getTotalClientesQualificadosParaParticipacaoResultados($minDate = null, $maxDate = null)
    {
        if ($minDate == null && $maxDate == null) :
            $minDate = new Datetime('first day of last month');
            $minDate->setTime(0, 0, 0);

            $maxDate = new DateTime('last day of last month');
            $maxDate->setTime(23, 59, 59);
        endif;

        $query = PedidoQuery::create()
            ->filterByCreatedAt(['min' => $minDate, 'max' => $maxDate])
            ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->usePedidoFormaPagamentoQuery()
                ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS, Criteria::NOT_EQUAL)
            ->endUse()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->addGroupByColumn(sprintf(
                'IFNULL(%s, %s)',
                PedidoPeer::HOTSITE_CLIENTE_ID,
                PedidoPeer::CLIENTE_ID
            ))
            ->having(
                sprintf('SUM(%s) >= ?', PedidoPeer::VALOR_PONTOS),
                1000,
                \PDO::PARAM_INT
            );

        return $query->count();
    }
    
    /**
     * Retorna o total de vendas de produtos que devem ser considerados na participação nos resultados.
     * ATENÇÃO: esta função retorna o total de vendas de todos os tempos. O controle de quanto já foi desctribuído
     * e o que falta distribuir deve ser feito pelo chamador.
     *
     * @return float
     */
    public function getTotalVendasParticipacaoResultados()
    {
        $query = PedidoItemQuery::create()
                        ->clearSelectColumns()
                        ->addAsColumn('total', sprintf('SUM(%s * %s)', PedidoItemPeer::QUANTIDADE, PedidoItemPeer::VALOR_UNITARIO))
                        
                        ->usePedidoQuery(null, Criteria::INNER_JOIN)
                            ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
                            ->usePedidoFormaPagamentoQuery(null, Criteria::INNER_JOIN)
                                ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO)
                            ->endUse()
                        ->endUse();
                
        $total = 0.0;
        if ($row = BasePeer::doSelect($query)->fetch()) {
            $total = (float)$row['total'];
        }
        
        return $total;
    }

    /**
     * Retorna o valor total de venda do mês anterior
     * @param null $minDate
     * @param null $maxDate
     * @return float
     * @throws PropelException
     */

    public function getTotalVendaBrutoDistribuicao($minDate = null, $maxDate = null)
    {
        if ($minDate == null && $maxDate == null) :
            $minDate = new Datetime('first day of last month');
            $minDate->setTime(0, 0, 0);

            $maxDate = new DateTime('last day of last month');
            $maxDate->setTime(23, 59, 59);
        endif;

        $total = PedidoQuery::create()
            ->select(['valorTotal'])
            ->withColumn(sprintf(
                'SUM(%s + %s - %s)',
                PedidoPeer::VALOR_ITENS,
                PedidoPeer::VALOR_ENTREGA,
                PedidoPeer::VALOR_CUPOM_DESCONTO
            ), 'valorTotal')
            ->filterByCreatedAt(['min' => $minDate, 'max' => $maxDate])
            ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->usePedidoFormaPagamentoQuery()
                ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS, Criteria::NOT_EQUAL)
            ->endUse()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->findOne();

        return (float)$total;
    }

    /**
     * Retorna o valor total de pontos das vendas do mês anterior
     * @param null $minDate
     * @param null $maxDate
     * @return float
     * @throws PropelException
     */

    public function getTotalPontosDistribuicao($minDate = null, $maxDate = null)
    {
        if ($minDate == null && $maxDate == null) :
            $minDate = new Datetime('first day of last month');
            $minDate->setTime(0, 0, 0);

            $maxDate = new DateTime('last day of last month');
            $maxDate->setTime(23, 59, 59);
        endif;

        $total = PedidoQuery::create()
            ->select(['TotalValor'])
            ->filterByCreatedAt(['min'=> $minDate, 'max' => $maxDate])
            ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->usePedidoItemQuery()
                ->withColumn(sprintf('SUM(qp1_pedido_item.VALOR_PONTOS_UNITARIO * qp1_pedido_item.QUANTIDADE)'), 'TotalValor')
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->findOne();

        return $total;
    }
    
    /**
     * Retorna o total de pontos restantes da ultima distribuição finalizada.
     *
     * @return float
     */
    protected function getTotalPontosRestantesDistribuicaoAnterior()
    {
        $minDate = new Datetime('first day of this month');
        $minDate->setTime(0, 0, 0);

        $maxDate = new DateTime('last day of this month');
        $maxDate->setTime(23, 59, 59);

        $query = ParticipacaoResultadoQuery::create()
                        ->select(array('TotalPontosDistribuidos'))
                        ->withColumn('SUM(TOTAL_PONTOS)', 'TotalPontosDistribuidos' )
                        ->filterByStatus(ParticipacaoResultado::STATUS_DISTRIBUIDO)
                        ->filterByTipo(ParticipacaoResultado::TIPO_DESTAQUE)
                        ->filterByData(['min' => $minDate, 'max' => $maxDate])
                        ->orderByData(Criteria::DESC);
        
        return (float)$query->findOne();
    }

    /**
     *
     * @param ParticipacaoResultado $participacaoResultado
     * @return void
     * @throws PropelException
     */
    protected function criaParticipacaoClientes(ParticipacaoResultado $participacaoResultado)
    {
        /**
         * Regra de negócio: SPIGREEN
         *
         * 1% da venda total mensal da spigreen.
         * O valor arrecadado será divido por todos aqueles que se qualificarem para
         * esta premiação (clientes com pontos igual ou superior a 1000 ne mês anterior).
         */
        
        //temos que pegar 1% do valor total de venda
        $totalPremiacao = round(($this->getTotalPontosDistribuicao() * 1) / 100, 2);

        $valorDistribuido = $this->getTotalPontosRestantesDistribuicaoAnterior();
        if($valorDistribuido > 0) :
            $totalPremiacao = $totalPremiacao - $valorDistribuido;
        endif;

        if ($totalPremiacao <= 0.0) {
            return;
        }

        //verifica os clientes que estão qualificados a participar da distribuicao.
        $clientesQualificados = array();
        $clientes = ClienteQuery::create()->orderById()->find($this->con);

        $pontosClientesSoma = 0;
        foreach ($clientes as $cliente) :
            if ($cliente->getPlano() != null && !$cliente->getPlano()->getPlanoClientePreferencial()) :
                $pontosPessoais = $this->getValorTotalPontosPedidosMesAnterior($cliente->getId());
                if ($pontosPessoais >= 1000) :
                    $pontosClientesSoma += $pontosPessoais;
                    $clientesQualificados[] = $cliente;
                endif;
            endif;
        endforeach;

        if (empty($clientesQualificados)) {
            //nenhum cliente qualificado.
            return;
        }

        $distruirPorCliente = ceil($totalPremiacao / count($clientesQualificados) * 100) / 100;
        $totalDist = 0;

        //cria os registros de participacao
        foreach ($clientesQualificados as $cliente) :
            // Regra antiga distribuindo por valor bruto de venda
            // $pontosPessoais = $this->getValorTotalPontosPedidosMesAnterior($cliente->getId());
            // $percentualPorCliente = ($pontosPessoais * 100) / $pontosClientesSoma;
            // $distruirPorCliente = $totalPremiacao * ( $percentualPorCliente / 100 );

            // Valor total distribuido 
            $totalDist += $distruirPorCliente;
            // percentual de cada cliente
            $percentualPorCliente = ($distruirPorCliente * 100) / $totalPremiacao;
           
            $participacaoResultadoCliente = new ParticipacaoResultadoCliente();
            $participacaoResultadoCliente->setParticipacaoResultado($participacaoResultado);
            $participacaoResultadoCliente->setCliente($cliente);
            $participacaoResultadoCliente->setTotalPontos($distruirPorCliente);
            $participacaoResultadoCliente->setData(new DateTime());
            $participacaoResultadoCliente->setPercentual($percentualPorCliente);
            $participacaoResultadoCliente->save($this->con);
        endforeach;

        //salva o que foi distribuido.
        $participacaoResultado->setTotalPontos($totalDist);
        $participacaoResultado->save($this->con);
    }
    
    /**
     *
     * @param ParticipacaoResultadoCliente $participacaoResultadoCliente
     * @return \Extrato|null
     */
    protected function geraExtratoCliente(ParticipacaoResultadoCliente $participacaoResultadoCliente)
    {
        if ($participacaoResultadoCliente->getTotalPontos() > 0) {
            $data = new DateTime();
            
            $extrato = new Extrato();
            $extrato->setClienteId($participacaoResultadoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_BONUS_DESTAQUE);
            $extrato->setPontos($participacaoResultadoCliente->getTotalPontos());
            $extrato->setOperacao('+');
            $extrato->setData($data);
            $extrato->setObservacao(sprintf("Bônus destaque %s.", $data->format('d/m/Y')));
            $extrato->setParticipacaoResultadoId($participacaoResultadoCliente->getParticipacaoResultadoId());
            $extrato->save($this->con);
            
            return $extrato;
        }
        
        return null;
    }

    public function getValorTotalPontosPedidosMesAnterior($clienteId)
    {
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('last day of last month');
        $end->setTime(23, 59, 59);

        $total = PedidoQuery::create()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->select(['valorTotalPontos'])
            ->withColumn('IFNULL(SUM(VALOR_PONTOS), 0)', 'valorTotalPontos')
            ->where(
                sprintf(
                    'IFNULL(%s, %s) = ?',
                    PedidoPeer::HOTSITE_CLIENTE_ID,
                    PedidoPeer::CLIENTE_ID
                ),
                $clienteId,
                \PDO::PARAM_INT
            )
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByCreatedAt(['min' => $start, 'max' => $end])
            ->findOne();

        return (float) $total;
    }
}
