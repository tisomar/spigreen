<?php

class GerenciadorPlanoCarreira
{

    /**
     * @var Cliente
     */
    private $cliente;

    /**
     *
     * @var PropelPDO
     */
    private $con;

    /**
     * @var double
     */
    private $aproveitamentoLinha;

    public function __construct(PropelPDO $con, Cliente $cliente)
    {
        $this->con = $con;
        $this->cliente = $cliente;
        $this->aproveitamentoLinha = 0.6;
    }

    /**
     * Retorna a maior qualificação já atingida pelo cliente
     *
     * @return mixed|PlanoCarreira|null
     * @throws PropelException
     */
    public function getMaiorQualificacaoAnterior()
    {
        $dataCadastro = $this->cliente->getCreatedAt();
        $qualificacao = null;

        $mes = (int)Date('m', strtotime($dataCadastro));
        $ano = (int)Date('Y', strtotime($dataCadastro));

        $mesAtual = (int)Date('m', strtotime('now'));
        $anoAtual = (int)Date('Y', strtotime('now'));

        $maiorPontuacao = 0;

        do {
            if (($ano > $anoAtual) || ($ano === $anoAtual && $mes >= $mesAtual)) :
                break;
            endif;

            $pontosMes = $this->getTotalPontosMes($mes > 9 ? (string)$mes : '0' . $mes, (string)$ano);

            if ($pontosMes > $maiorPontuacao) :
                // Verifica se a rede do cliente atende os requisitos para qualificação no mês
                $qualificacao = $this->getQualificacaoEquipe($mes, $ano);

                // Verifica se o cliente alcançou a pontuação pessoal mínima mínima mensal no mês
                if ($this->getTotalPontosPessoaisMes($mes, $ano) < ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal()) :
                    $qualificacao = null;
                endif;

                if ($qualificacao !== null) :
                    $maiorPontuacao = $pontosMes;
                endif;
            endif;

            if ($mes === 12) :
                $mes = 0;
                $ano++;
            endif;

            $mes++;
        } while (true);

        return $this->getQualificacaoPorPontos($maiorPontuacao);
    }

    /**
     * Retorna apenas a descrição da maior qualificação atingida pelo cliente
     *
     * @return string
     * @throws PropelException
     */
    public function getMaiorQualificacaoAnteriorDescricao()
    {
        $maiorQualificacaoAnterior = $this->getMaiorQualificacaoAnterior();

        return $maiorQualificacaoAnterior ? $maiorQualificacaoAnterior->getGraduacao() : '';
    }

    /**
     * Retorna a maior qualificação atingida pelo cliente
     * registrada no histórico do plano de carreira
     *
     * @return PlanoCarreiraHistorico
     * @throws PropelException
     */
    public function getMaiorQualificacaoAnteriorHistorico()
    {
        $dataCadastro = $this->cliente->getCreatedAt();

        /**
         * @var $maiorQualificao PlanoCarreiraHistorico
         */
        $maiorQualificao = null;

        $mes = (int)Date('m', strtotime($dataCadastro));
        $ano = (int)Date('Y', strtotime($dataCadastro));

        $mesAtual = (int)Date('m', strtotime('now'));
        $anoAtual = (int)Date('Y', strtotime('now'));

        do {
            if (($ano > $anoAtual) || ($ano === $anoAtual && $mes >= $mesAtual)) :
                break;
            endif;

            $qualificacaoMes = $this->getQualificacaoMesHistorico($mes > 9 ? (string) $mes : '0' . $mes, (string) $ano);

            if ($qualificacaoMes &&
                (!$maiorQualificao || $qualificacaoMes->getPlanoCarreira()->getNivel() > $maiorQualificao->getPlanoCarreira()->getNivel())) :
                $maiorQualificao = $qualificacaoMes;
            endif;

            if ($mes === 12) :
                $mes = 0;
                $ano++;
            endif;

            $mes++;
        } while (true);

        return $maiorQualificao;
    }

    /**
     * Retorna apenas a descrição da maior qualificação atingida pelo cliente
     * registrada no histórico do plano de carreira
     *
     * @return string
     * @throws PropelException
     */
    public function getMaiorQualificacaoAnteriorHistoricoDescricao()
    {
        $historico = $this->getMaiorQualificacaoAnteriorHistorico();

        return $historico ? $historico->getPlanoCarreira()->getGraduacao() : '';
    }

    public function getPlanoAtivo()
    {

    }

    /**
     * Retorna a primeira qualificação atingida pelo cliente
     * que engloba o período de 01/07/2019 a 31/10/2019
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @param mixed|Cliente|null $cliente
     * @return mixed|PlanoCarreira|null
     * @throws PropelException
     */
    public function getPrimeiraQualificacao($dataInicio, $dataFim, $cliente = null)
    {
        // Verifica se a rede do cliente atende os requisitos para qualificação
        $qualificacao = $this->getPrimeiraQualificacaoEquipe($dataInicio, $dataFim, $cliente);

        // Verifica se o cliente alcançou a pontuação pessoal mínima no período
        if ($this->getTotalPontosPessoaisPeriodo($dataInicio, $dataFim, $cliente) < ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal()) :
            $qualificacao = null;
        endif;

        return $qualificacao;
    }

