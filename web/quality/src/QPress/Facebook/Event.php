<?php

/**
 * This file is part of the QualityPress package.
 *
 * (c) Jorge Vahldick
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace QPress\Facebook;

class Event
{

    const DEFAULT_CURRENCY = 'BRL';

    // Acompanhamento para páginas chave (Ex: página de produtos, detalhes do produto, artigo)
    const EVENT_CONTENT = 'ViewContent';

    // Acompanhamento para página de busca
    const EVENT_SEARCH = 'Search';

    // Acompanhamento de quando um produto é adicionado ao carrinho de compas
    const EVENT_ADD_TO_CART = 'AddToCart';

    // Acompanhar o acréscimo de um produto em uma lista de desejos/casamento
    const EVENT_ADD_TO_WISHLIST = 'AddToWishlist';

    // Acompanhamento para início do processo de checkout (saindo do carrinho para pagamento)
    const EVENT_INIT_CHECKOUT = 'InitiateCheckout';

    // Para acompanhar página de pagamento (onde é inserido dados de pagamento)
    const EVENT_PAYMENT_INFO = 'AddPaymentInfo';

    // Página de confirmação da compra
    const EVENT_PAYMENT_CONFIRMATION = 'Purchase';

    // Interesse do cliente em algo (produto, newsletter, ofertas, contato, etc)
    const EVENT_LEAD = 'Lead';

    // Confirmação de registro do cliente
    const EVENT_REGISTRATION_COMPLETE = 'CompleteRegistration';

    protected $name;
    protected $arguments;

    /**
     * Constructor.
     * Informações padrões para os eventos.
     *
     * @param   string  $name
     * @param   array   $arguments
     */
    public function __construct($name, $arguments = array())
    {
        $this->name         = $name;
        $this->arguments    = $arguments;
    }

    /**
     * Localizar o nome do evento.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retornar argumentos atribuídos ao evento.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Display da linha no formato que deverá ser inserido no javascript.
     *
     * @return string
     */
    public function outputJavaScriptLine()
    {
        if ($this->getName() == self::EVENT_PAYMENT_CONFIRMATION) {
            return $this->getJavascriptPurchaseOutputLine();
        }

        return sprintf("fbq('track', '%s');", $this->getName());
    }

    /**
     * No caso do evento chamado ser purchase, deve haver parâmetros.
     * Este evento é o único que aceita parãmetro.
     *
     * @return string
     */
    protected function getJavascriptPurchaseOutputLine()
    {
        $args = $this->getArguments();

        if (!count($args)) {
            return sprintf("fbq('track', '%s', {value: '0.00', currency: '%s'});", $this->getName(), self::DEFAULT_CURRENCY);
        }

        $nArgs = array();
        foreach ($args as $k => $v ){
            $nArgs[] = sprintf("%s: '%s'", $k, $v);
        }

        return sprintf("fbq('track', '%s', {%s});", $this->getName(), join(', ', $nArgs));
    }

    /**
     * Buscar tag em formato de imagem do evento.
     *
     * Para eventos purchase:
     * <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6027909682143&amp;cd[value]=0.00&amp;cd[currency]=BRL&amp;noscript=1" />
     *
     * Outros:
     * <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1610463355874616&ev=PageView&noscript=1" />
     *
     * @param   string  $facebookId
     * @return  string
     */
    public function outputImageTag($facebookId)
    {
        if ($this->getName() == self::EVENT_PAYMENT_CONFIRMATION) {
            return $this->getImageTagPurchaseLine($facebookId);
        }

        return sprintf('<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=%s&ev=%s&noscript=1" />', $facebookId, $this->getName());
    }

    /**
     * Retornar tag de imagem para evento purchase.
     *
     * @param   string  $facebookId
     * @return  string
     */
    protected function getImageTagPurchaseLine($facebookId)
    {
        $args = $this->getArguments();

        if (!count($args)) {
            return sprintf('<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=%s&ev=%s&cd[value]=0.00&cd[currency]=%s&noscript=1" />', $facebookId, $this->getName(), self::DEFAULT_CURRENCY);
        }

        $nArgs = array();
        foreach ($args as $k => $v ){
            $nArgs[] = sprintf("cd[%s]=%s", $k, $v);
        }

        return sprintf("https://www.facebook.com/tr?id=%s&ev=%s&%s&noscript=1", $facebookId, $this->getName(), join('&', $nArgs));
    }
}