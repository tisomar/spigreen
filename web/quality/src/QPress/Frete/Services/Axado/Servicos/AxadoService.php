<?php

namespace QPress\Frete\Services\Axado\Servicos;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\Package\Package;
use QPress\Frete\FreteInterface;
use QPress\Frete\DataResponse\DataResponseFreteInterface;

class AxadoService implements FreteInterface
{

    private $nome;
    private $titulo;
    private $consultar;

    function __construct($nome, $titulo, DataResponseFrete $consultar)
    {
        $this->consultar = $consultar;
        $this->nome = $nome;
        $this->titulo = $titulo;
    }

    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Retorna o título da modalidade de frete.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Deve fazer a consulta do frete e retornar os resultados no formato
     *
     * @param Package $package Dados de entrada para a consulta do frete.
     *
     * @return DataResponseFreteInterface Retorna um array com o prazo de entrega e o valor do frete
     *
     */
    public function consultar(Package $package)
    {
        return $this->consultar;
    }

}
