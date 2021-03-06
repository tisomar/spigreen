<?php

namespace QPress\Container;

use Integrations\Manager\IntegrationManager;
use Integrations\Models\Bling\Bling;
use QPress\Component\Association\Product\Type\VendaCruzadaType;
use QPress\Component\Association\Product\Type\ProdutoRelacionadoType;
use QPress\Frete\Manager\FreteManager;
use QPress\Frete\Services\Correios\Manager\CorreiosManager;
use QPress\Frete\Services\Axado\Manager\AxadoManager;

//use QPress\Frete\Services\Correios\Servicos\Correios40010 as SedexSemContrato;
//use QPress\Frete\Services\Correios\Servicos\Correios41106 as PacSemContrato;
//use QPress\Frete\Services\Correios\Servicos\Correios40096 as SedexComContrato;
//use QPress\Frete\Services\Correios\Servicos\Correios41068 as PacComContrato;

/*
 * Novos serviços
 */

use QPress\Frete\Services\Correios\Servicos\Correios04510 as PacSemContrato;
use QPress\Frete\Services\Correios\Servicos\Correios04014 as SedexSemContrato;

use QPress\Frete\Services\Correios\Servicos\Correios04162 as SedexComContrato;
use QPress\Frete\Services\Correios\Servicos\Correios04669 as PacComContrato;

use QPress\Frete\Services\Correios\Servicos\Correios81019 as ESedexComContrato;

