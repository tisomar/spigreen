<?php
namespace QPress\Frete\Services\Transportadora;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\DataResponse\DataResponseFreteInterface;
use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;

class FreteTransportadora implements FreteInterface {

    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome()
    {
        return "transportadora";
    }

    /**
     * Retorna o título da modalidade de frete. O título pode ser exibido ao usuário.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo()
    {
        return "Transportadora";
    }

    /**
     * Deve fazer a consulta do frete e retornar os resultados no formato
     *
     * @param Package $dados para a consulta do frete.
     *
     * @return DataResponseFreteInterface
     */
    public function consultar(Package $package)
    {
        $faixa = $this->getFaixa($package->getClient()->getCepTo(), $package->getPeso());
        $response = new DataResponseFrete();

        if (!is_null($faixa)) {
            $response->setPrazo($faixa->getPrazoEntrega());
            $valor = $faixa->calcularFrete($package->getValor(), $package->getPeso());
            $response->setValor(format_money($valor));
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
    private function getFaixa($cep, $peso) {
        return \TransportadoraFaixaPesoQuery::create()
            ->useTransportadoraRegiaoQuery()
                ->filterByCepInicial(array('max' => $cep))
                ->filterByCepFinal(array('min' => $cep))
                ->filterByIsAtivo(true)
            ->endUse()
            ->filterByPeso(array('max' => $peso))
            ->orderByPeso('DESC')
            ->findOne()
        ;
    }

}