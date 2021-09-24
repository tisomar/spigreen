<?php

namespace QPress\Gateway\Services\ItauShopline;

use QPress\Gateway\AbstractGateway;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ItauShopline extends AbstractGateway implements ItauShoplineInterface
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Boleto Bancário - Itaú Shopline';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'dias_vencimento'   => \Config::get('boleto.quantidade_dias_vencimento')
        );
    }

    /**
     * {@inheritdoc}
     */

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $id     = \PedidoFormaPagamentoPeer::getHashBoleto($formaPagamento->getId());
        $data   = (object) array(
            'id'         => $id,
            'url_acesso' => get_url_site() . '/boleto/itau-shopline/' . $id,
            'status'     => \PedidoFormaPagamentoPeer::STATUS_PENDENTE,
        );

        return new Response($data);
    }
}