    /**
     * Retorna a qualificação atual do cliente
     *
     * @return mixed|PlanoCarreira|null
     */
    public function getQualificacaoAtual()
    {
        return $this->getQualificacaoMes(Date('m'), Date('Y'));
    }

    /**
     * Retorna apenas a descrição da qualificação atual do cliente
     *
     * @return string
     */
    public function getQualificacaoAtualDescricao()
    {
        $qualificacao = $this->getQualificacaoAtual();

        return $qualificacao ? $qualificacao->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação atual do cliente
     * registrada no histórico do plano de carreira
     *
     * @param string $mes
     * @param string $ano
     * @return PlanoCarreiraHistorico
     */
    public function getQualificacaoAtualHistorico($mes, $ano)
    {
        return $this->getQualificacaoMesHistorico($mes ? $mes : Date('m'), $ano ? $ano : Date('Y'));
    }

    /**
     * Retorna apenas a descrição da qualificação atual do cliente
     * registrada no histórico do plano de carreira
     *
     * @param mixed|string|null $mes
     * @param mixed|string|null $ano
     * @return string
     * @throws PropelException
     */
    public function getQualificacaoAtualHistoricoDescricao($mes = null, $ano = null)
    {
        $historico = $this->getQualificacaoAtualHistorico($mes, $ano);

        return $historico ? $historico->getPlanoCarreira()->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação do cliente em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @param mixed|Cliente|null $cliente
     * @return mixed|PlanoCarreira|null
     * @throws PropelException
     */
    public function getQualificacaoMes($mes = '01', $ano = null, $cliente = null)
    {
        // Verifica se a rede do cliente atende os requisitos para qualificação
        $qualificacao = $this->getQualificacaoEquipe($mes, $ano, $cliente);

        // Verifica se o cliente alcançou a pontuação pessoal mínima mensal
        if ($this->getTotalPontosPessoaisMes($mes, $ano, $cliente) < ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal()) :
            $qualificacao = null;
        endif;

        return $qualificacao;
    }

    /**
     * Retorna apenas a descrição da qualificação do cliente em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @param mixed|Cliente|null $cliente
     * @return string
     * @throws PropelException
     */
    public function getQualificacaoMesDescricao($mes = '01', $ano = null, $cliente = null)
    {
        $qualificacao = $this->getQualificacaoMes($mes, $ano, $cliente);

        return $qualificacao ? $qualificacao->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação do cliente em determinado mês
     * registrada no histórico do plano de carreira
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return PlanoCarreiraHistorico
     * @throws PropelException
     */
    public function getQualificacaoMesHistorico($mes = '01', $ano = null)
    {
        $historico = PlanoCarreiraHistoricoQuery::create()
            ->filterByCliente($this->cliente)
            ->filterByMes((int) $mes)
            ->filterByAno($ano ? $ano : Date('Y'))
            ->findOne();

        return $historico;
    }

    /**
     * Retorna apenas a descrição da qualificação do cliente
     * em determinado mês registrada no plano de carreira
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return string
     * @throws PropelException
     */
    public function getQualificacaoMesHistoricoDescricao($mes = '01', $ano = null)
    {
        $historico = $this->getQualificacaoMesHistorico($mes, $ano);

        return $historico ? $historico->getPlanoCarreira()->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação do cliente no mês anterior de determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return mixed|PlanoCarreira|null
     * @throws PropelException
     */
    public function getQualificacaoMesAnterior($mes = '01', $ano = null)
    {
        $data = $mes;

        if ($ano === null) :
            $data = date('Y') . '-' . $data;
        else :
            $data = $ano . '-' . $data;
        endif;

        $data = $data . '-01';

        $periodoAnterior = date('r', strtotime('-1 month', strtotime($data)));

        $qualificacaoPeriodo = $this->getQualificacaoMes(Date('m', strtotime($periodoAnterior)), Date('Y', strtotime($periodoAnterior)));

        return $qualificacaoPeriodo;
    }

    /**
     * Retorna apenas a descrição da qualificação do cliente no mês anterior de determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return string
     * @throws PropelException
     */
    public function getQualificacaoMesAnteriorDescricao($mes = '01', $ano = null)
    {
        $qualificacao = $this->getQualificacaoMesAnterior($mes, $ano);

        return $qualificacao ? $qualificacao->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação do cliente no mês anterior de determinado mês
     * registrada no histórico do plano de carreira
     *
     * @param $mes
     * @param $ano
     * @return PlanoCarreiraHistorico
     * @throws PropelException
     */
    public function getQualificacaoMesAnteriorHistorico($mes, $ano)
    {
        $data = $mes ? $mes : Date('m');

        if ($ano === null) :
            $data = date('Y') . '-' . $data;
        else :
            $data = $ano . '-' . $data;
        endif;

        $data = $data . '-01';

        $periodoAnterior = date('r', strtotime('-1 month', strtotime($data)));

        return $this->getQualificacaoMesHistorico(Date('m', strtotime($periodoAnterior)), Date('Y', strtotime($periodoAnterior)));
    }

    /**
     * Retorna apenas a descrição da qualificação do cliente no mês anterior
     * de determinado mês registrada no histórico do plano de carreira
     *
     * @param mixed|string|null $mes
     * @param mixed|string|null $ano
     * @return string
     * @throws PropelException
     */
    public function getQualificacaoMesAnteriorHistoricoDescricao($mes = null, $ano = null)
    {
        $historico = $this->getQualificacaoMesAnteriorHistorico($mes, $ano);

        return $historico ? $historico->getPlanoCarreira()->getGraduacao() : '';
    }

    /**
     * Retorna a qualificação baseada pelos pontos do cliente
     *
     * @param int $pontos
     * @return mixed|PlanoCarreira|null
     */
    public function getQualificacaoPorPontos($pontos)
    {
        $query = PlanoCarreiraQuery::create()
            ->find();

        $qualificacao = null;

        /**
         * @var $planoCarreira PlanoCarreira
         */
        foreach ($query as $planoCarreira) :
            if ($pontos >= $planoCarreira->getPontos()) :
                $qualificacao = $planoCarreira;
            else :
                break;
            endif;
        endforeach;

        return $qualificacao;
    }

    /**
     * Retorna o status de ativação do cliente em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return boolean
     * @throws PropelException
     */
    public function getStatusAtivacao($mes = '01', $ano = null)
    {
        if (is_null($this->cliente->getPlanoId())) :
            return false;
        endif;

        if ($ano === null) :
            $ano = date('Y');
        endif;

        if ($mes == '10' && $ano == '2019') :
            return true;
        endif;

        $dataInicio = DateTime::createFromFormat('Y-m-d H:i:s', "{$ano}-{$mes}-01 00:00:00");
        $dataFim = (clone $dataInicio)->modify('last day of this month');
        $dataFim->setTime(23, 59, 59);

        return ClientePeer::getClienteAtivoMensal($this->cliente->getId(), $dataInicio, $dataFim);
    }

    /**
     * Retorna o total de pontos do cliente em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosMes($mes = '01', $ano = null, $cliente = null)
    {
        $data = $mes;

        if ($ano === null) :
            $data = date('Y') . '-' .$data;
        else :
            $data = $ano . '-' .$data;
        endif;

        $dataInicio = $data . '-01';

        $dataFim = new DateTime($dataInicio);
        $dataFim = $dataFim->format('Y-m-t');

        return $this->getTotalPontos($dataInicio, $dataFim, null, $cliente);
    }

    /**
     *  Retorna o total de pontos do cliente em determinado período
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosPeriodo($dataInicio, $dataFim)
    {
        $dataInicioFormatada = explode('/', $dataInicio);
        $dataInicioFormatada = new DateTime($dataInicioFormatada[2] . '-' . $dataInicioFormatada[1] . '-' . $dataInicioFormatada[0]);

        $dataFimFormatada = explode('/', $dataFim);
        $dataFimFormatada = new DateTime($dataFimFormatada[2] . '-' . $dataFimFormatada[1] . '-' . $dataFimFormatada[0]);

        return $this->getTotalPontos($dataInicioFormatada->format('Y-m-d'), $dataFimFormatada->format('Y-m-d'));
    }

    /**
     * Retorna o total de pontos de adesão da equipe do cliente
     * em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosEquipeAdesaoMes($mes = '01', $ano = null)
    {
        $inicio = date_create_from_format('Y-m-d', "{$ano}-{$mes}-01");
        $inicio->setTime(0, 0, 0, 0);
        
        $fim = clone $inicio;
        $fim->modify('last day of this month');
        $fim->setTime(23, 59, 59, 999999);

        $pontosAdesao = PedidoQuery::create()
            ->select(['valorTotalPontos'])
            ->withColumn(sprintf('IFNULL(SUM(%s * %s), 0)', PedidoItemPeer::VALOR_PONTOS_UNITARIO, PedidoItemPeer::QUANTIDADE), 'valorTotalPontos')
            ->useClienteQuery()
                ->filterByTreeRight($this->cliente->getTreeRight(), Criteria::LESS_THAN)
                ->filterByTreeLeft($this->cliente->getTreeLeft(), Criteria::GREATER_THAN)
            ->endUse()
            ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNOTNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByDataPagamentoPeriodo($inicio, $fim)
            ->findOne();

        return $pontosAdesao;
    }

    /**
     * Retorna o total de pontos de adesão da equipe do cliente
     * em determinado período
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosEquipeAdesaoPeriodo($dataInicio, $dataFim)
    {
        $dataInicioFormatada = explode('/', $dataInicio);
        $dataInicioFormatada = new DateTime($dataInicioFormatada[2] . '-' . $dataInicioFormatada[1] . '-' . $dataInicioFormatada[0]);

        $dataFimFormatada = explode('/', $dataFim);
        $dataFimFormatada = new DateTime($dataFimFormatada[2] . '-' . $dataFimFormatada[1] . '-' . $dataFimFormatada[0]);

        return $this->getTotalPontos($dataInicioFormatada->format('Y-m-d'), $dataFimFormatada->format('Y-m-d'), 'PA');
    }

    /**
     * Retorna o total de pontos de recompra da equipe do cliente
     * em determinado mês
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosEquipeRecompraMes($mes = '01', $ano = null)
    {
        $inicio = date_create_from_format('Y-m-d', "{$ano}-{$mes}-01");
        $inicio->setTime(0, 0, 0, 0);
        
        $fim = clone $inicio;
        $fim->modify('last day of this month');
        $fim->setTime(23, 59, 59, 999999);

        $pontosAdesao = PedidoQuery::create()
            ->select(['valorTotalPontos'])
            ->withColumn(sprintf('IFNULL(SUM(%s * %s), 0)', PedidoItemPeer::VALOR_PONTOS_UNITARIO, PedidoItemPeer::QUANTIDADE), 'valorTotalPontos')
            ->useClienteQuery()
                ->filterByTreeRight($this->cliente->getTreeRight(), Criteria::LESS_THAN)
                ->filterByTreeLeft($this->cliente->getTreeLeft(), Criteria::GREATER_THAN)
            ->endUse()
            ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->condition('cond1', sprintf('%s IS NULL', PedidoPeer::HOTSITE_CLIENTE_ID), null)
            ->condition('cond2', sprintf('%s <> ?', PedidoPeer::HOTSITE_CLIENTE_ID), $this->cliente->getId())
            ->where(['cond1', 'cond2'], 'or')
            ->filterByDataPagamentoPeriodo($inicio, $fim)
            ->findOne();

        return $pontosAdesao;
    }

    /**
     * Retorna o total de pontos de recompra da equipe do cliente
     * em determinado período
     *
     * @param $dataInicio
     * @param $dataFim
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosEquipeRecompraPeriodo($dataInicio, $dataFim)
    {
        $dataInicioFormatada = explode('/', $dataInicio);
        $dataInicioFormatada = new DateTime($dataInicioFormatada[2] . '-' . $dataInicioFormatada[1] . '-' . $dataInicioFormatada[0]);

        $dataFimFormatada = explode('/', $dataFim);
        $dataFimFormatada = new DateTime($dataFimFormatada[2] . '-' . $dataFimFormatada[1] . '-' . $dataFimFormatada[0]);

        return $this->getTotalPontos($dataInicioFormatada->format('Y-m-d'), $dataFimFormatada->format('Y-m-d'), 'PR');
    }

    /**
     * Retorna o total de pontos de recompra da equipe do cliente
     * em determinado período
     *
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosMaiorQualificacaoAnterior()
    {
        $dataCadastro = $this->cliente->getCreatedAt();
        $qualificacao = null;

        $mes = (int) Date('m', strtotime($dataCadastro));
        $ano = (int) Date('Y', strtotime($dataCadastro));

        $mesAtual = (int) Date('m', strtotime('now'));
        $anoAtual = (int) Date('Y', strtotime('now'));

        $maiorPontuacao = 0;
        $mesMaiorPontuacao = '';
        $anoMaiorPontucao = '';

        do {
            if (($ano > $anoAtual) || ($ano === $anoAtual && $mes > $mesAtual)) :
                break;
            endif;

            $pontosMes = $this->getTotalPontosMes($mes > 9 ? (string) $mes : '0'.$mes, (string) $ano);

            if ($pontosMes > $maiorPontuacao) :
                $mesMaiorPontuacao = (string) $mes;
                $anoMaiorPontucao = (string) $ano;
                $maiorPontuacao = $pontosMes;
            endif;

            if ($mes === 12) :
                $mes = 0;
                $ano++;
            endif;

            $mes++;
        } while (true);

        return $this->getTotalPontosMes($mesMaiorPontuacao, $anoMaiorPontucao);
    }

    /**
     * Retorna o total de pontos pessoais do cliente
     * em determinado período
     *
     * @param string $mes
     * @param mixed|string|null $ano
     * @param Cliente $cliente
     * @return float|int
     * @throws Exception
     */
    public function getTotalPontosPessoaisMes($mes = '01', $ano = null, $cliente = null)
    {
        $data = $mes;

        if ($ano === null) :
            $ano = date('Y');
        endif;

        $dataInicio = date_create_from_format('Y-m-d', "{$ano}-{$mes}-01");
        $dataInicio->setTime(0, 0, 0);

        $dataFim = clone $dataInicio;
        $dataFim->modify('first day of next month');
        $dataFim->setTime(0, 0, 0, -1);

        $cliente = $cliente ?? $this->cliente;

        return PedidoPeer::getPontosPedidosPeriodo($cliente->getId(), $dataInicio, $dataFim);
    }

    /**
     * Retorna o total de pontos pessoais do cliente
     * em determinado período
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @param mixed|Cliente|null $cliente
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontosPessoaisPeriodo($dataInicio, $dataFim, $cliente = null)
    {
        $dataInicioFormatada = explode('/', $dataInicio);
        $dataInicioFormatada = new DateTime($dataInicioFormatada[2] . '-' . $dataInicioFormatada[1] . '-' . $dataInicioFormatada[0]);

        $dataFimFormatada = explode('/', $dataFim);
        $dataFimFormatada = new DateTime($dataFimFormatada[2] . '-' . $dataFimFormatada[1] . '-' . $dataFimFormatada[0]);

        return $this->getTotalPontos($dataInicioFormatada->format('Y-m-d'), $dataFimFormatada->format('Y-m-d'), 'PP', $cliente);
    }

    /**
     * Retorna a qualifição que a equipe do cliente
     * alcançou em determinado mês
     *
     * @param string $mes
     * @param string $ano
     * @param Cliente $cliente
     * @return mixed|PlanoCarreira|null
     * @throws PropelException
     */
    private function getQualificacaoEquipe($mes = '01', $ano, $cliente = null)
    {
        $data = $mes;

        if ($ano === null) :
            $data = date('Y') . '-' .$data;
        else :
            $data = $ano . '-' .$data;
        endif;

        $dataInicio = $data . '-01';

        $dataFim = new DateTime($dataInicio);
        $dataFim = $dataFim->format('Y-m-t');

        if ($cliente === null) :
            $cliente = $this->cliente;
        endif;

        $totalPontuacaoEquipe = 0;
        $pontuacaoPessoal = $this->getTotalPontosPessoaisMes($mes, $ano, $cliente);
        $planoCarreira = null;

        $pontuacaoEquipeNivel = PedidoQuery::create()
            ->select(['TotalPontosNivel', 'Cliente.TreeLevel'])
            ->withColumn('SUM(Pedido.ValorPontos)', 'TotalPontosNivel')
            ->join('PedidoStatusHistorico')
            ->join('Cliente')
            ->condition('condData1', 'Pedido.CreatedAt >= ?', $dataInicio)
            ->condition('condData2', 'Pedido.CreatedAt <= ?', $dataFim)
            ->where(['condData1', 'condData2'], 'and')
            ->where('Pedido.Status <> ?', 'CANCELADO')
            ->where('Pedido.ClienteId <> ?', $cliente->getId())
            ->condition('condTree1', 'Cliente.TreeLeft > ?', $cliente->getTreeLeft())
            ->condition('condTree2', 'Cliente.TreeRight < ?', $cliente->getTreeRight())
            ->where(['condTree1', 'condTree2'], 'and')
            ->condition('condPagto1', 'PedidoStatusHistorico.IsConcluido = ?', '1')
            ->condition('condPagto2', 'PedidoStatusHistorico.PedidoStatusId = ?', '1')
            ->where(['condPagto1', 'condPagto2'], 'and')
            ->groupBy('Cliente.TreeLevel')
            ->find();

        $planosCarreira = PlanoCarreiraQuery::create()
            ->orderById(Criteria::ASC)
            ->find();

        /**
         * @var $plano PlanoCarreira
         */
        foreach ($planosCarreira as $plano) :
            foreach ($pontuacaoEquipeNivel as $pontuacaoNivel) :
                if ($plano->getId() === 1) :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() ?
                        $pontuacaoNivel['TotalPontosNivel'] : $plano->getPontos();
                elseif ($plano->getId() === 2) :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() ?
                        $pontuacaoNivel['TotalPontosNivel'] : $plano->getPontos();
                else :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() * $this->aproveitamentoLinha ?
                        $pontuacaoNivel['TotalPontosNivel'] :
                        $plano->getPontos() * $this->aproveitamentoLinha;
                endif;
            endforeach;

            if (($totalPontuacaoEquipe + $pontuacaoPessoal) >= $plano->getPontos()) :
                // Verifica se o cliente cumpre os requisitos para obter a qualificação
                // de acordo com a sua póntuação pessoal e de rede
                if ($this->verificaRequisitoQualificacao($plano, $cliente, $mes, $ano)) :
                    $planoCarreira = $plano;
                    $totalPontuacaoEquipe = 0;
                endif;
            else :
                break;
            endif;
        endforeach;

        return $planoCarreira;
    }

    /**
    * Retorna o valor da vlp para graduação
    * 
    *
    * @param Cliente $cliente
    * @return mixed|PlanoCarreira|null
    * @throws PropelException
    */
    public function getTotalPontosPorVlp($plano, $mes, $ano, $cliente = null)
    {
        if ($cliente === null) :
            $cliente = $this->cliente;
        endif;

        $pontuacaoEquipe = PlanoCarreiraHistoricoQuery::create()
            ->useClienteQuery()
                ->filterByClienteIndicadorId($cliente->getId())
            ->endUse()
            ->filterByMes((int) $mes)
            ->filterByAno($ano)
            ->find();

        $totalPontos = 0;

        foreach ($pontuacaoEquipe as $pontuacao) :
            $totalEquipe = $pontuacao->getTotalPontosPessoais() + $pontuacao->getTotalPontosAdesao() + $pontuacao->getTotalPontosRecompra();
            $totalApl = $plano->getAproveitamentoLinha();
            $totalPontos += min($totalEquipe, $totalApl);
        endforeach;

        $totalPontos += $this->getTotalPontosPessoaisMes($mes, $ano, $cliente);

        $dataInicio = DateTime::createFromFormat('Y-m-d', "{$ano}-{$mes}-01");
        $dataFim = (clone $dataInicio)->modify('last day of this month');

        return $totalPontos;
    }

    /**
     * Retorna a primeira qualificação que a equipe do cliente
     * alcançou, no período de 01/07/2019 a 31/10/2019
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @param mixed|Cliente|null $cliente
     * @return mixed|PlanoCarreira|null
     * @throws Exception
     */
    public function getPrimeiraQualificacaoEquipe($dataInicio, $dataFim, $cliente = null)
    {
        $dataInicioFormatada = explode('/', $dataInicio);
        $dataInicioFormatada = new DateTime($dataInicioFormatada[2] . '-' . $dataInicioFormatada[1] . '-' . $dataInicioFormatada[0]);

        $dataFimFormatada = explode('/', $dataFim);
        $dataFimFormatada = new DateTime($dataFimFormatada[2] . '-' . $dataFimFormatada[1] . '-' . $dataFimFormatada[0]);

        if ($cliente === null) :
            $cliente = $this->cliente;
        endif;

        $totalPontuacaoEquipe = 0;
        $pontuacaoPessoal = $this->getTotalPontosPessoaisPeriodo($dataInicio, $dataFim, $cliente);
        $planoCarreira = null;

        $pontuacaoEquipeNivel = PedidoQuery::create()
            ->select(['TotalPontosNivel', 'Cliente.TreeLevel'])
            ->withColumn('SUM(Pedido.ValorPontos)', 'TotalPontosNivel')
            ->join('PedidoStatusHistorico')
            ->join('Cliente')
            ->condition('condData1', 'Pedido.CreatedAt >= ?', $dataInicioFormatada)
            ->condition('condData2', 'Pedido.CreatedAt <= ?', $dataFimFormatada)
            ->where(['condData1', 'condData2'], 'and')
            ->where('Pedido.Status <> ?', 'CANCELADO')
            ->where('Pedido.ClienteId <> ?', $cliente->getId())
            ->condition('condTree1', 'Cliente.TreeLeft > ?', $cliente->getTreeLeft())
            ->condition('condTree2', 'Cliente.TreeRight < ?', $cliente->getTreeRight())
            ->where(['condTree1', 'condTree2'], 'and')
            ->condition('condPagto1', 'PedidoStatusHistorico.IsConcluido = ?', '1')
            ->condition('condPagto2', 'PedidoStatusHistorico.PedidoStatusId = ?', '1')
            ->where(['condPagto1', 'condPagto2'], 'and')
            ->groupBy('Cliente.TreeLevel')
            ->find();

        $planosCarreira = PlanoCarreiraQuery::create()
            ->orderById(Criteria::ASC)
            ->find();

        /**
         * @var $plano PlanoCarreira
         */
        foreach ($planosCarreira as $plano) :
            foreach ($pontuacaoEquipeNivel as $pontuacaoNivel) :
                if ($plano->getId() === 1) :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() ?
                        $pontuacaoNivel['TotalPontosNivel'] : $plano->getPontos();
                elseif ($plano->getId() === 2) :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() ?
                        $pontuacaoNivel['TotalPontosNivel'] : $plano->getPontos();
                else :
                    $totalPontuacaoEquipe += $pontuacaoNivel['TotalPontosNivel'] < $plano->getPontos() * $this->aproveitamentoLinha ?
                        $pontuacaoNivel['TotalPontosNivel'] :
                        $plano->getPontos() * $this->aproveitamentoLinha;
                endif;
            endforeach;

            if (($totalPontuacaoEquipe + $pontuacaoPessoal) >= $plano->getPontos()) :
                // Verifica se o cliente cumpre os requisitos para obter a qualificação
                // de acordo com a sua póntuação pessoal e de rede
                if ($this->verificaRequisitoQualificacao($plano, $cliente, '', '', true, $dataInicio, $dataFim)) :
                    $planoCarreira = $plano;
                    $totalPontuacaoEquipe = 0;
                endif;
            else :
                break;
            endif;
        endforeach;

        return $planoCarreira;
    }

    /**
     * Retorna o total de pontos que o cliente
     * alcançou em determinado período
     *
     * @param string $dataInicio
     * @param string $dataFim
     * @param mixed|string|null $tipo
     * @param mixed|string|Cliente $cliente
     * @return float|int
     * @throws PropelException
     */
    public function getTotalPontos($dataInicio, $dataFim, $tipo = null, $cliente = null)
    {
        $criteria = new Criteria();

        $condicao = "qp1_pedido.status <> 'CANCELADO'
                     AND qp1_pedido.created_at >= '{$dataInicio} 00:00:00'
                     AND qp1_pedido.created_at <= '{$dataFim} 23:59:59'
                     AND qp1_pedido.id = (SELECT pedido_id
                                          FROM qp1_pedido_status_historico
                                          WHERE qp1_pedido_status_historico.PEDIDO_ID = qp1_pedido.ID
                                            AND qp1_pedido_status_historico.is_concluido = 1
                                            AND qp1_pedido_status_historico.pedido_status_id = 1
                                            AND MONTH(qp1_pedido.created_at) = MONTH(qp1_pedido_status_historico.updated_at))";

        $cliente = $cliente ?? $this->cliente;

        switch ($tipo) :
            case 'PP':
                $condicao .= " AND IFNULL(qp1_pedido.hotsite_cliente_id, qp1_pedido.cliente_id) = {$cliente->getId()}";
                break;
            case 'PA':
            case 'PR':
                $condicao .= " AND IFNULL(qp1_pedido.HOTSITE_CLIENTE_ID, qp1_pedido.cliente_id) IN (
                                    SELECT id
                                    FROM qp1_cliente
                                    WHERE qp1_cliente.tree_left >= {$cliente->getTreeLeft()}
                                      AND qp1_cliente.tree_right <= {$cliente->getTreeRight()} )
                               AND qp1_pedido.cliente_id <> {$cliente->getId()}";
                break;
                break;
            default:
                $condicao .= " AND IFNULL(qp1_pedido.HOTSITE_CLIENTE_ID, qp1_pedido.cliente_id) IN (
                                    SELECT id
                                    FROM qp1_cliente
                                    WHERE qp1_cliente.tree_left >= {$cliente->getTreeLeft()}
                                      AND qp1_cliente.tree_right <= {$cliente->getTreeRight()} )";
        endswitch;
        
        $criteria->add(PedidoPeer::ID, $condicao, Criteria::CUSTOM);

        $pedidos = PedidoPeer::doSelect($criteria);

        $totalPontos = 0;

        /**
         * @var $pedido Pedido
         */
        foreach ($pedidos as $pedido) :
            $totalPontos += $pedido->getValorPontos();

            $pedidoItens = $pedido->getPedidoItems();

            /**
             * @var $pedidoItem PedidoItem
             */
            foreach ($pedidoItens as $pedidoItem) :
                /*
                 * Desabilitar o soft_delete para trazer os produtos "excluídos",
                 * produtos que tem valor na coluna data_exclusão
                 */
                ProdutoVariacaoPeer::disableSoftDelete();
                ProdutoPeer::disableSoftDelete();
                ProdutoAtributoPeer::disableSoftDelete();
                ProdutoVariacaoAtributoPeer::disableSoftDelete();

                $produtoVariacao = $pedidoItem->getProdutoVariacao();
                $produto = $produtoVariacao->getProduto();

                if ($tipo === 'PA') :
                    if (!$produto->isKitAdesao()) :
                        $totalPontos -= $pedidoItem->getValorPontosUnitario() * $pedidoItem->getQuantidade();
                    endif;
                elseif ($tipo === 'PR') :
                    if ($produto->isKitAdesao() || !empty($pedido->getHotsiteClienteId())) :
                        $totalPontos -= $pedidoItem->getValorPontosUnitario() * $pedidoItem->getQuantidade();
                    endif;
                endif;

                if ($tipo == 'PP' && !empty($pedido->getHotsiteClienteId()) && $produto->isKitAdesao()) :
                    $totalPontos -= $pedidoItem->getValorPontosUnitario() * $pedidoItem->getQuantidade();
                endif;
            endforeach;
        endforeach;

        return $totalPontos;
    }

    /**
     * Retorna se o cliente cumpre os requisitos para determinada
     * qualificação em determinado mês ou período
     *
     * @param PlanoCarreira $qualificacao
     * @param Cliente $cliente
     * @param string $mes
     * @param string $ano
     * @param boolean $verificaPorPeriodo
     * @param string $dataInicio
     * @param string $dataFim
     * @return bool
     */
    private function verificaRequisitoQualificacao($qualificacao, $cliente = null, $mes, $ano, $verificaPorPeriodo = false, $dataInicio, $dataFim)
    {
        $cumpreRequisito = false;

        if ($cliente === null) :
            $cliente = $this->cliente;
        endif;

        switch ($qualificacao->getGraduacao()) :
            case 'Consultor': // Adquirir Kit inicial
                $query = ClienteQuery::create()
                    ->filterById($cliente->getId())
                    ->where('Cliente.PlanoId IS NOT NULL')
                    ->findOne();

                if ($query) :
                    $cumpreRequisito = true;
                endif;
                break;

            case 'Supervisor': // Ter 3 consultores diretos ativos
            case 'Supervisor Master':
            case 'Supervisor Pleno':
            case 'Supervisor Senior':
            case 'Gerente':
                $query = ClienteQuery::create()
                    ->filterByClienteIndicadorId($cliente->getId())
                    ->find();

                $contConsultoresAtivos = 0;

                /**
                 * @var $cliente Cliente
                 */
                foreach ($query as $cliente) :
                    $qualificacao = $verificaPorPeriodo ? $this->getPrimeiraQualificacao($dataInicio, $dataFim, $cliente) :
                                    $this->getQualificacaoMes($mes, $ano, $cliente);

                    if ($qualificacao && $qualificacao->getId() >= 1) :
                        $contConsultoresAtivos++;
                    endif;

                    if ($contConsultoresAtivos >= 3) :
                        $cumpreRequisito = true;
                        break;
                    endif;
                endforeach;
                break;

            case 'Gerente Bronze': // Ter 4 consultores diretos ativos
            case 'Gerente Prata':
            case 'Gerente Ouro':
                $query = ClienteQuery::create()
                    ->filterByClienteIndicadorId($cliente->getId())
                    ->find();

                $contConsultoresAtivos = 0;

                /**
                 * @var $cliente Cliente
                 */
                foreach ($query as $cliente) :
                    $qualificacao = $verificaPorPeriodo ? $this->getPrimeiraQualificacao($dataInicio, $dataFim, $cliente) :
                                    $this->getQualificacaoMes($mes, $ano, $cliente);

                    if ($qualificacao && $qualificacao->getId() >= 1) :
                        $contConsultoresAtivos++;
                    endif;

                    if ($contConsultoresAtivos >= 4) :
                        $cumpreRequisito = true;
                        break;
                    endif;
                endforeach;
                break;

            case 'Executivo': // Ter 1 gerente em uma linha
                $query = ClienteQuery::create()
                    ->condition('condCliente1', 'Cliente.TreeLeft > ?', $cliente->getTreeLeft())
                    ->condition('condCliente2', 'Cliente.TreeRight < ?', $cliente->getTreeRight())
                    ->where(['condCliente1', 'condCliente2'], 'and')
                    ->find();

                $contGerente = 0;

                /**
                 * @var $cliente Cliente
                 */
                foreach ($query as $cliente) :
                    $qualificacao = $verificaPorPeriodo ? $this->getPrimeiraQualificacao($dataInicio, $dataFim, $cliente) :
                                    $this->getQualificacaoMes($mes, $ano, $cliente);

                    if ($qualificacao && $qualificacao->getId() >= 6) :
                        $contGerente++;
                    endif;

                    if ($contGerente >= 1) :
                        $cumpreRequisito = true;
                        break;
                    endif;
                endforeach;
                break;

            case 'Executivo Rubi':
                $query = ClienteQuery::create()
                    ->condition('condCliente1', 'Cliente.TreeLeft > ?', $cliente->getTreeLeft())
                    ->condition('condCliente2', 'Cliente.TreeRight < ?', $cliente->getTreeRight())
                    ->where(['condCliente1', 'condCliente2'], 'and')
                    ->orderBy('Cliente.TreeLevel')
                    ->find();

                $contGerente = 0;
                $pularLinha = 0;

                /**
                 * @var $cliente Cliente
                 */
                foreach ($query as $cliente) :
                    if ($cliente->getTreeLevel() <= $pularLinha) :
                        continue;
                    endif;

                    $qualificacao = $verificaPorPeriodo ? $this->getPrimeiraQualificacao($dataInicio, $dataFim, $cliente) :
                        $this->getQualificacaoMes($mes, $ano, $cliente);

                    if ($qualificacao && $qualificacao->getId() >= 6) :
                        $contGerente++;
                        $pularLinha = $cliente->getTreeLevel();
                    endif;

                    if ($contGerente >= 2) :
                        $cumpreRequisito = true;
                        break;
                    endif;
                endforeach;
                break;

            case 'Executivo Esmeralda':
                $cumpreRequisito = false;
                break;

            case 'Executivo Diamante':
                $cumpreRequisito = false;
                break;

            case 'Executivo Duplo Diamante':
                $cumpreRequisito = false;
                break;

            case 'Executivo Triplo Diamante':
                $cumpreRequisito = false;
                break;

            case 'Premier':
                $cumpreRequisito = false;
                break;

            case 'Premier Imperial':
                $cumpreRequisito = false;
                break;

            case 'Premier Global':
                $cumpreRequisito = false;
                break;

        endswitch;

        return $cumpreRequisito;
    }

}
