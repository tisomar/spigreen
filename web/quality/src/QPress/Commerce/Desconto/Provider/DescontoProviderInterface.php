<?php

namespace QPress\Commerce\Desconto\Provider;

/**
 * DescontoProviderInterface
 *
 * @author Jorge Vahldick <jorge@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
interface DescontoProviderInterface
{
    
    /**
     * doCalculoDesconto()
     * Efetua o cálculo de desconto por uma listagem de itens passadas por parâmetro
     * 
     * @param \PropelCollection $itens
     * @return double
     * @throws Exception Caso os itens não forem instancias de itens do carrinho
     */
    public function doCalculoDesconto(\PropelCollection $itens);
    
    /**
     * getName()
     * Nome do provedor de desconto
     * 
     * @return string
     */
    public function getName();
    
}
