<?php

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_produto_variacao_atributo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoVariacaoAtributoPeer extends BaseProdutoVariacaoAtributoPeer
{

    public static function getOpcoesDisponiveis($criteria, $produto_id)
    {
        $coll = ProdutoVariacaoAtributoQuery::create(null, $criteria)
            ->useProdutoAtributoQuery()
                ->orderByOrdem()
            ->endUse()
            ->useProdutoVariacaoQuery()
                ->filterByProdutoId($produto_id)
            ->endUse()
            ->orderByProdutoVariacaoId()
            ->withColumn('ProdutoAtributo.Descricao', 'ProdutoAtributoDescricao')
            ->groupByProdutoAtributoId()
            ->groupByDescricao()
            ->find();
        
        return $coll;
    }

    /**
     * Retorna uma estrutura com os atributos e seus valores
     * 
     * @param int $produto_id
     * @return array
     * 
     * Exemplo de retorno para produtos com 2 atributos
     *   Array
     *   (
     *       [18] => Array
     *           (
     *               [nome] => Tamanho
     *               [opcoes] => Array
     *                   (
     *                       [166] => G
     *                       [167] => GG
     *                   )
     *
     *           )
     *
     *       [17] => Array
     *           (
     *               [nome] => Cor
     *               [opcoes] => Array
     *                   (
     *                       [166] => Preto + Amarelo
     *                       [170] => Verde
     *                   )
     *
     *           )
     *
     *   )
     */
    public static function getOpcoesDisponiveisToArray($produto_id, $collProdutoVariacaoAtributo = null)
    {

        if ($collProdutoVariacaoAtributo == null)
        {
            $collProdutoVariacaoAtributo = static::getOpcoesDisponiveis(null, $produto_id);
        }

        $atributos = array();

        foreach ($collProdutoVariacaoAtributo as $objProdutoVariacaoAtributo)
        { /* @var $objProdutoVariacaoAtributo ProdutoVariacaoAtributo */

            $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['nome'] = $objProdutoVariacaoAtributo->getProdutoAtributoDescricao();

            $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['id'] = $objProdutoVariacaoAtributo->getProdutoAtributoId();

            $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['is_cor'] = $objProdutoVariacaoAtributo->getProdutoAtributo()->isCor();

            $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['produto_id'] = $objProdutoVariacaoAtributo->getProdutoAtributo()->getProdutoId();

            if ($objProdutoVariacaoAtributo->getPropriedade()) {
                $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['background'][$objProdutoVariacaoAtributo->getProdutoVariacaoId()] = $objProdutoVariacaoAtributo->getPropriedade()->getBackground(32, 32);
            }

            $atributos[$objProdutoVariacaoAtributo->getProdutoAtributoId()]
                    ['opcoes'][$objProdutoVariacaoAtributo->getProdutoVariacaoId()] = $objProdutoVariacaoAtributo->getDescricao();
        }

        return $atributos;
    }

}
