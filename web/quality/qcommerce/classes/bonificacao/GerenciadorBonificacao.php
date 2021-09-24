<?php


abstract class GerenciadorBonificacao
{

    const TIPO_EXPANSAO = 'EXPANSAO';
    const TIPO_PRODUTIVIDADE = 'PRODUTIVIDADE';
    const TIPO_CLIENTE_PREFERENCIAL = 'CLIENTE_PREFERENCIAL';

    /**
     * @var PropelPDO
     */
    protected $con;

    /**
     * GerenciadorBonificacao constructor.
     * @param PropelPDO $con
     */
    public function __construct(PropelPDO $con = null)
    {
        $this->con = $con;
    }

    /**
     * Retorna o valor total de pontos de um pedido, ou apenas de expansão, ou apenas de produtividade
     *
     * @param string $tipoBonififcacao (EXPANSAO, PRODUTIVIDADE)
     * @param Pedido $pedido
     * @return float|int
     * @throws PropelException
     */
    protected function getTotalPontosPedido(string $tipoBonififcacao, Pedido $pedido)
    {
        $totalPontos = 0;

        if ($tipoBonififcacao == self::TIPO_EXPANSAO) :
            $totalPontos = $pedido->getTotalPontosProdutos(Extrato::TIPO_INDICACAO);
        elseif ($tipoBonififcacao == self::TIPO_PRODUTIVIDADE) :
            $totalPontos = $pedido->getTotalPontosProdutos(Extrato::TIPO_RESIDUAL);
        elseif ($tipoBonififcacao == self::TIPO_CLIENTE_PREFERENCIAL) :
            $totalPontos = $pedido->getTotalPontosProdutos(Extrato::TIPO_CLIENTE_PREFERENCIAL);
        elseif (!$tipoBonififcacao) :
            $totalPontos = $pedido->getValorPontos();
        endif;

        return $totalPontos;
    }

    /**
     * Verifica se já existe um extrato criado para o pedido, tipo e cliente
     *
     * @param string $tipoBonificacao
     * @param Pedido $pedido
     * @param Cliente|null $cliente
     * @return bool
     * @throws PropelException
     */
    protected function existeExtrato(string $tipoBonificacao, Pedido $pedido, Cliente $cliente = null)
    {
        $query = ExtratoQuery::create()
            ->filterByPedido($pedido)
            ->_if($tipoBonificacao === self::TIPO_EXPANSAO)
                ->filterByTipo(array(Extrato::TIPO_INDICACAO_INDIRETA, Extrato::TIPO_INDICACAO_DIRETA))
            ->_elseif($tipoBonificacao === self::TIPO_PRODUTIVIDADE)
                ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->_elseif($tipoBonificacao === self::TIPO_CLIENTE_PREFERENCIAL)
                ->filterByTipo(Extrato::TIPO_CLIENTE_PREFERENCIAL)
            ->_endif()
            ->_if($cliente)
                ->filterByCliente($cliente)
            ->_endif();

        if ($query->count($this->con) > 0) :
            return true;
        endif;
    }

    /**
     * Retorna o percentual cadastrado para o plano e o tiṕo de bonificação
     *
     * @param Plano $plano
     * @param string $tipoBonificacao
     * @param int $geracao
     * @return float|int
     * @throws PropelException
     */
    protected function getPercentualBonusPlano(Plano $plano, string $tipoBonificacao, int $geracao)
    {
        $percentual = 0;

        $query = PlanoPercentualBonusQuery::create()
            ->filterByPlano($plano)
            ->filterByTipo($tipoBonificacao)
            ->filterByGeracao($geracao)
            ->findOne();

        if ($query) :
            $percentual = $query->getPercentual() / 100;
        endif;

        return $percentual;
    }

    /**
     * @param string $tipoExtrato
     * @param string $operacao
     * @param float $bonus
     * @param Cliente $cliente
     * @param string $data
     * @param string $observacao
     * @param bool $bloqueado
     * @param array $origemBonificacao - ex: ['pedido_id' => $pedido->getId()], ['distribuicao_id' => $distribuicao->getId()]
     * @throws PropelException
     */
    protected function criaExtratoBonificacao(string $tipoExtrato, string $operacao, float $bonus, Cliente $cliente,
                                              Datetime $data, string $observacao, bool $bloqueado, array $origemBonificacao)
    {
        $extrato = new Extrato();
        $extrato->setTipo($tipoExtrato);
        $extrato->setOperacao($operacao);
        $extrato->setPontos($bonus);
        $extrato->setCliente($cliente);
        $extrato->setByArray($origemBonificacao);
        $extrato->setData($data);
        $extrato->setObservacao($observacao);
        $extrato->setBloqueado($bloqueado);
        $extrato->save();
    }

    /**
     * @return PropelPDO
     */
    public function getCon()
    {
        return $this->con;
    }

    /**
     * @param PropelPDO $con
     */
    public function setCon($con)
    {
        $this->con = $con;
    }

}