use QPress\Carrinho\Provider\CarrinhoProvider;
use QPress\Frete\Services\FreteGratis\FreteGratis;
use QPress\Frete\Services\RetiradaLoja\RetiradaLoja;
use QPress\Frete\Services\Transportadora\FreteTransportadora;
use QPress\Frete\Services\Gollog\FreteGollog;
use QPress\Frete\Services\TG\FreteTG;
use QPress\Gateway\Manager\GatewayManager;
use QPress\Gateway\Services\CieloApi30\CieloApi;
use QPress\Gateway\Services\ItauShopline\ItauShopline;
use QPress\Gateway\Services\PagSeguroTransparente\PagSeguroTransparente;
use QPress\Gateway\Services\PayPal\PayPal;
use QPress\Gateway\Services\SuperPayRest\SuperPayRest;
use QPress\Gateway\Services\PagSeguro\PagSeguro;
use QPress\Gateway\Services\BoletoPHP\BoletoPHP;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer
{

    public function __construct(array $values = array())
    {

        $app = $this;

        /**
         * Session
         */
        $app['session'] = function () {
            $session = new Session();
            $session->start();
            return $session;
        };

        /**
         * Request
         */
        $app['request'] = function () {
            return Request::createFromGlobals();
        };

        /**
         * Configurações do sistema
         */
        $app['config'] = function ($app) {

            $config = new \Config($app);
            $config->loadParameters();

            return $config;
        };

        /**
         * CorreiosManager
         */
        $app['correios_manager'] = function ($app) {
            return new CorreiosManager(\Config::get('correios_codigo_contrato'), \Config::get('correios_senha_contrato'));
        };

        /**
         * AxadoManager
         */
        $app['axado_manager'] = function ($app) {
            return new AxadoManager(\Config::get('axado_token'));
        };

        /**
         * FreteManager
         */
        $app['frete_manager'] = function ($app) {

            $frete_manager = new FreteManager();

            $frete_manager->addModalidade(new FreteGratis());

            /* Quando estiver no painel admin pode carregar todos os fretes */
            if (strpos($app['request']->server->get('REQUEST_URI'), '/admin') !== false) {
                $frete_manager->addModalidade(new PacComContrato($app['correios_manager']));
                $frete_manager->addModalidade(new SedexComContrato($app['correios_manager']));
                $frete_manager->addModalidade(new ESedexComContrato($app['correios_manager']));

                $frete_manager->addModalidade(new PacSemContrato($app['correios_manager']));
                $frete_manager->addModalidade(new SedexSemContrato($app['correios_manager']));

                $frete_manager->addModalidade(new FreteTransportadora());
                $frete_manager->addModalidade(new FreteGollog());
                $frete_manager->addModalidade(new FreteTG());
            } else {
                /**
                 * Frete com Correios (com contrato)
                 */
                if (\Config::get('correios_codigo_contrato') != '' && \Config::get('correios_senha_contrato') != '') {
                    if (\Config::get('has_correios_pac')) {
                        $frete_manager->addModalidade(new PacComContrato($app['correios_manager']));
                    }

                    if (\Config::get('has_correios_sedex')) {
                        $frete_manager->addModalidade(new SedexComContrato($app['correios_manager']));
                    }

                    if (\Config::get('has_correios_esedex')) {
                        $frete_manager->addModalidade(new ESedexComContrato($app['correios_manager']));
                    }
                }
                /**
                 * Frete com Correios (sem contrato)
                 */
                else {
                    if (\Config::get('has_correios_pac')) {
                        $frete_manager->addModalidade(new PacSemContrato($app['correios_manager']));
                    }

                    if (\Config::get('has_correios_sedex')) {
                        $frete_manager->addModalidade(new SedexSemContrato($app['correios_manager']));
                    }
                }

                $frete_manager->addModalidade(new FreteTransportadora());
                $frete_manager->addModalidade(new FreteGollog());
                $frete_manager->addModalidade(new FreteTG());
            }

            $frete_manager->addModalidade(new RetiradaLoja());

            return $frete_manager;
        };

        /**
         * CarrinhoProvider
         */
        $app['carrinho_provider'] = function ($app) {
            return new CarrinhoProvider($app['session'], $app['frete_manager']);
        };

        /**
         * GatewayManager
         */
        $app['gateway_manager'] = function () {
            $gateway_manager = new GatewayManager();

            /**
             * SuperPay
             */
            $ambiente = \Config::get('superpay.ambiente');
            $gateway_manager->register(new SuperPayRest(\Config::get('superpay.codigo_estabelecimento_' . $ambiente), $ambiente));

            $ambiente = \Config::get('cielo.ambiente');
            $gateway_manager->register(new CieloApi($ambiente));

            /**
             * PagSeguro
             */
            $isPagSeguroTransparentCheckout = \Config::get('pagseguro.opcao_pagamento') == 'transparente';
            if ($isPagSeguroTransparentCheckout) {
                $gateway_manager->register(new PagSeguroTransparente(
                    \Config::get('pagseguro_email'),
                    \Config::get('pagseguro_token'),
                    'production'
                ));
            } else {
                $gateway_manager->register(new PagSeguro(\Config::get('pagseguro_email'), \Config::get('pagseguro_token')));
            }

            /**
             * Boleto PHP
             */
            $gateway_manager->register(new BoletoPHP());

            /**
             * PayPal
             */
            $gateway_manager->register(new PayPal(
                \Config::get('paypal.ambiente'),
                \Config::get('paypal.username'),
                \Config::get('paypal.password'),
                \Config::get('paypal.signature')
            ));

            /**
             * Itau Shopline
             */
            $gateway_manager->register(new ItauShopline());

            return $gateway_manager;
        };

        $app['venda.cruzada'] = function () {
            return new VendaCruzadaType();
        };

        $app['produto.relacionado'] = function () {
            return new ProdutoRelacionadoType();
        };

        /**
         * Integrations / Bling
         *
         * @param $app
         * @return IntegrationManager
         */
        $app['integration_manager'] = function ($app) {
            $integration_manager = new IntegrationManager();
            $integration_manager->register(new Bling());

            return $integration_manager;
        };

        parent::__construct($values);
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this['session'];
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this['request'];
    }

    /**
     * @return CarrinhoProvider
     */
    public function getCarrinhoProvider()
    {
        return $this['carrinho_provider'];
    }

    /**
     * @return CorreiosManager
     */
    public function getCorreiosManager()
    {
        return $this['correios_manager'];
    }

    /**
     * @return AxadoManager
     */
    public function getAxadoManager()
    {
        return $this['axado_manager'];
    }

    /**
     *
     * @return FreteManager
     */
    public function getFreteManager()
    {
        return $this['frete_manager'];
    }

    /**
     *
     * @return GatewayManager
     */
    public function getGatewayManager()
    {
        return $this['gateway_manager'];
    }

    /**
     *
     * @return IntegrationManager
     */
    public function getIntegrationManager()
    {
        return $this['integration_manager'];
    }

    /**
     *
     * @return \Config
     */
    public function getConfig()
    {
        return $this['config'];
    }
}
