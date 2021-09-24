<?php
namespace QPress\Frete\Package;

class PackageItem {

    /**
     * Identificador do item
     * @var int|string
     */
    private $identifier;

    /**
     * Peso em gramas
     * @var int
     */
    private $peso;

    /**
     * Altura em centímetros
     * @var int
     */
    private $altura;

    /**
     * Largura em centímetros
     * @var int
     */
    private $largura;

    /**
     * Comprimento em centímetros
     * @var int
     */
    private $comprimento;

    /**
     * Quantidade de itens
     * @var int
     */
    private $quantidade;

    /**
     * Valor total do item contando a quantidade e demais regras como
     * desconto progressivo, etc.
     * @var int
     */
    private $valor;

    /**
     * Controi um novo item
     *
     * @param $id
     * @param $peso
     * @param $altura
     * @param $comprimento
     * @param $largura
     * @param $quantidade
     * @param $valor
     */
    function __construct($id, $peso, $altura, $comprimento, $largura, $quantidade, $valor)
    {
        $this->identifier = $id;
        $this->peso = $peso;
        $this->altura = $altura;
        $this->comprimento = $comprimento;
        $this->largura = $largura;
        $this->quantidade = $quantidade;
        $this->valor = $valor;
    }

    /**
     * @param int $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * @param int $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return int
     */
    public function getValor()
    {
        return $this->valor;
    }


}