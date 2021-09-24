<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 25/04/2018
 * Time: 09:28
 */

if ($container->getRequest()->getMethod() == 'POST') {
    $container->getSession()->remove('fromFranqueado');
    $container->getSession()->remove('slugFranqueado');

    $clientId = $container->getRequest()->request->get('id');

    if ($clientId > 0) {
        $objCliente = ClienteQuery::create()
            ->filterByNaoCompra(0)
            ->filterByTreeLeft(0, Criteria::GREATER_THAN)
            ->filterByPlanoId(null, Criteria::NOT_EQUAL)
            ->groupByChaveIndicacao()
            ->findOneById($clientId);

        if (!is_null($objCliente)) {
            $objHotsite = HotsiteQuery::create()
                ->filterByClienteId($objCliente->getId())
                ->findOne();

            if (!is_null($objHotsite)) {
                $container->getSession()->set('fromFranqueado', true);
                $container->getSession()->set('slugFranqueado', $objHotsite->getSlug());
                $return = array(
                    'retorno'   => 'success',
                    'title'     => 'Confirmado!',
                    'msg'       => 'Revendedor selecionado com sucesso.'
                );
            } else {
                $return = array(
                    'retorno'   => 'error',
                    'title'     => 'Erro na Confirmação',
                    'msg'       => 'Revendedor não possui Hotsite e não pode ser selecionado, tente outro revendedor.'
                );
            }
        } else {
            $return = array(
                'retorno'   => 'error',
                'title'     => 'Erro na Confirmação',
                'msg'       => 'Revendedor não encontrador, tente novamente ou selecione outro revendedor.'
            );
        }
    } else {
        $return = array(
            'retorno'   => 'error',
            'title'     => 'Erro na Confirmação',
            'msg'       => 'Revendedor não encontrador, tente novamente ou selecione outro revendedor.'
        );
    }
} else {
    $return = array(
        'retorno'   => 'error',
        'title'     => 'Erro na Função',
        'msg'       => 'Consulta inválida, entre em contato conosco.'
    );
}

echo json_encode($return);
die;
