<?php

namespace QPress\Carrinho\Storage;

interface CarrinhoStorageInterface
{
    /**
     * Returns o id do carrinho atual. É a chave primária do carrinho no DB
     * 
     * @return mixed
     */
    public function getCurrentCarrinhoId();

    /**
     * Seta o id do carrinho atual
     *
     * @param \Carrinho $cart
     */
    public function setCurrentCarrinhoId(\Carrinho $carrinho);

    /**
     * Reseta o carrinho atual.
     * Basicamente, isto significa abandonar o carrinho atual
     */
    public function resetCurrentCarrinhoId();
}