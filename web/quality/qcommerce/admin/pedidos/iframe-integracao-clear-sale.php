<?php

$pageTitle = 'Pedidos';
$_class = PedidoPeer::OM_CLASS;

$pedido = $object = PedidoQuery::create()->findPk($_GET['id']);
if (!$pedido) {
    redirect_404admin();
}

require QCOMMERCE_DIR . '/admin/pedidos/components/iframe-integracao-clear-sale/content.php';
