<?php

namespace QPress\Frete\Services\Correios\Servicos;

use QPress\Frete\Package\Package;
use QPress\Frete\Services\Correios\AbstractCorreios;
use QPress\Frete\Services\Correios\Manager\CorreiosManager;
use QPress\Frete\DataResponse\DataResponseFreteInterface;

/**
 * 40096 - Sedex - Com contrato
 */
class Correios40096 extends AbstractCorreios
{
    CONST SERVICE = 40096;

    protected $manager;

    function __construct(CorreiosManager $correiosManager)
    {
        $this->manager = $correiosManager;
    }

    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome()
    {
        return 'correios_' . static::SERVICE;
    }

    /**
     * Retorna o título da modalidade de frete.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo()
    {
        return 'Sedex';
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
        return $this->manager->consultaCorreios($this, $package);
    }

    /**
     * Retorna o código do serviço.
     *
     * @return int
     */
    public function getService() {
        return self::SERVICE;
    }

}
