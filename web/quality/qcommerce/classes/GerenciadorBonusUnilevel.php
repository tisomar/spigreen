<?php

use Monolog\Logger;

/**
 * Description GerenciadorBonusUnilevel
 * 
 * @author Kleiton Albuquerque
 */
class GerenciadorBonusUnilevel extends GerenciadorPontos
{
    private $pontosClientes = [];
    private $ativacaoClientes = [];
    private $graduacaoCliente = [];

     /**
     * GerenciadorBonusUnilevel constructor.
     * @param PropelPDO $con
     * @param Logger $logger
     */
    public function __construct(\PropelPDO $con, Logger $logger)
    {
        parent::__construct($con, $logger);
    }

    /**
     *
     * @param Distribuicao $distribuicao
     * @throws Exception
     */
    public function geraPreview(Distribuicao $distribuicao)
    {
        if ($distribuicao->getStatus() != Distribuicao::STATUS_AGUARDANDO_PREVIEW) :
            throw new LogicException('Preview desta distribuição já foi gerado.');
        endif;

        $distribuicao->setStatus(Distribuicao::STATUS_PROCESSANDO_PREVIEW);
        $distribuicao->save($this->con);

        $this->con->beginTransaction();
        try {
            $clientes = ClienteQuery::create()
                ->orderById()
                ->filterByStatus(1)
                ->filterByVago(false)
                ->usePlanoQuery()
                    ->filterByPlanoClientePreferencial(false)
                ->endUse()
                ->find();

            foreach ($clientes as $cliente) :
                if($this->getClienteAtivoMesAnterior($cliente->getId())) :
                    $this->distribuiBonusAoCliente($distribuicao, $cliente); // chamar a função para distribuir pontuação para cliente ativo
                endif;
            endforeach;

            $distribuicao->setStatus(Distribuicao::STATUS_PREVIEW);
            $distribuicao->save($this->con);

            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) :
                $this->con->rollBack();
            endif;

            throw $ex;
        }
    }

    /**
     *
     * @param Distribuicao $distribuicao
     * @throws Exception
     */
    public function confirmaDistribuicao(Distribuicao $distribuicao)
    {
        if ($distribuicao->getStatus() != Distribuicao::STATUS_AGUARDANDO) :
            if (in_array($distribuicao->getStatus(), array(Distribuicao::STATUS_AGUARDANDO_PREVIEW, Distribuicao::STATUS_PROCESSANDO_PREVIEW, Distribuicao::STATUS_PREVIEW))) :
                throw new LogicException('Preview desta distribuição ainda não foi gerado.');
            else :
                throw new LogicException('Esta distribuição já foi gerada.');
            endif;
        endif;

        $distribuicao->setStatus(Distribuicao::STATUS_PROCESSANDO);
        $distribuicao->save($this->con);

        $this->con->beginTransaction();

        try {
            //busca os registros de distribuicao_cliente gerados durante o preview e gera o extrato para cada cliente.
            $query = DistribuicaoClienteQuery::create()
                ->filterByDistribuicao($distribuicao);

            $totalPontos = 0;

            foreach ($query->find($this->con) as $distribuicaoCliente) :
                $extrato = $this->geraExtratoCliente($distribuicaoCliente);

                if ($extrato) :
                    $totalPontos += $extrato->getPontos();
                endif;
            endforeach;

            $distribuicao->setTotalPontos($totalPontos);
            $distribuicao->setStatus(Distribuicao::STATUS_DISTRIBUIDO);
            $distribuicao->save($this->con);

            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) :
                $this->con->rollBack();
            endif;

            throw $ex;
        }
    }

    /**
     *
     * @param Distribuicao $distribuicao Distribuicao que deve ser executada.

     * @throws Exception
     */
    protected function distribuiBonusAoCliente(Distribuicao $distribuicao, Cliente $cliente)
    {
        $con = $this->con;

        $plano = $cliente->getPlano();

        if (!$plano) :
            return; // cliente sem plano
        endif;

        //verifica se ja distribuimos os pontos a este cliente
        $query = DistribuicaoClienteQuery::create()
            ->filterByDistribuicao($distribuicao)
            ->filterByCliente($cliente);

        if ($query->count($con) > 0) :
            $con->commit();
            return; //ja foi distribuido.
        endif;

        //busca o total de pontos de adesão/indicação, recompra e liderança a distrubuir
        $totalPontosAdesao = $this->getTotalPontosParaDistribuirCliente($cliente, Extrato::TIPO_INDICACAO_INDIRETA);
        $totalPontosRecompra = $this->getTotalPontosParaDistribuirCliente($cliente, Extrato::TIPO_RESIDUAL);
        $totalPontosLideranca = $this->getTotalBonusLiderancaCliente($distribuicao, $cliente);

        //verifica quantos pontos ja foram distribuidos em execucoes anteriores
        $distribuidosAnteriormente = $this->getTotalPontosProcessadosAnteriormente($cliente);

        //escolhe o lado com menos pontos
        $pontosProcessados = ($totalPontosAdesao + $totalPontosRecompra + $totalPontosLideranca) - $distribuidosAnteriormente;
        $pontosProcessados = ($pontosProcessados < 0) ? 0 : $pontosProcessados;

        $data = new Datetime();

        //cria um registro de distribuicao_cliente para indicar que a distribuição deste cliente ja foi feita,
        //e para salvar a quantidade de pontos que foram considerados nessa distribuicao (é necessario nos proximos ciclos).
        $distribuicaoCliente = DistribuicaoClienteQuery::create()
            ->filterByDistribuicaoId($distribuicao->getId())
            ->filterByClienteId($cliente->getId())
            ->findOneOrCreate();

        $distribuicaoCliente
            ->setData($data)
            ->setTotalPontosUsados($pontosProcessados)
            ->setTotalPontosProcessados($pontosProcessados)
            ->setTotalPontos($pontosProcessados)
            ->setTotalPontosAdesao($totalPontosAdesao)
            ->setTotalPontosRecompra($totalPontosRecompra)
            ->setTotalPontosLideranca($totalPontosLideranca)
            ->save($con);

        $distribuicao->save($con);
    }

    /**
     * Retorna o total de pontos que devem ser distribuidos ao cliente, segundo as regras do bonus de rede binaria.
     * @warning: é retornado o total de pontos de todos os tempos. O controle de quantos pontos ja foram distribuidos em
     * execuções anteriores e quantos faltam distribuir deve ser feita pelo chamador.
     *verifica se ja distribuimos os pontos a este cliente
     * @param Cliente $cliente
     * @param $lado
     * @return float
     */
    protected function getTotalPontosParaDistribuirCliente(Cliente $cliente, string $tipo = '')
    {
        $startDate = new Datetime('first day of last month');
        $startDate->setTime(0, 0, 0);
        $endDate = new Datetime('last day of last month');
        $endDate->setTime(23, 59, 59);

        $valor = 0;

        if ($tipo === Extrato::TIPO_RESIDUAL) :
            $bonificacaoProdutividade = new BonificacaoProdutividade();
            //$bonificacaoProdutividade->executarCompressaoDinamica($cliente, $startDate, $endDate);
            $valor = $bonificacaoProdutividade->getTotalBonusDistribuicao($cliente, $startDate, $endDate);
        else:
            $query = ExtratoQuery::create()
                ->addAsColumn('total', "SUM(ROUND(CASE OPERACAO WHEN '-' THEN 0 - PONTOS ELSE PONTOS END, 3))")
                ->filterByCliente($cliente)
                ->usePedidoQuery()
                ->filterByCreatedAt(['min' => $startDate, 'max' => $endDate])
                ->endUse()
                ->_if($tipo == Extrato::TIPO_INDICACAO_INDIRETA)
                ->filterByTipo(Extrato::TIPO_INDICACAO_INDIRETA)
                ->_elseif($tipo == Extrato::TIPO_RESIDUAL)
                ->filterByTipo(Extrato::TIPO_RESIDUAL)
                ->_else()
                ->filterByTipo([
                    Extrato::TIPO_INDICACAO_INDIRETA,
                    Extrato::TIPO_RESIDUAL
                ])
                ->_endif();

            if ($row = BasePeer::doSelect($query)->fetch()) :
                $valor = (float) $row['total'];
            endif;
        endif;

        return $valor;
    }

    /**
     *
     * @param Cliente $cliente
     * @return float
     */
    protected function getTotalPontosProcessadosAnteriormente(Cliente $cliente)
    {
        $startDate = new Datetime('first day of this month');
        $startDate->setTime(0, 0, 0);
        $endDate = new Datetime('last day of this month');
        $endDate->setTime(23, 59, 59);

        $query = DistribuicaoClienteQuery::create()
            ->addAsColumn('total', sprintf('SUM(round(%s, 3))', DistribuicaoClientePeer::TOTAL_PONTOS_PROCESSADOS))
            ->useDistribuicaoQuery()
                ->filterByStatus('DISTRIBUIDO')
            ->endUse()
            ->filterByData(['min' => $startDate, 'max' => $endDate])
            ->filterByCliente($cliente);

        if ($row = BasePeer::doSelect($query)->fetch()) :
            return (float) $row['total'];
        endif;

        return 0;
    }

    /**
     *
     * @param DistribuicaoCliente $distribuicaoCliente
     * @return Extrato|null
     */
    protected function geraExtratoCliente(DistribuicaoCliente $distribuicaoCliente)
    {
        if ($distribuicaoCliente->getTotalPontos() > 0) :
            $data = new DateTime();

            $startDate = new Datetime('first day of last month');
            $startDate->setTime(0, 0, 0);
            $endDate = new Datetime('last day of last month');
            $endDate->setTime(23, 59, 59);

            $indicacao = $recompra = 0;

            $extratos = ExtratoQuery::create()
                ->select(['total', 'tipo'])
                ->withColumn("SUM(ROUND(CASE OPERACAO WHEN '-' THEN 0 - PONTOS ELSE PONTOS END, 3))", 'total')
                ->filterByClienteId($distribuicaoCliente->getClienteId())
                ->usePedidoQuery()
                    ->filterByCreatedAt(['min' => $startDate, 'max' => $endDate])
                ->endUse()
                ->filterByData($distribuicaoCliente->getData(), Criteria::LESS_EQUAL)
                ->filterByTipo([
                    Extrato::TIPO_INDICACAO_INDIRETA,
                    Extrato::TIPO_RESIDUAL
                ])
                ->groupByTipo()
                ->find();

            $observacao = sprintf(
                'Distribuição de Bônus: Bônus de Indicação: R$ %s, Bônus de Produtividade: R$ %s, Bônus de Liderança: R$ %s',
                number_format($distribuicaoCliente->getTotalPontosAdesao(), 2, ',', '.'),
                number_format($distribuicaoCliente->getTotalPontosRecompra(), 2, ',', '.'),
                number_format($distribuicaoCliente->getTotalPontosLideranca(), 2, ',', '.')
            );

            //cria o extrato do cliente
            $extratoDistribuicao = new Extrato();
            $extratoDistribuicao->setClienteId($distribuicaoCliente->getClienteId());
            $extratoDistribuicao->setTipo(Extrato::TIPO_DISTRIBUICAO_REDE);
            $extratoDistribuicao->setPontos($distribuicaoCliente->getTotalPontos());
            $extratoDistribuicao->setOperacao('+');
            $extratoDistribuicao->setData($data);
            $extratoDistribuicao->setObservacao($observacao);
            $extratoDistribuicao->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extratoDistribuicao->save($this->con);

            $extrato = new Extrato();
            $extrato->setClienteId($distribuicaoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_INDICACAO_INDIRETA);
            $extrato->setPontos($distribuicaoCliente->getTotalPontosAdesao());
            $extrato->setOperacao('-');
            $extrato->setData($data);
            $extrato->setObservacao('Bônus distribuídos.');
            $extrato->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extrato->save($this->con);

            $extrato = new Extrato();
            $extrato->setClienteId($distribuicaoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_RESIDUAL);
            $extrato->setPontos($distribuicaoCliente->getTotalPontosRecompra());
            $extrato->setOperacao('-');
            $extrato->setData($data);
            $extrato->setObservacao('Bônus distribuídos.');
            $extrato->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extrato->save($this->con);

            return $extratoDistribuicao;
        elseif (!$this->getClienteAtivoMesAnterior($distribuicaoCliente->getClienteId())) :
            $data = new DateTime();

            $startDate = new Datetime('first day of last month');
            $startDate->setTime(0, 0, 0);
            $endDate = new Datetime('last day of last month');
            $endDate->setTime(23, 59, 59);

            $indicacao = $recompra = 0;

            $extratos = ExtratoQuery::create()
                ->select(['total', 'tipo'])
                ->withColumn("SUM(ROUND(CASE OPERACAO WHEN '-' THEN 0 - PONTOS ELSE PONTOS END, 3))", 'total')
                ->filterByClienteId($distribuicaoCliente->getClienteId())
                ->usePedidoQuery()
                    ->filterByCreatedAt(['min' => $startDate, 'max' => $endDate])
                ->endUse()
                ->filterByData($distribuicaoCliente->getData(), Criteria::LESS_EQUAL)
                ->filterByTipo([
                    Extrato::TIPO_INDICACAO_INDIRETA,
                    Extrato::TIPO_RESIDUAL
                ])
                ->groupByTipo()
                ->find();

            foreach ($extratos as $extrato) :
                switch ($extrato['tipo']):
                    case Extrato::TIPO_INDICACAO_INDIRETA:
                        $indicacao = $extrato['total'];
                        break;
                    case Extrato::TIPO_RESIDUAL:
                        $recompra = $extrato['total'];
                        break;
                endswitch;
            endforeach;

            $extrato = new Extrato();
            $extrato->setClienteId($distribuicaoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_INDICACAO_INDIRETA);
            $extrato->setPontos($indicacao);
            $extrato->setOperacao('-');
            $extrato->setData($data);
            $extrato->setObservacao('Bônus perdidos por não ativação.');
            $extrato->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extrato->save($this->con);

            $extrato = new Extrato();
            $extrato->setClienteId($distribuicaoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_RESIDUAL);
            $extrato->setPontos($recompra);
            $extrato->setOperacao('-');
            $extrato->setData($data);
            $extrato->setObservacao('Bônus perdidos por não ativação.');
            $extrato->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extrato->save($this->con);

            return null;
        endif;
    }

    private function getClienteAtivoMesAnterior($clienteId)
    {
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of this month');
        $end->setTime(0, 0, -1);

        return ClientePeer::getClienteAtivoMensal($clienteId, $start, $end);
    }

    public function getTotalBonusLiderancaCliente(Distribuicao $distribuicao, Cliente $cliente)
    {
        if (!$this->cumpreRequisitosBonusLideranca($cliente)) :
            return 0;
        endif;

        $bonusAReceber = 0;

        $mesPassado = Date('n', strtotime('-1 month'));
        $anoMesPassado = Date('Y', strtotime('-1 month'));

        /**
         * @var $planoCarreira PlanoCarreira
         */
        $planoCarreira = $this->getGraduacao($cliente, $mesPassado, $anoMesPassado);

        if ($planoCarreira) :
            $percentual = $planoCarreira->getPercBonusLideranca();

            if (!empty($percentual)) :
                $pontosPessoais = $this->getPontosPessoaisMes($cliente->getId(), $mesPassado, $anoMesPassado);
                $valorLiderancaPessoal = $pontosPessoais * $percentual / 100;
                $bonusAReceber += $valorLiderancaPessoal;

                // $bonusLideranca = BonusLiderancaQuery::create()
                //     ->filterByDistribuicaoId($distribuicao->getId())
                //     ->filterByClienteId($cliente->getId())
                //     ->filterByFilhoDiretoId(null, Criteria::ISNULL)
                //     ->findOneOrCreate();
                
                // $bonusLideranca
                //     ->setValor($valorLiderancaPessoal)
                //     ->save();

                $disFilhos = ClienteQuery::create()
                    ->filterByClienteIndicadorId($cliente->getId())
                    ->usePlanoQuery()
                        ->filterByPlanoClientePreferencial(false)
                    ->endUse()
                    ->find();
                
                foreach ($disFilhos as $disFilho) :
                    $percentualFilho = 0;
                    $planoCarreiraHistorico = $disFilho->getPlanoCarreira($mesPassado, $anoMesPassado);

                    if ($planoCarreiraHistorico && !empty($planoCarreiraHistorico->getPlanoCarreira()->getPercBonusLideranca())) :
                        $percentualFilho = $planoCarreiraHistorico->getPlanoCarreira()->getPercBonusLideranca();
                    endif;

                    $valorLideranca = $this->getValorLiderancaCliente(
                        $disFilho,
                        $percentual,
                        $percentual - $percentualFilho,
                        $mesPassado,
                        $anoMesPassado
                    );

                    $valor = $valorLideranca < 0 ? 0 : $valorLideranca;
                    $bonusAReceber += $valor;

                    $bonusLideranca = BonusLiderancaQuery::create()
                        ->filterByDistribuicaoId($distribuicao->getId())
                        ->filterByClienteId($cliente->getId())
                        ->filterByFilhoDiretoId($disFilho->getId())
                        ->findOneOrCreate();
                    
                    if (!empty($valorLideranca)) :
                        $bonusLideranca
                            ->setValor($valorLideranca)
                            ->save();
                    else :
                        $bonusLideranca->delete();
                    endif;
                endforeach;
            endif;
        endif;
            
        return $bonusAReceber < 0 ? 0 : $bonusAReceber;
    }

    private function cumpreRequisitosBonusLideranca(Cliente $cliente)
    {
//        // Verificar se o cliente é pessoa jurídica
//
//        // Por enquanto será verificado a ativação do cliente apenas no mês anterior (-1 mes)
//        $primeiroMes = Date('m', strtotime('-1 month'));
//        $anoPrimeiroMes = Date('Y', strtotime('-1 month'));
//
//        //$segundoMes = Date('m', strtotime('-1 months'));
//        //$anoSegundoMes = Date('Y', strtotime('-1 months'));
//
//        $planosCarreiraHistorico = PlanoCarreiraHistoricoQuery::create()
//            ->filterByCliente($cliente)
//            ->condition('cond1MesAtras1', 'PlanoCarreiraHistorico.Mes = ?', $primeiroMes)
//            ->condition('cond1MesAtras2', 'PlanoCarreiraHistorico.Ano = ?', $anoPrimeiroMes)
//            //->condition('cond2MesesAtras2', 'PlanoCarreiraHistorico.Ano = ?', $anoSegundoMes)
//            //->condition('cond2MesesAtras1', 'PlanoCarreiraHistorico.Mes = ?', $segundoMes)
//            ->where(['cond1MesAtras1', 'cond1MesAtras2'], 'and')
//            //->_or()
//            //->where(['cond2MesesAtras1', 'cond2MesesAtras2'], 'and')
//            ->find();
//
//        // Verifica se tem plano de carreira no último mes, ou seja, se esteve ativo nesse tempo
//        if (count($planosCarreiraHistorico) < 1) :
//            return;
//        endif;
//
//        // Verifica se o mímimo de pontos foi atingido no mês passado
//        foreach ($planosCarreiraHistorico as $planoHistorico) :
//            /**
//             * @var $planoHistorico PlanoCarreiraHistorico
//             */
//            if ($planoHistorico->getMes() == $primeiroMes && $planoHistorico->getAno() == $anoPrimeiroMes) :
//                $totalPontos = $planoHistorico->getTotalPontosPessoais() +
//                    $planoHistorico->getTotalPontosAdesao();
//
//                if ($totalPontos < 80) :
//                    return;
//                endif;
//            endif;
//        endforeach;
//
//        return true;

        return $this->getAtivacaoMesAnterior($cliente->getId());
    }

    private function getAtivacaoMesAnterior($clienteId)
    {
        if (!isset($this->ativacaoClientes[$clienteId])) :
            $start = new DateTime('first day of last month');
            $start->setTime(0, 0, 0);

            $end = new DateTime('last day of last month');
            $end->setTime(23, 59, 59);

            $this->ativacaoClientes[$clienteId] = ClientePeer::getClienteAtivoMensal($clienteId, $start, $end);
        endif;

        return $this->ativacaoClientes[$clienteId];
    }

    private function getPontosPessoaisMes($clienteId, $mes, $ano)
    {
        $pontos = 0;

        if (isset($this->pontosClientes[$clienteId])) :
            $pontos = $this->pontosClientes[$clienteId];
        else :
            $controlePontuacao = ControlePontuacaoClienteQuery::create()
                ->filterByClienteId($clienteId)
                ->filterByMes($mes)
                ->filterByAno($ano)
                ->findOne();

            if (!empty($controlePontuacao)) :
                $pontos = $controlePontuacao->getPontosPessoais() ?? 0;
            endif;

            $this->pontosClientes[$clienteId] = $pontos;
        endif;

        return $pontos;
    }

    private function getGraduacao($cliente, $mes, $ano)
    {
        $graduacao = null;
        $clienteId = $cliente->getId();

        if (isset($this->graduacaoCliente[$clienteId])) :
            $graduacao = $this->graduacaoCliente[$clienteId];
        else :
            $graduacaoHistorico = $cliente->getPlanoCarreira($mes, $ano);

            if (!empty($graduacaoHistorico)) :
                $graduacao = $graduacaoHistorico->getPlanoCarreira();
            endif;

            $this->graduacaoCliente[$clienteId] = $graduacao;
        endif;

        return $graduacao;
    }

    public function getValorLiderancaCliente($cliente, $percentualGeral, $percentual, $mes, $ano)
    {
        $pontos = $this->getPontosPessoaisMes($cliente->getId(), $mes, $ano);

        $valor = $pontos * $percentual / 100;

        $clientes = ClienteQuery::create()
            ->usePlanoQuery()
                ->filterByPlanoClientePreferencial(false)
            ->endUse()
            ->filterByClienteIndicadorId($cliente->getId())
            ->find();

        foreach ($clientes as $cliente) : /** @var $cliente Cliente */
            $plano = $this->getGraduacao($cliente, $mes, $ano);;

            $perc = !empty($plano) ? $plano->getPercBonusLideranca() : 0;

            if (!empty($perc) && $perc >= $percentualGeral) :
                continue;
            endif;

            $perc = empty($perc) || $perc < ($percentualGeral - $percentual) || !$this->cumpreRequisitosBonusLideranca($cliente) ? $percentual : $percentualGeral - $perc;

            $valor += $this->getValorLiderancaCliente($cliente, $percentualGeral, $perc, $mes, $ano);
        endforeach;

        return $valor;
    }


    // implementar este metoo com stream ou gerar a mesma logica em uma proc
    // no caso do DIS inativo procutar na arvore acima um DIS ATIVO
    public function getClienteRecebedor($clienteId) {
        $observacao = '';

        $clienteOriginal = ClienteQuery::create()->filterById($clienteId)->findOne();
        $cliente = null;
     
        $cliente = ClienteQuery::create()->filterById($clienteId)->findOne();

        while ($observacao == '') {

            $status = !$this->getClienteAtivoMesAnterior($cliente->getId()) ? 'Inativo' : 'Ativo';

            if(!$this->getClienteAtivoMesAnterior($cliente->getId())) :
                $cliente = $cliente->getPatrocinadorDireto();
                $clienteId = $cliente->getId();
            else:
                $observacao = 'Bônus recebido pela inativação do cliente ' . strtoupper($clienteOriginal->getNome());

                return [$cliente, $observacao];
            endif;
        };

    }
}
