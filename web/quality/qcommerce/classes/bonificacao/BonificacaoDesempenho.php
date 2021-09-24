<?php

use Monolog\Logger;

class BonificacaoDesempenho extends GerenciadorBonificacao implements BonificacaoParticipacaoInterface
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
            throw new LogicException('Preview de bônus desempenho já foi gerado.');
        }
        
        $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW);
        $participacaoResultado->setTipo(ParticipacaoResultado::TIPO_DESEMPENHO);
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
            ->withColumn(sprintf('SUM(%s)', PedidoPeer::VALOR_PONTOS), 'TotalValor')
            ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->filterByDataPagamentoPeriodo($minDate, $maxDate)
            ->findOne();
        return $total;
    }

    /**
     * Retorna o total de pontos restantes da ultima distribuição finalizada.
     *
     * @return float
    */

    protected function getTotalPontosRestantesDistribuicaoAnterior($planoCarreiraId)
    {
        $minDate = new Datetime('first day of this month');
        $minDate->setTime(0, 0, 0);

        $maxDate = new DateTime('last day of this month');
        $maxDate->setTime(23, 59, 59);

        $data = new Datetime('last month');
        $mes = $data->format('n');
        $ano = $data->format('Y');

        $query = ParticipacaoResultadoClienteQuery::create()
                        ->select(array('TotalPontosDistribuidos'))
                        ->withColumn(
                            sprintf('SUM(%s)', ParticipacaoResultadoClientePeer::TOTAL_PONTOS),
                            'TotalPontosDistribuidos'
                        )
                        ->useParticipacaoResultadoQuery()
                            ->filterByStatus(ParticipacaoResultado::STATUS_DISTRIBUIDO)
                            ->filterByTipo(ParticipacaoResultado::TIPO_DESEMPENHO)
                        ->endUse()
                        ->useClienteQuery()
                            ->usePlanoCarreiraHistoricoQuery()
                                ->filterByMes($mes)
                                ->filterByAno($ano)
                                ->filterByPlanoCarreiraId($planoCarreiraId)
                            ->endUse()
                        ->endUse()
                        ->filterByData(['min' => $minDate, 'max' => $maxDate]);
        
        $valor = (float)$query->findOne();

        return $valor;
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
         * 11% do total de pontos de recompra e vendas online mensal da spigreen (exeto kits).
         * O valor arrecadado será divido igualmente entre todos Esperaldas e superiores que se qualificarem para
         * esta premiação (clientes com pontos igual ou superior a 1000 ne mês anterior).
         */
        
        $totalPremiacao = $this->getTotalPontosDistribuicao();
        if ($totalPremiacao <= 0.0) {
            return;
        }

        $data = new Datetime('last month');
        $mes = $data->format('n');
        $ano = $data->format('Y');

        $planoCarreiraList = PlanoCarreiraQuery::create()
            ->filterByPercBonusDesempenho(null, Criteria::ISNOTNULL)
            ->find();
        
        $totalParticipacao = 0;

        foreach ($planoCarreiraList as $planoCarreira) :
            $valorDistribuir = $totalPremiacao * $planoCarreira->getPercBonusDesempenho() / 100;

            if ($valorDistribuir <= 0) :
                continue;
            endif;

            $clientesGraduacao = PlanoCarreiraHistoricoQuery::create()
                ->filterByMes($mes)
                ->filterByAno($ano)
                ->filterByPlanoCarreiraId($planoCarreira->getId())
                ->find();

            $clientes = [];

            foreach ($clientesGraduacao as $clienteHistorico) :

                // Pontos de volume de grupo
                $totalPontos = $clienteHistorico->getVolumeTotalGrupo();

                $start = new DateTime('first day of last month');
                $start->setTime(0, 0, 0);

                $end = new DateTime('last day of last month');
                $end->setTime(23, 59, 59);

                if (ClientePeer::getClienteAtivoMensal($clienteHistorico->getClienteId(), $start, $end)) :
                    $clientes[] = [
                        'clienteId' => $clienteHistorico->getClienteId(),
                        'pontos' => $totalPontos,
                        'percentualCliente' => $planoCarreira->getPercBonusDesempenho(),
                        'graduacao' => $planoCarreira->getGraduacao(),
                        'graduacaoNivel' => $planoCarreira->getNivel()
                    ];
                endif;

            endforeach;

            foreach ($clientes as $data) :
                $observacao = null;
                $percentualPorCliente = round(100 / count($clientes), 2);
                $valor = ceil($valorDistribuir / count($clientes) * 100) / 100;
                
                // Campanha Desempenho em dobro Mês de Maio  -- init --  
                // $maxGraducaoNivel = $this->getMaxGraduacao($data['clienteId']);
                // $graduacaoAtualNivel = $data['graduacaoNivel'];
                // if($graduacaoAtualNivel >= $maxGraducaoNivel) :
                //     $valor = $valor * 2;
                //     $observacao = ' bônus desempenho em dobro (Campanha mês de maio)';
                // endif;
                // Campanha Desempenho em dobro Mês de Maio -- final --

                $totalParticipacao += $valor;

                $participacaoResultadoCliente = new ParticipacaoResultadoCliente();
                $participacaoResultadoCliente->setParticipacaoResultado($participacaoResultado);
                $participacaoResultadoCliente->setClienteId($data['clienteId']);
                $participacaoResultadoCliente->setTotalPontos($valor);
                $participacaoResultadoCliente->setData(new DateTime());
                $participacaoResultadoCliente->setPercentual($percentualPorCliente);
                $participacaoResultadoCliente->setGraduacao($data['graduacao']);
                $participacaoResultadoCliente->setObservacao($observacao);
                $participacaoResultadoCliente->save($this->con);

                // var_dump("(Cliente: {$data['clienteId']}), (PontosGrupo:  {$data['pontos']}), (perceCliente: $percentualPorCliente), (valor:  $valor), {$data['graduacao']}");
            endforeach;
            //salva o que foi distribuido.
            $participacaoResultado->setTotalPontos($totalParticipacao);
            $participacaoResultado->save($this->con);
        endforeach;
    }

    /**
     *
     * @param ParticipacaoResultado $participacaoResultado
     * @throws Exception
    */
    public function distribuirBonus(ParticipacaoResultado $participacaoResultado) {

        if ($participacaoResultado->getStatus() != ParticipacaoResultado::STATUS_AGUARDANDO) {
            if (in_array($participacaoResultado->getStatus(), array(ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW, ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW, ParticipacaoResultado::STATUS_PREVIEW))) {
                throw new LogicException('Preview deste bônus desempenho ainda não foi gerado.');
            } else {
                throw new LogicException('Esta geração de bônus desempenho já foi gerada.');
            }
        }
        
        $participacaoResultado->setStatus(ParticipacaoResultado::STATUS_PROCESSANDO);
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
     *
     * @param ParticipacaoResultadoCliente $participacaoResultadoCliente
     * @return \Extrato|null
     */
    protected function geraExtratoCliente(ParticipacaoResultadoCliente $participacaoResultadoCliente)
    {
        if ($participacaoResultadoCliente->getTotalPontos() > 0) {
            $data = new DateTime();
            $lastMonth = new Datetime('first day of last month');
            $graduacao = PlanoCarreiraHistoricoQuery::create()
                ->filterByClienteId($participacaoResultadoCliente->getClienteId())
                ->filterByMes($lastMonth->format('n'))
                ->filterByAno($lastMonth->format('Y'))
                ->findOne();
            
            $extrato = new Extrato();
            $extrato->setClienteId($participacaoResultadoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_BONUS_DESEMPENHO);
            $extrato->setPontos($participacaoResultadoCliente->getTotalPontos());
            $extrato->setOperacao('+');
            $extrato->setData($data);
            $extrato->setObservacao(
                sprintf(
                    "Bônus desempenho %s. Graduação: %s",
                    $data->format('d/m/Y'),
                    !empty($graduacao) ? $graduacao->getPlanoCarreira()->getGraduacao() . $participacaoResultadoCliente->getObservacao() : ''
                )
            );
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
            ->usePedidoFormaPagamentoQuery()
                ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS, Criteria::NOT_EQUAL)
                ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO)
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

     /**
    * @param  $clienteId
    * @throws PropelException
    */
    public function getMaxGraduacao($clienteId) {
        $cliente = ClienteQuery::create()
        ->filterById($clienteId)
        ->findOne();
        
        $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);
        $maiorGraduacaoDesc = $gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao();

        $maxGraduacaoNivel = PlanoCarreiraQuery::create()
            ->filterByGraduacao($maiorGraduacaoDesc, Criteria::EQUAL)
            ->findOne();

        return $maxGraduacaoNivel->getNivel();
    }
}