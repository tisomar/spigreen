<?php

namespace QPress\Commerce\Desconto\Provider;

/**
 * DescontoProgressivoProvider
 *
 * @author Jorge Vahldick <jorge@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
class DescontoProgressivoProvider implements DescontoProviderInterface
{
    
    /**
     * Descontos de produtos já pesquisados
     * @var array 
     */
    protected $produtosPesquisados = array();
    
    public function getName()
    {
        return 'desconto_progressivo';
    }
    
    public function doCalculoDesconto(\PropelCollection $itens)
    {
        $desconto = 0;
        
        foreach ($itens as $item) {
            
            // Caso o produto tenha sido excluído pelo administrador durante a compra
            if (is_null($item->getProduto()))
                continue;
            
            if (FALSE === $item instanceof \QPress\Commerce\Carrinho\Model\ItemCarrinhoInterface)
                throw new Exception('Os itens pertencentes a coleção devem ser instancia de ItemCarrinhoInterface');
            
            // CALCULA VALOR DOS ADICIONAIS
            $valorAdicional = 0;
            if ($item->countItemAdicionalCarrinhos()) {
                foreach ($item->getItemAdicionalCarrinhos() as $itemAdicional) {
                    $valorAdicional += $itemAdicional->getItemAdicional()->getValor();
                }
            }
            
            $descontoProduto = $this->getDescontoByProduto($item->getProduto(), $item->getQuantidadeRequisitada(), $valorAdicional);
            if ($descontoProduto) {
                $desconto += $descontoProduto;
            }
        }
        
        return $desconto;
    }
    
    /**
     * getDescontoByProduto()
     * Efetuo um cálculo de desconto para um produto específico
     * 
     * @param \Produto $produto
     * @return float
     */
    public function getDescontoByProduto($produto, $quantidade, $valorAdicional = 0)
    {
        $faixas = $this->getFaixasByProduto($produto);
        
        /* @var $faixa \FaixaDesconto */
        foreach ($faixas as $faixa) {
            if ($quantidade >= $faixa->getQuantidadeMinima() && $quantidade <= $faixa->getQuantidadeMaxima())
                return (($produto->getValorComDesconto() + $valorAdicional) * $quantidade) * ($faixa->getDesconto() / 100);
        }
        
        return 0;
    }
    
    /**
     * getFaixaDescontoByProduto()
     * Efetuo um cálculo de desconto para um produto específico
     * 
     * @param \Produto $produto
     * @return mixed
     */
    public function getFaixaDescontoByProduto($produto, $quantidade)
    {
        $faixas = $this->getFaixasByProduto($produto);
        
        /* @var $faixa \FaixaDesconto */
        foreach ($faixas as $faixa) {
            if ($quantidade >= $faixa->getQuantidadeMinima() && $quantidade <= $faixa->getQuantidadeMaxima())
                return $faixa;
        }
        
        return null;
    }
    
    /**
     * getFaixaDescontoAproximadoByProduto()
     * Localiza a próxima faixa de desconto de acordo com os valores atuais
     * 
     * @param \Produto $produto
     * @param integer $quantidade
     * 
     * @return mixed \FaixaDesconto|null 
     */
    public function getNextFaixaDescontoByProduto($produto, $quantidade)
    {
        $faixas = $this->getFaixasByProduto($produto);
        
        /* @var $faixa \FaixaDesconto */
        foreach ($faixas as $faixa) {
            if ($quantidade < $faixa->getQuantidadeMinima())
                return $faixa;
        }
        
        return null;
    }
    
    /**
     * getFaixasByProduto()
     * Localizo as faixas associadas ao produto
     * 
     * @param \Produto $produto
     * @return mixed
     */
    protected function getFaixasByProduto($produto)
    {
        if ($this->produtoHasFaixaDesconto($produto)) {
            $searched = $this->getFaixasByProdutosPesquisados();
            return $searched[$produto->getId()];
        }
        
        return $this->getFaixasByProdutoQuery($produto);
    }
    
    /**
     * getProdutosPesquisados()
     * Localizado um array com produtos já pesquisados
     * 
     * @return array
     */
    protected function getFaixasByProdutosPesquisados()
    {
        return $this->produtosPesquisados;
    }
    
    /**
     * addProdutoPesquisado()
     * Adiciono um produto a listagem de produtos já pesquisados
     * 
     * @param \Produto $produto
     */
    protected function addFaixasProdutoPesquisado($produto, $faixas)
    {
        $this->produtosPesquisados[$produto->getId()] = $faixas;
    }
    
    /**
     * getFaixasByProdutoQuery()
     * Localizo as faixas relacionadas a um produto diretamente da query
     * 
     * @param \Produto $produto
     * @return mixed
     */
    protected function getFaixasByProdutoQuery($produto)
    {
        $fdQuery = \FaixaDescontoQuery::create();
        $fdQuery
            ->joinWith(\ProdutoFaixaDescontoPeer::OM_CLASS, \Criteria::INNER_JOIN)
            ->useProdutoFaixaDescontoQuery()
                ->filterByProduto($produto)
            ->endUse()
            ->orderBy(\FaixaDescontoPeer::QUANTIDADE_MINIMA, \Criteria::ASC)
        ;
        
        $faixas = $fdQuery->find();
        $this->addFaixasProdutoPesquisado($produto, $faixas);
        
        return $faixas;
    }
    
    /**
     * produtoHasFaixaDesconto()
     * Verifico se o produto possui alguma faixa de desconto
     * 
     * @param type $produto
     * @return type
     */
    protected function produtoHasFaixaDesconto($produto)
    {
        return (key_exists($produto->getId(), $this->getFaixasByProdutosPesquisados()));
    }
    
}