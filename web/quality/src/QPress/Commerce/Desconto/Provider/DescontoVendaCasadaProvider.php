<?php

namespace QPress\Commerce\Desconto\Provider;

/**
 * DescontoVendaCasadaProvider
 *
 * @author Jorge Vahldick <jorge@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
class DescontoVendaCasadaProvider extends DescontoProgressivoProvider
{

    /**
     * Itens pertencentes ao carrinho
     * @var \PropelCollection
     */
    protected $itens;
    
    /**
     * Registro das vendas casadas junto aos seus produtos
     * @var array()
     */
    protected $vendasCasadas = array();
    
    public function getName()
    {
        return 'venda_casada';
    }
    
    /**
     * @param \PropelCollection $itens
     * @version 1.1 Alteração na "maneira de clonar" os itens, devido ao problema
     * do clone manter o original e assim alterá-lo, ocasionando erro
     */
    public function doCalculoDesconto(\PropelCollection $itens)
    {
        $arrItens = new \PropelCollection();
        foreach ($itens as $item) {
            $clone = clone $item;
            $arrItens->append($clone);
        }
        $this->itens = $arrItens;
        $this->doInitCalls();
        
        return $this->getValorDesconto();
    }
    
    protected function doInitCalls()
    {
        $this->registrarVendas();
    }

    /**
     * getValorDesconto()
     * Efetuo métodos necessários e efetuo cálculo para o desconto
     * 
     * @return real
     * @version 1.1 Correção para efetuar o cálculo baseado também nas quantidades
     */
    public function getValorDesconto()
    {
        $desconto = 0.00;
        foreach ($this->getVendasCasadas() as $key => $arrProdutosIds) {
            if (FALSE === $this->hasConflitoProdutos($arrProdutosIds)) {
                $quantidade = 0;
                foreach (\QPress\Commerce\Carrinho\Operator\CarrinhoOperator::getInstance()->getCarrinho()->getItens() as $itemCarrinho) {
                    if ($itemCarrinho->getProdutoId() == $arrProdutosIds[0] || $itemCarrinho->getProdutoId() == $arrProdutosIds[1]) {
                        $quantidade = ($quantidade > 0 && $quantidade < $itemCarrinho->getQuantidadeRequisitada()) ? $quantidade : $itemCarrinho->getQuantidadeRequisitada();
                    }
                }
                
                $desconto += ($this->getValorProdutos($key, $quantidade) - $this->getDescontoVendaCasada($key, $quantidade));
            }
        }
        
        return $desconto;
    }
    
    /**
     * getDescontoVendaCasada()
     * Localiza o valor de desconto de uma venda casada
     * 
     * @param integer $vendaCasada
     * @return double
     */
    protected function getDescontoVendaCasada($vendaCasada, $quantidade = 1)
    {
        $query = 'SELECT SUM(' . \VendaCasadaPeer::VALOR . ') * '. $quantidade .' AS TOTAL_DESCONTO FROM ' . \VendaCasadaPeer::TABLE_NAME . ' WHERE ' . \VendaCasadaPeer::ATIVO . ' IS TRUE AND ' . \VendaCasadaPeer::ID . ' = ' . $vendaCasada;
        $propel = \Propel::getConnection();
        $stmt   = $propel->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchObject()->TOTAL_DESCONTO;
    }
    
    /**
     * getValorProdutos()
     * Soma o valor dos produtos através de um parâmetro com id de venda casada
     * 
     * @param mixed array|int $vendasCasadas
     * @return double
     */
    protected function getValorProdutos($vendaCasada, $quantidade = 1)
    {
        $valorProdutos = 0.00;
        $produtos = \ProdutoQuery::create()->useProdutoVendaCasadaQuery()->filterByVendaCasadaId($vendaCasada)->endUse()->find();
        
        /* @var $produto \Produto */
        foreach ($produtos as $produto) {
            $valorProdutos += $produto->getValorComDesconto();
        }
        
        return $valorProdutos * $quantidade;
    }
    
    /**
     * getItens()
     * Localiza os itens registrados junto ao carrinho
     * 
     * @return \PropelCollection
     */
    protected function getItens()
    {
        return $this->itens;
    }
    
    /**
     * registrarVendas()
     * Guardo em um array os produtos que possuem combinação válida
     * interna ao carrinho
     */
    protected function registrarVendas()
    {
        $vendasCasadas = $this->getVendasForItens(); 

        foreach ($vendasCasadas as $vendaCasada) {
            $combinacao = array();            
            $arrVendasProdutos = \ProdutoVendaCasadaQuery::create()->filterByVendaCasada($vendaCasada)->find();
            foreach ($arrVendasProdutos as $item) {
                $combinacao[] = $item->getProdutoId();
            }
            
            if (TRUE === $this->hasCombinacaoExistenteForProdutos($combinacao)) {
                $this->vendasCasadas[$vendaCasada->getId()] = $combinacao;
            }
        }
    }
    
    /**
     * getVendasCasadas()
     * Localizo o array de vendas casadas registradas
     * 
     * @return array
     */
    protected function getVendasCasadas()
    {
        return $this->vendasCasadas;
    }
    
    /**
     * getVendasForItens()
     * Localização de todos as vendas casadas registradas aos itens
     * presentes aos itens do carrinho
     * 
     * @return \PropelCollection
     */
    protected function getVendasForItens()
    {
        $produtosIds = $this->getArrayProdutosIds();
        return \VendaCasadaQuery::create()
                ->filterByAtivo(true)
                ->joinWith(\ProdutoVendaCasadaPeer::OM_CLASS, \Criteria::INNER_JOIN)
                ->add(\ProdutoVendaCasadaPeer::PRODUTO_ID, $produtosIds, \Criteria::IN)
                ->orderByValor(\Criteria::DESC)
                ->find();
    }
    
    /**
     * getArrayProdutosIds()
     * Percorrer a listagem de itens e armazenar os id´s dos produtos
     * 
     * @return array
     */
    protected function getArrayProdutosIds()
    {
        $arr = array();
        foreach ($this->getItens() as $item)
            $arr[] = $item->getProdutoId();
        
        return $arr;
    }


    /**
     * hasCombinacaoExistenteForProdutos()
     * Verifico se nos itens há a combinação para 
     * 
     * @param array $combinacao
     * @return boolean
     */
    public function hasCombinacaoExistenteForProdutos($combinacao)
    {        
        $encontrados = 0;
        foreach ($this->getItens() as $item) {
            if (TRUE === in_array($item->getProdutoId(), $combinacao))
                $encontrados++;
        }
        
        return ($encontrados === count($combinacao) && count($combinacao));
    }
    
    /**
     * hasConflitoProdutos()
     * Verifica se a listagem de produtos está em conflito
     * 
     * @param array $produtosIds
     * @return boolean
     */
    public function hasConflitoProdutos($produtosIds)
    {
        foreach ($this->getItens() as $key => $item) {
            if (TRUE === in_array($item->getProdutoId(), $produtosIds)) {
                if ($item->getQuantidadeRequisitada() == 0)
                    return true;
                
                $item->setQuantidadeRequisitada($item->getQuantidadeRequisitada() - 1);
                $this->itens[$key] = $item;
            }
        }
        
        return false;
    }
    
}