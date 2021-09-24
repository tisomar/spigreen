<?php

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';
require_once QCOMMERCE_DIR . '/admin/pedidos/config/routes.php';

if ($container->getRequest()->getMethod() == 'POST') :
    $object = PedidoQuery::create()->findOneById($_GET['id']);
    try {
        $object->avancaStatus();
    } catch (\Throwable $th) {
        $log = LogErrosQuery::create()->filterByTarget("pedido id: {$object->getId()}", Criteria::EQUAL)->findOneOrCreate();
        $log->setUsuarioId(UsuarioPeer::getUsuarioLogado()->getId());
        $log->setData(new Datetime('now'));
        $log->setUrl('admin/pedidos/list');
        $log->setModulo('pedidos/update-status');
        $log->setDescricao($th->getMessage());
        $log->save();

        $session->getFlashBag()->add('warning', 'Ocorreu um erro ao avançar o pedido, verifique os logs!');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
    }
    $session->getFlashBag()->add('success', 'Status atualizado com sucesso!');
endif;

redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
exit;
