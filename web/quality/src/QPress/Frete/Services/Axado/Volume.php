<?php

namespace QPress\Frete\Services\Axado;

use QPress\Frete\Package\PackageItem;

class Volume {

    private $sku, $preco = 0.00, $quantidade = 0, $altura = 0, $comprimento = 0, $largura = 0, $peso = 0;

    function __construct(PackageItem $v)
    {
        if (is_array($v) || is_object($v)) {
            $this->setSku($v->getIdentifier());
            $this->setPreco($v->getValor());
            $this->setQuantidade($v->getQuantidade());
            $this->setAltura($v->getAltura());
            $this->setComprimento($v->getComprimento());
            $this->setLargura($v->getLargura());
            $this->setPeso($v->getPeso() / 1000);
        }
    }

    function toArray()
    {
        return array(
            'sku' => $this->getSku(),
            'preco' => $this->getPreco(),
            'quantidade' => $this->getQuantidade(),
            'altura' => $this->getAltura(),
            'comprimento' => $this->getComprimento(),
            'largura' => $this->getLargura(),
            'peso' => $this->getPeso(),
        );
    }

    /**
     * @param int $altura
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;
    }

    /**
     * @return int
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * @param int $comprimento
     */
    public function setComprimento($comprimento)
    {
        $this->comprimento = $comprimento;
    }

    /**
     * @return int
     */
    public function getComprimento()
    {
        return $this->comprimento;
    }

    /**
     * @param int $largura
     */
    public function setLargura($largura)
    {
        $this->largura = $largura;
    }

    /**
     * @return int
     */
    public function getLargura()
    {
        return $this->largura;
    }

    /**
     * @param int $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    /**
     * @return int
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param float $preco
     */
    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    /**
     * @return float
     */
    public function getPreco()
    {
        return $this->preco;
    }

    /**
     * @param int $quantidade
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }

    /**
     * @return int
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }



} 