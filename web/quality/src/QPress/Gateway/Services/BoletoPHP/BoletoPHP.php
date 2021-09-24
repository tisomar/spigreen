<?php

namespace QPress\Gateway\Services\BoletoPHP;

use QPress\Gateway\AbstractGateway;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class BoletoPHP extends AbstractGateway implements BoletoPHPInterface
{

    public function getName()
    {
        return 'Boleto Bancário';
    }

    public function getDefaultParameters()
    {
        return array(
            'dias_vencimento'   => \Config::get('boleto.quantidade_dias_vencimento'),
            'banco'             => \Config::get('boletophp.banco'),
            'agencia'           => \Config::get('boletophp.agencia'),
            'conta'             => \Config::get('boletophp.conta'),
            'carteira'          => \Config::get('boletophp.carteira'),
            'convenio'          => \Config::get('boletophp.convenio')
        );
    }

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $id     = \PedidoFormaPagamentoPeer::getHashBoleto($formaPagamento->getId());
        $data   = (object) array(
            'id'         => $id,
            'url_acesso' => get_url_site() . '/boleto/' . $id,
            'status'     => \PedidoFormaPagamentoPeer::STATUS_PENDENTE,
        );

        return new Response($data);
    }
}