<?php

$centroDistribuicaoId = $_POST['centro_distribuicao_id'];
$pedidoId = $_POST['pedido_id'];

$pedidoEstoque = PedidoQuery::create()->filterById($pedidoId)->findOne()->getCentroDistribuicaoEstoqueByEstadoCliente($centroDistribuicaoId);

echo json_encode($pedidoEstoque);