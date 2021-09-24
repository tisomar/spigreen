<?php


interface BonificacaoEcommerceInterface
{
    function distribuiBonus(Pedido $pedido);
}