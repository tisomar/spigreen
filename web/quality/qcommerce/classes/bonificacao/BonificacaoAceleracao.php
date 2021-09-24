<?php

use Monolog\Logger;

class BonificacaoAceleracao extends GerenciadorBonificacao implements BonificacaoParticipacaoInterface
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
        $participacaoResultado->setTipo(ParticipacaoResultado::TIPO_ACELERACAO);
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
     * @return void
     * @throws PropelException
     */
    protected function criaParticipacaoClientes(ParticipacaoResultado $participacaoResultado)
    {
        /**
         * Regra de negócio: SPIGREEN
         * 
         * PRÊMIOS ACELERAÇÃO
         * Prêmios pago para D.I.S que se qualificarem nos primeiros 7 meses (mês de cadastro + 6 meses) com as seguintes
         * graduações: 
         * 
         * RUBI
         * ESMERALDA
         * DIAMANTE
         * DUPLO DIAMANTE
         * 
         * O prêmio tem 2 (dois) períodos de qualificação: 
         * 
         * 1º Período: Mês de cadastro + 3 (três) próximos meses. 
         * 2º Período: do 4º ao 6º mês. 
         * 
         * - Em cada período, qual foi a maior graduação que o D.I.S atingiu? 
         * 
         * Bonus acumulativo entre os períodos ex:
         * DIS graduacao como rubi no primeiro mes recebe R$ 2.000,00
         * No próximo mes se gradua como Esmeralda então recebe R$ 1.000,00 ou seja Valor de premiação da graduação + o valor que recebeu 
         * na graduação passada
         */

        $data = new Datetime('last month');
        $mes = $data->format('n');
        $ano = $data->format('Y');

        $totalParticipacao = 0;

        // PERCORRENDO OS PLANOS DE CARREIRA: "RUBI e SUPERIORES"
        $planoCarreiraList = PlanoCarreiraQuery::create()
            ->filterByNivel(6, Criteria::GREATER_EQUAL)
            ->find();

        $clientes = [];
        $count = 0;
        $totalPontosGraduacao = 0;

        foreach ($planoCarreiraList as $planoCarreira) :

            $clientesGraduacao = PlanoCarreiraHistoricoQuery::create()
                ->filterByMes($mes)
                ->filterByAno($ano)
                ->filterByPlanoCarreiraId($planoCarreira->getId())
                ->find();

            foreach ($clientesGraduacao as $clienteHistorico) :

                $graduacaoHistoricoBonificacao = $this->getGraduacaoList($clienteHistorico->getClienteId(), $planoCarreira->getId(), $mes, $ano);
                
                if( $graduacaoHistoricoBonificacao['dadosCliente'] !== null ) :
                    
                    foreach($graduacaoHistoricoBonificacao as $historicoDistribuicao) :
                        $clientes[] = [
                            'clienteId' => $historicoDistribuicao['clienteId'],
                            'pontos' => $historicoDistribuicao['valorDistribuicao'],
                            'graduacao' => $clienteHistorico->getPlanoCarreira()->getGraduacao(),
                            'periodo' => $historicoDistribuicao['periodo'],
                            'graduacaoId' => $historicoDistribuicao['planoCarreiraId'],
                            'tempoCadastro' => $historicoDistribuicao['qtdMesesCadastrado'],
                        ];
                    endforeach;
                endif;
            endforeach;
        endforeach;
      
        if( !empty($clientes) ) :
            foreach ($clientes as $data) :
                $totalParticipacao += $data['pontos'];

                $participacaoResultadoCliente = new ParticipacaoResultadoCliente();
                $participacaoResultadoCliente->setParticipacaoResultado($participacaoResultado);
                $participacaoResultadoCliente->setClienteId($data['clienteId']);
                $participacaoResultadoCliente->setTotalPontos($data['pontos']);
                $participacaoResultadoCliente->setData(new DateTime());
                $participacaoResultadoCliente->setGraduacao($data['graduacao']);
                $participacaoResultadoCliente->setObservacao($data['periodo']);
                $participacaoResultadoCliente->save($this->con);
            endforeach;

            //salva o que foi distribuido.
            $participacaoResultado->setTotalPontos($totalParticipacao);
            $participacaoResultado->save($this->con);
        endif;
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
            $extrato->setTipo(Extrato::TIPO_BONUS_ACELERACAO);
            $extrato->setPontos($participacaoResultadoCliente->getTotalPontos());
            $extrato->setOperacao('+');
            $extrato->setPlanoCarreiraId($graduacao->getPlanoCarreira()->getId());
            $extrato->setData($data);
            $extrato->setObservacao(
                sprintf(
                    "Bônus aceleração %s. Graduação: %s",
                    $data->format('d/m/Y'),
                    !empty($graduacao) 
                    ? $graduacao->getPlanoCarreira()->getGraduacao() . ' - ' . $participacaoResultadoCliente->getObservacao()
                    : ''
                )
            );
            $extrato->setParticipacaoResultadoId($participacaoResultadoCliente->getParticipacaoResultadoId());
            $extrato->save($this->con);
            return $extrato;
        }
        
        return null;
    }

    public function getGraduacaoList($clientId, $planoCarreiraId, $mes, $ano) {

        $fim = new DateTime('last day of last month');

        $dataComparaCadastro = new DateTime('last day of last month');
        $dataComparaCadastro = $dataComparaCadastro->format('Y-m-d H:i:s');

        $dataCompare = date('Y-m-d H:i:s');

        $cliente = ClienteQuery::create()->filterById($clientId)->findOne();
        
        $inicioDivulgaçãoBonus = date('2020-08-01 00:00:00');

        // CASO O DATA DE REGISTRO DO DIS FOR ANTERIOR A DATA DE DIVULGACAO DESTA BONIFICACAO 
        // OS CLIENTE ANTEIGOS TBM PODEM PARTICIPAR FICANDO DENTRO DOS PERIODOS DA BONIFICACAO 
        if($cliente->getCreatedAt() < $inicioDivulgaçãoBonus) :
            $comparaMesesCadastro = $this->dateDifference($inicioDivulgaçãoBonus, $dataCompare);
        else:
            $comparaMesesCadastro = $this->dateDifference($cliente->getCreatedAt(), $dataCompare);
        endif;

        $valoresBonificacao = $this->getValorDistruicaoGraduacao($planoCarreiraId);

        if($comparaMesesCadastro['ano'] == 0 && $comparaMesesCadastro['mes'] < 7 ) :

            $mes = $comparaMesesCadastro['mes'];

            $inicio = new DateTime('first day of last month');
            $inicio->modify("-$mes months");

            if( $comparaMesesCadastro['mes'] < 4) :
                $dadosCliente = $this->getValorDitruicao($clientId, $inicio, $fim, $valoresBonificacao['valorPrimeiroPeriodo'], 'Primeiro período', $planoCarreiraId, $comparaMesesCadastro);
                return ['dadosCliente' => $dadosCliente];
            else: 
                $dadosCliente = $this->getValorDitruicao($clientId, $inicio, $fim, $valoresBonificacao['valorSegundoPeriodo'], 'Segundo período', $planoCarreiraId, $comparaMesesCadastro);
                return ['dadosCliente' => $dadosCliente];
            endif;
        endif;

        return null;
    }

    /** 
    * Retorna o maior id da graduacao onde o cliente ja recebeu a bonificacao aceleracao
    * @param null $clienteid
    * @param null $inicio
    * @param null $fim
    * @return object
    * @throws PropelException
    */
    public function getMaxGraduacaoBonusDistruidoId($clienteid, $periodo) {

        $query = ExtratoQuery::create()
        ->withColumn('MAX(plano_carreira_id)', 'maxGraduacao')
        ->filterByTipo(Extrato::TIPO_BONUS_ACELERACAO, Criteria::EQUAL)
        ->filterByClienteId($clienteid)
        ->filterByObservacao('%' . $periodo . '%', Criteria::LIKE)
        ->select(array('maxGraduacao'))
        ->find()
        ->toArray()[0];

        return $query;
    }

     /** 
    * Retorna o valor recebido pelo cliente em um periodo
    * @param null $clienteid
    * @param null $inicio
    * @param null $fim
    * @return object
    * @throws PropelException
    */
    public function getTotalRecebidoPeriodo($clienteid, $periodo) {

        $query = ExtratoQuery::create()
        ->withColumn('SUM(PONTOS)', 'PONTOS')
        ->filterByTipo(Extrato::TIPO_BONUS_ACELERACAO, Criteria::EQUAL)
        ->filterByClienteId($clienteid)
        ->filterByObservacao('%' . $periodo . '%', Criteria::LIKE)
        ->filterByOperacao('+')  
        ->select(array('PONTOS'))
        ->findOne();

        return $query;
    }

    /** 
    * Retorna um array contendo os dados do cliente para a distruicao do bonus
    * @param null $clienteid
    * @param null $inicio
    * @param null $fim
    * @param null $valor
    * @param null $periodo
    * @param null $planoCarreiraId
    * @return array
    * @throws PropelException
    */
    function getValorDitruicao($clientId, $inicio, $fim, $valor, $periodo, $planoCarreiraId, $comparaMesesCadastro) {

        $maxGraduacaoBonusAceleracao = $this->getMaxGraduacaoBonusDistruidoId($clientId, $periodo);
        
        if($maxGraduacaoBonusAceleracao != null) :

            if($maxGraduacaoBonusAceleracao >= $planoCarreiraId) :
                return null;
            endif;
            
            $valorRecebido = $this->getTotalRecebidoPeriodo($clientId, $periodo);
            $valor = $valor - $valorRecebido;

            // Se a graduacao atual for maior que DUPLO DIAMANTE, O VALOR RECEBIDO È DO DIAMANTE DUPLO
            if( $planoCarreiraId > 12 ) :
                $valorDiamanteDuplo = $this->getValorDistruicaoGraduacao(12);
                $valorPeriodo = $periodo == 'Primeiro periodo' ? $valorDiamanteDuplo['valorPrimeiroPeriodo'] : $valorDiamanteDuplo['valorSegundoPeriodo'];
                $valor = $valorPeriodo - $valorRecebido;

                // JA RECEBEU O LIMITE MAXIMO PERMITIDO
                if($valorRecebido >= $valorPeriodo) :
                    return null;
                endif;
            endif;
        else:
            $valor = $valor;
        endif;

        return [
            'clienteId'         => $clientId,
            'graduacaoAnterior' => $maxGraduacaoBonusAceleracao, 
            'periodo'           => $periodo, 
            'valorDistribuicao' => $valor, 
            'planoCarreiraId'   => $planoCarreiraId,
            'qtdMesesCadastrado'=> $comparaMesesCadastro
        ];
    }

    function dateDifference($date_1 , $date_2) {
        $d1 = new DateTime($date_1);
        $d2 = new DateTime($date_2);
        $interval = $d1->diff($d2);
        $diffInSeconds = $interval->s;
        $diffInMinutes = $interval->i;
        $diffInHours   = $interval->h;
        $diffInDays    = $interval->d;
        $diffInMonths  = $interval->m;
        $diffInYears   = $interval->y;

        return ['ano' => $diffInYears, 'mes' => $diffInMonths];
    }

     /** 
    * Retorna um array contendo os valores para distruibuicao da graduacao do primeiro e segundo período
    * @param null $planoCarreiraId
    * @return array
    * @throws PropelException
    */
    function getValorDistruicaoGraduacao($planoCarreiraId) {
        $proximaGraduacao = PlanoCarreiraQuery::create()
        ->filterById($planoCarreiraId, Criteria::EQUAL)
        ->findOne();

        $valorPrimeiroPeriodo = $proximaGraduacao->getValorBonusAceleracaoPrimeiroPeriodo();
        $valorSegundoPeriodo  = $proximaGraduacao->getValorBonusAceleracaoSegundoPeriodo();

        return ['valorPrimeiroPeriodo' => $valorPrimeiroPeriodo, 'valorSegundoPeriodo' => $valorSegundoPeriodo];
    }
}