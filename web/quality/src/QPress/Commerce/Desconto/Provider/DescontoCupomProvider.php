<?php

namespace QPress\Commerce\Desconto\Provider;

/**
 * DescontoCupomProvider
 *
 * @author Rodrigo Antunes <rodrigo@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
class DescontoCupomProvider implements DescontoProviderInterface
{
    
    /* Código do Cupom a ser utilizado
     * @var int
     */
    protected $codigoCupom;
    
    /**
     * getName()
     * Nome do provedor de desconto
     * 
     * @return string
     */
    public function getName()
    {
        return 'desconto_cupom';
    }
    
    /**
     * doCalculoDesconto()
     * Efetua o cálculo de desconto por uma listagem de itens passadas por parâmetro
     * 
     * @param \PropelCollection $itens
     * @return double
     * @throws Exception Caso os itens não forem instancias de itens do carrinho
     */
    public function doCalculoDesconto(\PropelCollection $itens)
    {
        $valorTotalItens = 0;

        foreach ($itens as $item) /* @var $item \ItemCarrinho */
        {
            if (FALSE === $item instanceof \QPress\Commerce\Carrinho\Model\ItemCarrinhoInterface)
                throw new Exception('Os itens pertencentes a coleção devem ser instancia de ItemCarrinhoInterface');
            
            $valorTotalItens += $item->getValorTotalComAdicionais();
        }
        
        $objCupom = \CupomQuery::create()->findOneByCupom($this->codigoCupom);
            
        $valorDesconto = $objCupom->getValorDesconto();
        $tipoDesconto = $objCupom->getTipoDesconto();
        
        $valorCarrinhoOK = false;
        if (!is_null($objCupom->getValorMinimoCarrinho())) {
            if ($valorTotalItens >= $objCupom->getValorMinimoCarrinho()) {
                $valorCarrinhoOK = true;
            } else {
                unset($_SESSION['pedido_info']['cupom']);
            }
        }
        
        if ($valorCarrinhoOK) {
            if ($tipoDesconto == \Cupom::TIPO_REAL) {
                if ($valorDesconto <= $valorTotalItens)
                    return $valorDesconto;
                else
                    return $valorTotalItens;
            } else {
                return (($valorTotalItens * $valorDesconto) / 100);
            }
        } else {
            return 0;
        }
    }
    
    public function setCupomDesconto($codigoCupom)
    {
        $this->codigoCupom = $codigoCupom;
        return $this;
    }
    
}

?>