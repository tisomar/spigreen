<?php

namespace QPress\Commerce\Desconto\Operator;

/**
 * DescontoOperatorInterface
 *
 * @author Jorge Vahldick <jorge@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
interface DescontoOperatorInterface
{
 
    /**
     * getInstance()
     * Percorre a listagem de provedores armazenando-os no objeto retornando
     * a seguir o próprio objeto
     * 
     * @return DescontoOperator
     */
    public static function getInstance();
    
    /**
     * calcularDescontosFromProviders()
     * Efetuar cálculo de descontos através de uma listagem de itens e um
     * array de provedores dos quais passados por array
     * 
     * @param \PropelCollection $itens
     * @param array $arrayProviders
     * 
     * @return real
     */
    function calcularDescontosFromProviders(\PropelCollection $itens, $arrayProviders);
    
    /**
     * getProvider()
     * Localiza um provedor de Desconto, retornando-o
     * 
     * @param string $provider
     * @return mixed \QPress\Commerce\Desconto\Provider\DescontoProviderInterface|null
     */
    function getProvider($provider);
    
    /**
     * calcularDesconto()
     * Efetuar o cálculo de desconto passando um array de itens e um provedor
     * 
     * @param \PropelCollection $itens
     * @param string $provider
     * @return real
     */
    function calcularDesconto(\PropelCollection $itens, $provider);
    
}

?>
