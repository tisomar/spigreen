<?php


class BonificacaoFrete extends GerenciadorBonificacao implements BonificacaoPedidoInterface
{

    /**
     * BonificacaoFrete constructor.
     * @param PropelPDO|null $con
     */
    public function __construct(PropelPDO $con = null)
    {
        parent::__construct($con);
    }

    public function distribuirBonus(Pedido $pedido)
    {
        // Bônus será gerado apenas para DIS ou Cliente Preferencial
        if ($pedido->getCliente()->isClienteFinal()) :
            return;
        endif;

        // Bônus será gerado apenas se os pontos do pedido
        // forem maiores que a pontuação mínima configurada
        if ($pedido->getValorPontos() < (int) Config::get('pontos_minimos_bonus_frete')) :
            return;
        endif;

        // Para receber o bônus o pedido deve ter algum valor
        if ($pedido->getValorEntrega() <= 0) :
            return;
        endif;

        // Bônus não será gerado para pedidos com frete
        // para as cidades de Cuiabá (id 1372) e Várzea Grande (id 1460)
        if (in_array($pedido->getEndereco()->getCidade()->getId(), [1372, 1460])) :
            return;
        endif;

        $this->criaExtratoBonificacao(
            Extrato::TIPO_BONUS_FRETE,
            '+',
            $pedido->getValorEntrega(),
            $pedido->getCliente(),
            new DateTime(),
            "Bônus promocional por compra de acessório. Pedido {$pedido->getId()}",
            false,
            ['PEDIDO_ID' => $pedido->getId()]
        );
    }

}