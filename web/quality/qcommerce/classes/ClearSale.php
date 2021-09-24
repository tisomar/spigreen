<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClearSale
 *
 * @author Garlini
 */
class ClearSale
{
    const SITUACAO_APROVADO  = 'APROVADO';
    const SITUACAO_REPROVADO = 'REPROVADO';
    const SITUACAO_NEUTRO    = 'NEUTRO';
    
    private static $cacheStatus = array();

    /**
     *
     * @return string
     */
    public static function getUrlIntegracao()
    {
        return (Config::get('clearsale.ambiente') === 'production') ?
            '//www.clearsale.com.br/start/Entrada/EnviarPedido.aspx' :
            '//homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx';
    }

    public static function getToken()
    {
        $env    = Config::get('clearsale.ambiente');
        $token  = Config::get('clearsale.codigo_integracao_' . $env);
        return $token;
    }

    /**
     *
     * @param Pedido $pedido
     * @return string
     */
    public static function getUrlVerificacaoStatusPedido(Pedido $pedido)
    {
        return self::getUrlIntegracao() . '?codigointegracao=' . self::getToken() . '&PedidoID=' . $pedido->getId();
    }

    /**
     *
     * @param Pedido $pedido
     * @return bool
     * @throws RuntimeException
     */
    public static function isPedidoAprovado(Pedido $pedido)
    {
        return self::getCachedStatusPedido($pedido) === self::SITUACAO_APROVADO;
    }

    /**
     *
     * @param Pedido $pedido
     * @return bool
     * @throws RuntimeException
     */
    public static function isPedidoReprovado(Pedido $pedido)
    {
        return self::getCachedStatusPedido($pedido) === self::SITUACAO_REPROVADO;
    }

    /**
     *
     * @param Pedido $pedido
     * @return string|null
     * @throws RuntimeException
     */
    public static function getStatusPedido(Pedido $pedido)
    {
        //Aqui vamos fazer uma gambiarra, pois a Clear Sale não disponibilizou uma API e teremos que trabalhar com o HTML retornado na url abaixo.
        
        $urlConsulta = (is_ssl() ? 'https:' : 'http:') . self::getUrlVerificacaoStatusPedido($pedido);
                
        $contents = file_get_contents($urlConsulta);
        if (false === $contents) {
            throw new RuntimeException("Não foi possivel consultar o status do pedido {$pedido->getId()}");
        }

        $doc = new DOMDocument();
        if (false === $doc->loadHTML($contents)) {
            throw new RuntimeException("Não foi possivel carregar o HTML do status do pedido {$pedido->getId()}");
        }

        //Vamos procurar por um elemento com id "PnlStatus". Se um dia a Clear Sale mudar o formato do HTML essa integração provavelmente deixara de funcionar.
        //TODO: verificar se desponibilizaram uma API.
        $elStatus = $doc->getElementById('PnlStatus');
        if (!$elStatus) {
            return null;
        }

        //Pega o atributo "class" e verifica se ele indica reprovação ou aprovação.
        if (!$elStatus->hasAttribute('class')) {
            return null;
        }

        $class = $elStatus->getAttribute('class');

        if (strpos($class, 'status-definido') === false) {
            return null;
        }

        if (strpos($class, 'status-positivo') !== false) {
            return self::SITUACAO_APROVADO;
        } elseif (strpos($class, 'status-negativo') !== false) {
            return self::SITUACAO_REPROVADO;
        }

        return self::SITUACAO_NEUTRO;
    }
    
    public static function getCachedStatusPedido(Pedido $pedido)
    {
        if (array_key_exists($pedido->getId(), self::$cacheStatus)) {
            return self::$cacheStatus[$pedido->getId()];
        }
        $status = self::getStatusPedido($pedido);
        self::$cacheStatus[$pedido->getId()] = $status;
        return $status;
    }
    
    public static function clearStatusPedido()
    {
        self::$cacheStatus = array();
    }
}
