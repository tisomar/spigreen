<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/flatly/bootstrap.min.css" rel="stylesheet" integrity="sha384-XYCjB+hFAjSbgf9yKUgbysEjaVLOXhCgATTEBpCqT1R3jvG5LGRAK5ZIyRbH5vpX" crossorigin="anonymous">
<?php

set_time_limit(0);

$pedidos = PedidoQuery::create()
    ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
    ->usePedidoStatusHistoricoQuery()
    ->filterByPedidoStatusId(1)
    ->filterByIsConcluido(1)
    ->endUse()
    ->filterByCupom(null, Criteria::NOT_EQUAL)
    ->orderById()
    ->find();

$sql = "UPDATE qp1_pedido SET VALOR_CUPOM_DESCONTO = :valor WHERE ID = :id";

$con = Propel::getConnection();

/* @var $pedido Pedido */
foreach ($pedidos as $pedido) {
    $valor = $pedido->getValorDescontoBy(CupomPeer::OM_CLASS);
    $id = $pedido->getId();

    $stmt = $con->prepare($sql);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':id', $id);

    echo $id, ' - ', $valor, '<br>';
    $stmt->execute();
}

echo 'Ok';
