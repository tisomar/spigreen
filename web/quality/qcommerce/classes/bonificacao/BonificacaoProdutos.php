<?php

use Monolog\Logger;

class BonificacaoProdutos extends GerenciadorBonificacao
{
    public function __construct(\PropelPDO $con, Logger $logger)
    {
        parent::__construct($con, $logger);
    }

    /**
     *
     * @param BonificacaoProdutos $participacaoResultado
     * @throws Exception
     */
    public function geraPreview(DistribuicaoBonusProdutos $distribuicaoBonusProdutos)
    {

        if ($distribuicaoBonusProdutos->getStatus() != Distribuicao::STATUS_AGUARDANDO_PREVIEW) {
            throw new LogicException('Preview de bônus desempenho já foi gerado.');
        }

        $distribuicaoBonusProdutos->setStatus(Distribuicao::STATUS_PROCESSANDO_PREVIEW);
        $distribuicaoBonusProdutos->save($this->con);
        
        try {
            $this->con->beginTransaction();
            $this->geraBonificacao($distribuicaoBonusProdutos);
            
            $distribuicaoBonusProdutos->setStatus(ParticipacaoResultado::STATUS_PREVIEW);
            $distribuicaoBonusProdutos->save($this->con);
            
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
     * @param DistribuicaoBonusProdutos $participacaoResultado
     * @return void
     * @throws PropelException
     */
    protected function geraBonificacao(DistribuicaoBonusProdutos $distribuicaoBonusProdutos)
    {

        /**
        * Trata-se de Prêmios entregues para os DIS que se graduarem no mês anterior a:

        * Master, Supervisor, Gerente ou Executivo.

        * Regras para estar qualificado(a):
        * No mês da qualificação, ter se ativado com 80 pontos até o dia 10 ou ter feito a adesão (kit primeira compra: Bronze, Prata ou Ouro).
        * No mês de qualificação, ter atingido uma pontuação pessoal de, no mínimo, 150 pontos.

        * O D.I.S deverá estar em crescimento, exemplo:
        * Ex 1:  No mês de Maio, o D.I.S que se qualificou a Supervisor pela primeira vez, receberá o prêmio.

        * Ex 2: No mês de Junho, se mantém com a mesma qualificação “Supervisor”, 
        * receberá o prêmio, porém se ele não conseguiu se manter com a mesma qualificação e atingiu o nível de Master, ele não terá direito a este bônus.

        * Mês de Julho, se mantém com a mesma qualificação “Supervisor”, ele não receberá o bônus, porém, se ele atingir o nível de Gerente, ele receberá como Gerente.

        * O D.I.S irá receber o prêmio durante 2 meses com a mesma graduação, porém, a partir do 3º mês precisará atingir um nível acima.

        * Uma vez atingido o nível de Rubi e acima, ele não terá mais o direito a receber este bônus produtos.
        */


        /**
         * Metroria de bônus produtos, agora temos o Bônus produtos acumulado, que se trata da graduacao no mês de cadastro + o próximo Mês
         * 
         * Exemplos: O DIS se cadastrou no mes de Fevereiro, e no mês de março, ele se qualificou a nível de gerente, ele irá receber o bônus produtos de Master + supervisor + gerente = totalizando R$ 1.413,40 em produtos.
         * 
         * Graduações superiores a executivo agora recebem o bônus, mas o valor a ser pago vai ser o teto usado para a graduacao de EXECUTIVO para todas as graduaçoes acima.
         */
       

        $data = new Datetime('first day of last month');
        $mes = $data->format('n');
        $ano = $data->format('Y');

        $today = new DateTime();
        
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('last day of last month');
        $end->setTime(23, 59, 59);

        $planoCarreiraList = PlanoCarreiraQuery::create()
            ->filterByNivel(1, Criteria::GREATER_THAN)
            ->find();
        
        // PERCORRENDO OS PLANOS DE CARREIRA: "MASTER - SUPERVISOR - GERENTE - EXECUTIVO"
        $clientes = [];
        $count = 0;

        foreach ($planoCarreiraList as $planoCarreira) :

            // PEGANDO TODOS OS CLIENTE QUE POSSUEM GRADUAÇÔES NO MES ANTERIOR
            $clientesGraduacao = PlanoCarreiraHistoricoQuery::create()
                ->filterByMes($mes)
                ->filterByAno($ano)
                ->filterByPlanoCarreiraId($planoCarreira->getId())
                ->find();

            foreach ($clientesGraduacao as $clienteHistorico) :
                $graduacaoAtual = $this->getGraduacao(0, $clienteHistorico->getClienteId());

                if($graduacaoAtual['graduacaoNivel'] > 1) :
                    $totalPontosNos10PrimeirosDias = $this->getTotalPontosPeriodo($clienteHistorico->getClienteId());
                    $totalPontosPessoaisMes = $clienteHistorico->getTotalPontosPessoais();
                    [$isCompraKit, $kit] = $this->getIsCompraKit($clienteHistorico->getClienteId());

                    if($totalPontosPessoaisMes >= 150 && ($totalPontosNos10PrimeirosDias >= ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal() || $isCompraKit)) :
                        $consultaDistribuidoExistente = ExtratoBonusProdutosQuery::create()
                            ->filterByClienteId($clienteHistorico->getClienteId())
                            ->filterByPlanoCarreiraId($graduacaoAtual['graduacaoCarreiraId'])
                            ->filterByOperacao('-')    
                            ->find()
                            ->toArray();

                        [ $bonusAcumuladosCliente, $isBonusAcumulados ] = $this->getBonusAcumulado($clienteHistorico->getClienteId(), $graduacaoAtual['graduacaoCarreiraId']);

                        if($isBonusAcumulados) :
                            $count ++;
                            $clientes[] = [
                                'clienteId' => $clienteHistorico->getClienteId(),
                                'graduacaoBonificacaoAcumulada' => $bonusAcumuladosCliente,
                                'graduacaoBonificacao' => null,
                                'isBonusAcumulados' => true,
                                'kitCompra' => $kit,
                                'graduacaoAtual' => $graduacaoAtual['graduacaoDesc']
                            ];
                        elseif(count($consultaDistribuidoExistente) > 0) :
                            // Comparando se o bonus ja foi distribuido
                            $chechAptoLastMonth = ExtratoBonusProdutosQuery::create()
                                ->filterByData(['min' => $start , 'max' => $end])
                                ->filterByOperacao('-')  
                                ->filterByClienteId($clienteHistorico->getClienteId())
                                ->find();

                            $countSameGraduacao = count($consultaDistribuidoExistente);
                            $planoCarreiraBonus = $graduacaoAtual['graduacaoCarreiraId'];
                            $maxGraduacaoId = $this->getMaxGraduacaoIdNoExtrato($clienteHistorico->getClienteId());
                            
                            if($countSameGraduacao < 2 && count($chechAptoLastMonth) > 0 && $planoCarreiraBonus >= $maxGraduacaoId) :
                                $count ++;
                                $clientes[] = [
                                    'clienteId' => $clienteHistorico->getClienteId(),
                                    'graduacaoBonificacao' => $graduacaoAtual,
                                    'graduacaoBonificacaoAcumulada' => null,
                                    'isBonusAcumulados' => $isBonusAcumulados,
                                    'kitCompra' => $kit,
                                    'graduacaoAtual' => $graduacaoAtual['graduacaoDesc']
                                ];
                            endif;
                        else:
                            $maxGraduacaoHistorico = $this->getMaxGraduacaoNivel($clienteHistorico->getClienteId());
                            $planoCarreiraBonusNivel = $graduacaoAtual['graduacaoNivel'];

                            if( $planoCarreiraBonusNivel >= $maxGraduacaoHistorico || $maxGraduacaoHistorico > 5 ) :
                                $count ++;
                                $clientes[] = [
                                    'clienteId' => $clienteHistorico->getClienteId(),
                                    'graduacaoBonificacao' => $graduacaoAtual,
                                    'graduacaoBonificacaoAcumulada' => null,
                                    'isBonusAcumulados' => $isBonusAcumulados,
                                    'kitCompra' => $kit,
                                    'graduacaoAtual' => $graduacaoAtual['graduacaoDesc']
                                ];
                            endif;
                        endif;
                    endif;
                endif;
            endforeach;
        endforeach;

        foreach ($clientes as $data) :
            $date = new DateTime(); 
            $date->add(new DateInterval('P30D'));
            $date->setTime(23, 59, 59);
            $dateExpires = $date->format('Y-m-d H:i:s');

            $graduacaoBonificacao = '';
            $graduacoesList = [];
            $valorTotalBonificacao = 0;
            if($data['graduacaoBonificacao'] !== null) :
                 // $produtosId = $data['graduacaoBonificacao']['graduacaoNivel'];
                $planoCarreiraId = $data['graduacaoBonificacao']['graduacaoCarreiraId'];
                $graduacaoBonificacao .= $data['graduacaoBonificacao']['graduacao'];
                $graduacoesList[] = $data['graduacaoBonificacao']['graduacaoCarreiraId'];
            endif;

            if( $data['graduacaoBonificacaoAcumulada'] !== null ) :
                $planoCarreiraId = 0;
                foreach( $data['graduacaoBonificacaoAcumulada'] as $k => $graduacao ) :
                    $countGraduacao = count($data['graduacaoBonificacaoAcumulada']) - 1;
                    $separador = $k == $countGraduacao ? '' : ', ';
                    $graduacaoBonificacao .= $graduacao['graduacao'] . $separador;
                    $planoCarreiraId = $graduacao['graduacaoCarreiraId'];
                    $graduacoesList[] = $graduacao['graduacaoCarreiraId'];
                endforeach;
            endif;

            $totalBonificacao = PlanoCarreiraQuery::create()
            ->select(['total_bonificacao'])
            ->withColumn('SUM(valor_premio_bonus_produtos)', 'total_bonificacao')
            ->filterById($graduacoesList, Criteria::IN)
            ->findOne();

            $clienteID = $data['clienteId'];
            $isBonusAcumulados = $data['isBonusAcumulados'];
            $observacao = $isBonusAcumulados ? "Bônus produtos acumulados disponível para as graduações: {$graduacaoBonificacao}" : "Bônus produtos disponível para a graduação: {$graduacaoBonificacao}";
            $participacaoResultadoCliente = new ExtratoBonusProdutos();
            $participacaoResultadoCliente->setClienteId($clienteID);
            $participacaoResultadoCliente->setDistribuicaoId($distribuicaoBonusProdutos->getId());
            $participacaoResultadoCliente->setPlanoCarreiraId($planoCarreiraId);
            $participacaoResultadoCliente->setIsBonusAcumulados($isBonusAcumulados);
            $participacaoResultadoCliente->setValorTotalBonificacao($totalBonificacao);
            $participacaoResultadoCliente->setData($today);
            $participacaoResultadoCliente->setGraduacao($data['graduacaoAtual']);
            $participacaoResultadoCliente->setOperacao('+');
            $participacaoResultadoCliente->setObservacao($observacao);
            $participacaoResultadoCliente->save($this->con);

        endforeach;

        $distribuicaoBonusProdutos->setTotalClientes($count);
        $distribuicaoBonusProdutos->save($this->con);
    }

    /**
     *
     * @param DistribuicaoBonusProdutos $participacaoResultado
     * @throws Exception
    */
    public function distribuirBonus(DistribuicaoBonusProdutos $distribuicaoBonusProdutos) {

        if ($distribuicaoBonusProdutos->getStatus() != Distribuicao::STATUS_AGUARDANDO) {
            if (in_array($participacaoResultado->getStatus(), array(Distribuicao::STATUS_AGUARDANDO_PREVIEW, Distribuicao::STATUS_PROCESSANDO_PREVIEW, Distribuicao::STATUS_PREVIEW))) {
                throw new LogicException('Preview deste bônus desempenho ainda não foi gerado.');
            } else {
                throw new LogicException('Esta geração de bônus desempenho já foi gerada.');
            }
        }
        
        
        $distribuicaoBonusProdutos->setStatus(Distribuicao::STATUS_PROCESSANDO);
        $distribuicaoBonusProdutos->save($this->con);
        
        $this->con->beginTransaction();
        try {
            //busca os registros de distribuicao_cliente gerados durante o preview e gera o extrato para cada cliente.
            $query = ExtratoBonusProdutos::create()
                                        ->filterByDistribuicaoId($distribuicaoBonusProdutos->getId())
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

    public function getGraduacao($subMes, $clienteID) {
        $date = new DateTime('first day of last month');
        $date->setTime(0, 0, 0)->modify("-$subMes month");

        $cliente = ClienteQuery::create()
            ->filterById($clienteID)
            ->findOne();

        $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);
        $graduacao = ($gerenciador->getQualificacaoMesHistorico($date->format('m'), $date->format('Y'))) != '' ? $gerenciador->getQualificacaoMesHistorico($date->format('m'), $date->format('Y')): 'Sem graduação' ;
        
        $graduacaoDesc        = $graduacao != 'Sem graduação' ? $graduacao->getPlanoCarreira()->getGraduacao() : 'Sem graduação';
        $graduacaoCarreiraId  = $graduacao != 'Sem graduação' ? $graduacao->getPlanoCarreiraId() : 0;
        $graduacaoNivel       = $graduacao != 'Sem graduação' ? $graduacao->getPlanoCarreira()->getNivel() : 0;

        if($graduacaoNivel > 5) :
            return ['graduacao' => 'Executivo', 'graduacaoCarreiraId' => 4, 'graduacaoNivel' => 5, 'cliente' => $clienteID, 'graduacaoDesc' => $graduacaoDesc];
        endif;

        return ['graduacao' => $graduacaoDesc, 'graduacaoCarreiraId' => $graduacaoCarreiraId, 'graduacaoNivel' => $graduacaoNivel, 'cliente' => $clienteID, 'graduacaoDesc' => $graduacaoDesc];
    }

    public function getBonusAcumulado($clienteID, $graduacaoAtual) {

        $clienteCadastro = ClienteQuery::create()->findPk($clienteID)->getCreatedAt();
        $dateCadastro = new DateTime($clienteCadastro);
        $dateAgora = new DateTime();

        $mesesCadastroDiff = date_diff($dateCadastro, $dateAgora);

        $anosCadastro = $mesesCadastroDiff->format('%Y');
        $mesesCadastro = $mesesCadastroDiff->format('%m');

        $planoCarreira = PlanoCarreiraQuery::create()
            ->filterById($graduacaoAtual, Criteria::LESS_EQUAL)
            ->filterById(19, Criteria::NOT_EQUAL)->find();

        if(count($planoCarreira) == 1 && $planoCarreira[0]->getId() == 1):
            return [null, false];
        endif;

        if( $anosCadastro == 0 && $mesesCadastro < 2 && count($planoCarreira) > 0) :

            // confere se já resgatou os dois meses de bonificacao na graduacao de executivo
            $consultaDistribuidoExecutivo = ExtratoBonusProdutosQuery::create()
            ->filterByClienteId($clienteID)
            ->filterByPlanoCarreiraId(4)
            ->filterByOperacao('-')    
            ->find()
            ->count();

            if($consultaDistribuidoExecutivo < 2) :

                $graduacaoRecebeBonus = [];
                foreach($planoCarreira as $graduacoes) :

                    $consultaDistribuidoExistentePorGraduacao = ExtratoBonusProdutosQuery::create()
                        ->filterByClienteId($clienteID)
                        ->filterByPlanoCarreiraId($graduacoes->getId())
                        ->filterByOperacao('-')    
                        ->find()
                        ->count();

                    if($consultaDistribuidoExistentePorGraduacao <= 1):
                        if($graduacoes->getNivel() > 5) : // a graduacao maximo de bonificação é a de EXECUTIVO, e,tão graduações acima recebem bonificação da graduacao de executivo
                            continue;
                        else:
                            $graduacaoRecebeBonus[] = 
                                [
                                    'graduacao' => $graduacoes->getGraduacao(),
                                    'graduacaoCarreiraId' => $graduacoes->getId(),
                                    'graduacaoNivel' => $graduacoes->getNivel(),
                                    'cliente' => $clienteID
                                ];
                        endif;
                    endif;
                endforeach;

                return [$graduacaoRecebeBonus, true];
            endif;
        endif;
        
        return [null, false];
    }

    public function getTotalPontosPeriodo($clienteID) {

        // REGRA PROVISÓRIA PEGANDO A PONTUACAO TOTAL DURANTE O MES INTEIRO (REGRA CORRETA PEGANDO NO MES DA GERACAO)
        $inicio = new DateTime('first day of last month');
        $inicio->format('d/m/Y');
        $inicio->setTime(00, 00, 00, 000000);
        
        // REGRA ORIGINAL PEGANDO SOMENTE DOS 10 PRIMEIROS DIAS DO MÊS NO MES DA GERACAO PREVIEW
        $fim = new DateTime('first day of last month');
        $fim->format('d/m/Y');
        $fim->setTime(23, 59, 59, 999999);
        $fim = $fim->modify('+9 days');

        return PedidoPeer::getPontosPedidosPeriodo($clienteID, $inicio, $fim);
    }

    public function getIsCompraKit($clienteId) {
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('last day of last month');
        $end->setTime(23, 59, 59, 99999);

        $cliente = ClienteQuery::create()
            ->filterById($clienteId)
            ->filterByVago(false)
            ->filterByStatus(1)
            ->findOne();

        if (empty($cliente)) :
            return false;
        endif;

        $plano = $cliente->getPlano();

        if(!$plano) {
            return false;
        };

        $ultimaCompraPlano = PedidoStatusHistoricoQuery::create()
            ->filterByPedidoStatusId(1)
            ->filterByIsConcluido(1)
            ->filterByUpdatedAt([
                'min' => $start,
                'max' => $end
            ])
            ->usePedidoQuery()
                ->filterByClienteId($clienteId)
                ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                ->usePedidoItemQuery()
                    ->useProdutoVariacaoQuery()
                        ->useProdutoQuery()
                            ->filterByPlanoId($plano->getId())
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->findOne();

        if (!is_null($ultimaCompraPlano)) :

            $kitCompra = ''; 
            foreach($ultimaCompraPlano->getPedido()->getPedidoItems() as $p) {
                $kitCompra = $p->getProdutoVariacao()->getProduto()->getNome();
            }

            return [true, $kitCompra];
        endif;

        if ($plano->getId() == 9):
            $startPrev = (clone $start)->modify('first day of previous month');
            $endPrev = (clone $end)->modify('last day of previous month');

            $ultimaCompraPlano = PedidoStatusHistoricoQuery::create()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
                ->filterByUpdatedAt([
                    'min' => $startPrev,
                    'max' => $endPrev
                ])
                ->usePedidoQuery()
                    ->filterByClienteId($clienteId)
                    ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                    ->usePedidoItemQuery()
                        ->useProdutoVariacaoQuery()
                            ->useProdutoQuery()
                                ->filterById(149)
                                ->filterByPlanoId($plano->getId())
                            ->endUse()
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->findOne();

            if (!is_null($ultimaCompraPlano)):

                $kitCompra = ''; 
                foreach($ultimaCompraPlano->getPedido()->getPedidoItems() as $p) :
                    $kitCompra = $p->getProdutoVariacao()->getProduto()->getNome();
                endforeach;
    
                return [true, $kitCompra];
            endif;
        endif;

        return false;
    }

    public function getMaxGraduacaoIdNoExtrato($clienteId) {
        return ExtratoBonusProdutosQuery::create()
        ->withColumn('MAX(plano_carreira_id)', 'maxGraduacao')
        ->filterByClienteId($clienteId)
        ->filterByOperacao('-')  
        ->select(array('maxGraduacao'))
        ->findOne();
    }

    public function getMaxGraduacaoNivel($clienteId) {
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