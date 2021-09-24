<?php
namespace QPress\Frete\Services\RetiradaLoja;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\DataResponse\DataResponseFreteInterface;
use QPress\Frete\Package\Package;

class RetiradaLoja implements  \QPress\Frete\FreteInterface {


    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome()
    {
        return "retirada_loja";
    }

    /**
     * Retorna o título da modalidade de frete. O título pode ser exibido ao usuário.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo()
    {
        return "Retirada na loja";
    }

    /**
     * Deve fazer a consulta do frete e retornar os resultados no formato
     *
     * @param Package $package Dados de entrada para a consulta do frete.
     *
     * @return DataResponseFreteInterface Retorna um array com o prazo de entrega e o valor do frete
     *
     */
    public function consultar(Package $package = null)
    {
        $response = new DataResponseFrete();

        $opcao = \RetiradaLojaQuery::create()
            ->filterByHabilitado()
            ->orderByValor()
            ->orderByPrazo()
            ->findOne();

        if (is_null($opcao)) {
            $response->setDisponivel(false);
        } else {
            $response->setDisponivel(true);
            $response->setPrazo($opcao->getPrazo());
            $response->setValor($opcao->getValor());
        }

        return $response;
    }

}