<?php
namespace QPress\Frete;

use QPress\Frete\Package\Package;

interface FreteInterface
{
    /**
     * Deve retornar o nome da modalidade de frete.
     *
     * @return string O nome da modalidade de frete.
     */
    public function getNome();

    /**
     * Retorna o título da modalidade de frete. O título pode ser exibido ao usuário.
     *
     * @return string O título da modalidade de frete.
     */
    public function getTitulo();

    /**
     * Deve fazer a consulta do frete e retornar os resultados no formato.
     *
     * Returns calculated shipping costs.
     * 
     * @param array $dados para a consulta do frete.
     * @return DataResponse\DataResponseFreteInterface
     */
    public function consultar(Package $dados);
}
