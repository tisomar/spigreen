<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    return;
}
require_once __DIR__ . '/../quality/vendor/autoload.php';
mb_internal_encoding('UTF-8');
require_once QCOMMERCE_DIR . '/includes/include_propel.inc.php';

$service = include __DIR__ . '/../quality/qcommerce/admin/ajax/pedido-clear-service.php';
$content = json_decode(file_get_contents('php://input'), true);

if (!$content || !isset($content['code'])) {
    http_response_code(400);
    exit('Without code');
}

$orderId = $content['code'];
$pedido = PedidoQuery::create()->findPk($orderId);
$orderCode = (string) $orderId;
$send = $service->statusCheck($orderCode);
$pedido->setSituacaoClearSale($send['status']);
$pedido->save();

echo $orderId;

