<?php


interface BonificacaoPedidoInterface
{
    public function distribuirBonus(Pedido $pedido);
}