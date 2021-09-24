<?php


class BonificacaoEcommerce extends GerenciadorBonificacao implements BonificacaoEcommerceInterface
{
    /**
     * @param Pedido $pedido
     * @throws PropelException
     */
    public function distribuiBonus(Pedido $pedido)
    {
        if (empty($pedido->getHotsiteClienteId())) :
            return;
        endif;

        $cliente = $pedido->getCliente();
        $distribuidor = $cliente->getClienteRelatedByClienteIndicadorId();

        if ($this->existeExtrato(Extrato::TIPO_VENDA_HOTSITE, $pedido, $distribuidor)) :
            return;
        endif;

        $plano = $distribuidor->getPlano();

        if (($plano->getPercDescontoHotsite() ?? 0) <= 0) :
            return;
        endif;

        $valorItens = 0;

        foreach ($pedido->getPedidoItems() as $item) :
            $valorUnitario = $item->getValorUnitario();
            $valorBase = $item->getProdutoVariacao()->getValorBase();

            if (!$item->getProdutoVariacao()->getProduto()->isKitAdesao() && ($valorUnitario === $valorBase)) :
                $valorItens += $item->getValorUnitario() * $item->getQuantidade();
            endif;
        endforeach;

        if($valorItens > 0) :
            $bonusEcommerce = $valorItens * $plano->getPercDescontoHotsite() / 100;
            $observacao = "Bônus Hotsite. Pedido: {$pedido->getId()} - Cliente {$cliente->getNomeCompleto()}";
            $data = new Datetime();

            $this->criaExtratoBonificacao(
                Extrato::TIPO_VENDA_HOTSITE,
                '+',
                ceil($bonusEcommerce * 100) / 100,
                $distribuidor,
                $data,
                $observacao,
                ClientePeer::getClienteAtivoMensal($distribuidor->getId()),
                [
                    'PEDIDO_ID' => $pedido->getId()
                ]
            );
        endif;
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
            ->filterByTipo($tipoBonificacao);

        if (!empty($cliente)) :
            $query->filterByCliente($cliente);
        endif;

        return $query->count($this->con) > 0;
    }
}