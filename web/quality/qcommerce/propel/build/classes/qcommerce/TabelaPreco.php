<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_tabela_preco' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class TabelaPreco extends BaseTabelaPreco
{

    public function setPorcentagem($v) {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' ', '%'), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setPorcentagem($v);
    }

    /**
     * Após salvar, é necessário verificar os registros da tabela PRODUTO_VARIACAO
     * que não estão associados à esta tabela para incluí-los.
     *
     * @param PropelPDO $con
     */
    public function postSave(PropelPDO $con = null) {
        TabelaPrecoPeer::addProdutoVariacaoNaoAdicionado($this);
    }

}
