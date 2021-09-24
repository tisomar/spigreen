<?php
namespace QPress\Frete\Services\FreteGratis;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\DataResponse\DataResponseFreteInterface;
use QPress\Frete\Package\Package;

class FreteGratis implements  \QPress\Frete\FreteInterface {


    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome()
    {
        return "frete_gratis";
    }

    /**
     * Retorna o título da modalidade de frete. O título pode ser exibido ao usuário.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo()
    {
        return "Frete Grátis";
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
        $faixa = $this->getFaixa($package->getClient()->getCepTo(), $package->getValor());

        $response = new DataResponseFrete();

        if (!is_null($faixa)) {
            $response->setPrazo($faixa->getPrazoEntrega());
            $response->setValor('0,00');
        } else {
            $response->setDisponivel(false);
        }

        return $response;
    }

    /**
     * @param $cep
     * @param $peso
     * @return \TransportadoraFaixaPeso
     */
    private function getFaixa($cep, $valor) {
        return \RegiaoFreteGratisQuery::create()
            ->filterByCepInicial(array('max' => $cep))
            ->filterByCepFinal(array('min' => $cep))
            ->filterByValorMinimo(array('max' => $valor))
            ->filterByIsAtivo(true)
            ->find()
            ->getFirst();
    }

}