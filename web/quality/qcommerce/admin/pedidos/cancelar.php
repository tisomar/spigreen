<?php

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';
require_once QCOMMERCE_DIR . '/admin/pedidos/config/routes.php';

$object = PedidoQuery::create()->findOneById($_GET['id']);
$object->cancelar();

$session->getFlashBag()->add('success', 'Pedido cancelado com sucesso!');

redirectTo($config['routes']['list']);
exit;
