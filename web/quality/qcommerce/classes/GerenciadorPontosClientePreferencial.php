<?php


class GerenciadorPontosClientePreferencial
{

    public function getTotalPontosDisponiveis(Cliente $cliente, DateTime $dataInicial = null, DateTime $dataFinal = null)
    {
        $totalPontos = 0;

        $query = ExtratoClientePreferencialQuery::create()
            ->_if($dataInicial)
                ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
            ->_endif()
            ->_if($dataFinal)
                ->filterByData($dataFinal, Criteria::LESS_EQUAL)
            ->_endif()
            ->filterByCliente($cliente)
            ->find();

        /**
         * @var $extrato Extrato
         */
        foreach ($query as $extrato) :
            $totalPontos = $extrato->getOperacao() == '+' ? round($totalPontos + $extrato->getPontos(), 2) :
                round($totalPontos - $extrato->getPontos(), 2);
        endforeach;

        return $totalPontos;
    }

    public function criarExtrato(Pedido $pedido, DateTime $data, string $operacao, float $pontos, string $observacao)
    {
        $extrato  = new ExtratoClientePreferencial();
        $extrato->setCliente($pedido->getCliente());
        $extrato->setPedido($pedido);
        $extrato->setData($data);
        $extrato->setOperacao($operacao);
        $extrato->setPontos($pontos);
        $extrato->setObservacao($observacao);
        $extrato->save();
    }

}