<?php

header('Content-type: text/json; chartset=ISO-8859-1');

if (!isset($_GET['model'])) {
    throw new Exception('parâmetro \'model\' não informado!');
} elseif (is_empty($_GET['model']) || !class_exists($_GET['model'])) {
    throw new Exception(sprintf('model %s não encontrado!', $_GET['model']));
}

$model = $_GET['model'];
$peer = $model . 'Peer';

$object = $peer::retrieveByPk($_GET['pk']);

if (is_null($object)) {
    die('Objeto não encontrado!');
}

$response = $object->$_GET['method']();

echo ($response);
