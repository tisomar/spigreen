<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_VENDA_CASADA' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class VendaCasada extends BaseVendaCasada
{
    
    /**
     * Formata o valor da venda casada para apresentação
     * @return string
     */
    public function getValorFormatado()
    {
        return format_number($this->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES);
    }
    
    /**
     * Retorna a url para os detalhes de um produto
     */
    public function getUrlVendaCasada()
    {
        return ROOT_PATH . '/produtos/comprar-juntos/index/' . escape($this->getId()) . '/';
    }
    
    /**
     * Verifica se a venda casada possui todos os produtos ativos e nenhum produto deletado,
     * evitando exibir uma venda casada incorretamente
     * 
     * @return boolean True se a venda casada possui todos os produtos ativos e não deletados
     */
    public function hasProdutosValidos()
    {
        $produtosVendaCasada = $this->getProdutoVendaCasadasJoinProduto();
        
        foreach ($produtosVendaCasada AS $objVendaProduto)
        {
            if ($objVendaProduto->getProduto()->getAtivo() == false || $objVendaProduto->getProduto()->getDataExclusao() !== null)
            {
                return false;
            }
        }
        return true;
    }
    
}
