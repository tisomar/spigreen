<?php

$object = PedidoPeer::retrieveByPk($_POST['pk']);

if (is_null($object)) {
    echo json_encode(['status' => 'error', 'message' => 'Objeto não encontrado!']);
}

$object->setTransportadoraNome($_POST['value']);

### Salvar o objeto
$response = $object->save();

if($response == 1) {
    $object->avisaClienteComCodigosFrete('setTransportadoraNome');
    echo json_encode(['status' => 'ok']);
}else{
    echo json_encode(['status' => 'error']);
}


