<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_INTEGRACAO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class IntegracaoPeer extends BaseIntegracaoPeer
{

    const INTEGRACAO_UOL = 'UOL';
    const INTEGRACAO_GOOGLE = 'GOOGLE';
    const INTEGRACAO_BUSCAPE = 'BUSCAPE';
    
    /**
     * Verifica se o produto já está na tabela de integração
     * @return bool (true SIM, false NAO)
     */
    public static function verificarIntegracao($produtoId, $tipo)
    {
        //Realizando Query
        $arrProdutoIntegracao = IntegracaoQuery::create()
                ->filterByProdutoId($produtoId)
                ->filterByTipo($tipo)
                ->find();

        if ($arrProdutoIntegracao->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
