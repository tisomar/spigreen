<?php

include('../includes/config.inc.php');
include('../includes/security.inc.php');

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

$id = (is_numeric($_GET['id']) && $_GET['id'] > 0) ? $_GET['id'] : null;
$objeto = filter_var($_GET['objeto'], FILTER_SANITIZE_STRING);

$status = array('status' => 'failed', 'message' => utf8_encode('Não foi possível efetuar a exclusão do arquivo'));

### Valido informaçõees
if (is_null($id) || $objeto == '' || !class_exists($objeto)) {
    echo json_encode($status);
    exit;
}

## Faço a seleção do objeto
$objeto_query = $objeto . 'Query';
$object_to_delete = $objeto_query::create()->findOneById($id);

if (!$object_to_delete instanceof $objeto) {
    echo json_encode($status);
    exit;
}

try {
    $object_to_delete->delete();
    $status['status'] = 'success';
    $status['message'] = utf8_encode('Exclusão realizada com sucesso');
} catch (Exception $exc) {
    $status['message'] = $exc->getTraceAsString();
}

echo json_encode($status);
