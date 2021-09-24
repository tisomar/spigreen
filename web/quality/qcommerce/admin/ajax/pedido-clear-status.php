<?php
include __DIR__ . '/../includes/config.inc.php';
include __DIR__ . '/../includes/security.inc.php';
header('Content-Type: application/json; charset=utf-8');
$orderId = filter_input(INPUT_GET, 'id');
try {
    $pedido = PedidoQuery::create()->findPk($orderId);
    $service = include __DIR__ . '/pedido-clear-service.php';
    $orderCode = (string) $orderId;
    $send = $service->statusCheck($orderCode);
    $pedido->setSituacaoClearSale($send['status']);
    $pedido->save();
    echo json_encode([
        'success' => true,
        'order' => $orderId,
        'clearSale' => $send,
        'message' => 'Pedido atualizado com sucesso!',
    ]);
} catch (\ClearSale\Service\ServiceResponseException $e) {
    echo json_encode([
        'success' => false,
        'order' => $orderId,
        'message' => 'Erro ao receber status da clear sale: ' . $e->getMessage(),
    ]);
    return;
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'order' => $orderId,
        'message' => 'Erro ao consultar o pedido: ' . $e->getMessage(),
    ]);
    return;
}
